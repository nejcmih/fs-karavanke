<?php
declare(strict_types=1);

/**
 * Validate whether a URL attribute (href / src / data-full) is safe.
 */
function isSafeArticleUrl(string $url, bool $isImageSrc = false): bool
{
    $trimmed = trim($url);
    if ($trimmed === '' || str_contains($trimmed, "\0")) {
        return false;
    }

    // Reject protocol-relative URLs (//example.com or \\example.com)
    if (str_starts_with($trimmed, '//') || str_starts_with($trimmed, '\\\\')) {
        return false;
    }

    // Strip control chars and whitespace before checking protocol
    $normalized = preg_replace('/[\x00-\x20]+/', '', strtolower($trimmed)) ?? '';
    if (
        str_starts_with($normalized, 'javascript:') ||
        str_starts_with($normalized, 'vbscript:') ||
        str_starts_with($normalized, 'data:') ||
        str_starts_with($normalized, 'file:')
    ) {
        return false;
    }

    if ($isImageSrc) {
        // Inline article images may only reference local site media paths
        return (
            str_starts_with($trimmed, '../clanki/') ||
            str_starts_with($trimmed, 'clanki/') ||
            str_starts_with($trimmed, '../uploads/') ||
            str_starts_with($trimmed, 'uploads/') ||
            str_starts_with($trimmed, '../thumb?') ||
            str_starts_with($trimmed, 'thumb?')
        );
    }

    if (
        str_starts_with($trimmed, 'https://') ||
        str_starts_with($trimmed, 'http://') ||
        str_starts_with($trimmed, '../') ||
        str_starts_with($trimmed, './') ||
        str_starts_with($trimmed, '/') ||
        str_starts_with($trimmed, 'mailto:') ||
        str_starts_with($trimmed, 'tel:') ||
        str_starts_with($trimmed, '#')
    ) {
        return true;
    }

    // Allow simple relative filenames/paths without protocol colons
    return !str_contains($trimmed, ':');
}

/**
 * Sanitize rich HTML article content using a strict DOMDocument allowlist.
 * Strips scripts, inline event handlers, foreign inline styles, and editor-only UI controls.
 */
function sanitizeArticleHtml(string $rawHtml): string
{
    $trimmed = trim($rawHtml);
    if ($trimmed === '') {
        return '';
    }

    $prevUseErrors = libxml_use_internal_errors(true);
    $dom = new DOMDocument('1.0', 'UTF-8');
    $wrapped = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body><div id="__agy_sanitize_root">'
        . $trimmed
        . '</div></body></html>';

    $loaded = $dom->loadHTML($wrapped, LIBXML_NOERROR | LIBXML_NOWARNING);
    libxml_clear_errors();
    libxml_use_internal_errors($prevUseErrors);

    if (!$loaded) {
        return htmlspecialchars(strip_tags($trimmed), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    $root = $dom->getElementById('__agy_sanitize_root');
    if (!$root instanceof DOMElement) {
        return htmlspecialchars(strip_tags($trimmed), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    sanitizeDomChildren($dom, $root);

    $output = '';
    foreach ($root->childNodes as $child) {
        $saved = $dom->saveHTML($child);
        if (is_string($saved)) {
            $output .= $saved;
        }
    }

    $output = trim($output);
    // Treat empty placeholder paragraphs (<p><br></p> or <p></p>) with nothing else as empty
    if (preg_replace('#^(?:<p>(?:\s|<br\s*/?>|&nbsp;)*</p>\s*)+$#iu', '', $output) === '') {
        return '';
    }

    return $output;
}

/**
 * Recursively sanitize child nodes of a DOMElement in-place.
 */
function sanitizeDomChildren(DOMDocument $dom, DOMNode $parent): void
{
    $dropEntirelyTags = [
        'script' => true,
        'style' => true,
        'iframe' => true,
        'object' => true,
        'embed' => true,
        'applet' => true,
        'form' => true,
        'input' => true,
        'button' => true,
        'textarea' => true,
        'select' => true,
        'option' => true,
        'svg' => true,
        'math' => true,
        'meta' => true,
        'link' => true,
        'base' => true,
        'head' => true,
        'title' => true,
    ];

    $simpleTags = [
        'p' => true,
        'br' => true,
        'b' => true,
        'strong' => true,
        'i' => true,
        'em' => true,
        'u' => true,
        'h2' => true,
        'h3' => true,
        'h4' => true,
        'ul' => true,
        'ol' => true,
        'li' => true,
        'blockquote' => true,
    ];

    $allowedDivClasses = [
        'gallery' => true,
        'article-inline-gallery' => true,
        'row' => true,
        'row-cols-2' => true,
        'row-cols-sm-3' => true,
        'row-cols-md-4' => true,
        'align-items-start' => true,
        'g-2' => true,
        'g-3' => true,
        'col' => true,
        'col-6' => true,
        'col-sm-4' => true,
        'col-md-3' => true,
        'col-md-4' => true,
        'col-md-6' => true,
    ];

    $allowedFigureClasses = [
        'article-inline-image' => true,
        'size-medium' => true,
        'size-full' => true,
    ];

    // Snapshot child list before mutating DOM
    $children = [];
    foreach ($parent->childNodes as $child) {
        $children[] = $child;
    }

    foreach ($children as $node) {
        if ($node instanceof DOMComment) {
            $parent->removeChild($node);
            continue;
        }

        if (!$node instanceof DOMElement) {
            continue;
        }

        $tag = strtolower($node->tagName);

        // Drop dangerous or editor-UI elements completely
        if (isset($dropEntirelyTags[$tag]) || $node->hasAttribute('data-editor-ui')) {
            $parent->removeChild($node);
            continue;
        }

        // Convert h1 inside article body down to h2 so page keeps a single <h1>
        if ($tag === 'h1') {
            $replacement = $dom->createElement('h2');
            while ($node->firstChild !== null) {
                $replacement->appendChild($node->firstChild);
            }
            $parent->replaceChild($replacement, $node);
            sanitizeDomChildren($dom, $replacement);
            continue;
        }

        if (isset($simpleTags[$tag])) {
            stripAllAttributes($node);
            sanitizeDomChildren($dom, $node);
            continue;
        }

        if ($tag === 'figcaption') {
            stripAllAttributes($node);
            sanitizeDomChildren($dom, $node);
            if (trim($node->textContent) === '') {
                $parent->removeChild($node);
            }
            continue;
        }

        if ($tag === 'figure') {
            $keptClasses = filterAllowedClasses($node->getAttribute('class'), $allowedFigureClasses);
            if (!in_array('article-inline-image', $keptClasses, true)) {
                $keptClasses[] = 'article-inline-image';
            }
            stripAllAttributes($node);
            $node->setAttribute('class', implode(' ', $keptClasses));
            sanitizeDomChildren($dom, $node);
            if ($node->getElementsByTagName('img')->length === 0) {
                $parent->removeChild($node);
            }
            continue;
        }

        if ($tag === 'div') {
            $keptClasses = filterAllowedClasses($node->getAttribute('class'), $allowedDivClasses);
            if ($keptClasses !== []) {
                stripAllAttributes($node);
                $node->setAttribute('class', implode(' ', $keptClasses));
                sanitizeDomChildren($dom, $node);
                if (in_array('gallery', $keptClasses, true) && $node->getElementsByTagName('img')->length === 0) {
                    $parent->removeChild($node);
                }
            } else {
                // Convert generic contenteditable <div> into <p>
                $pNode = $dom->createElement('p');
                while ($node->firstChild !== null) {
                    $pNode->appendChild($node->firstChild);
                }
                $parent->replaceChild($pNode, $node);
                sanitizeDomChildren($dom, $pNode);
            }
            continue;
        }

        if ($tag === 'a') {
            $href = trim($node->getAttribute('href'));
            if (!isSafeArticleUrl($href, false)) {
                unwrapDomElement($parent, $node);
                continue;
            }
            $classList = filterAllowedClasses($node->getAttribute('class'), ['lightbox' => true, 'povezava' => true]);
            $target = $node->getAttribute('target') === '_blank' ? '_blank' : '';
            $dataGallery = preg_replace('/[^a-zA-Z0-9_-]/', '', $node->getAttribute('data-gallery')) ?? '';
            $dataDesc = trim($node->getAttribute('data-description'));

            stripAllAttributes($node);
            $node->setAttribute('href', $href);
            if ($classList !== []) {
                $node->setAttribute('class', implode(' ', $classList));
            }
            if ($target === '_blank' && !in_array('lightbox', $classList, true)) {
                $node->setAttribute('target', '_blank');
                $node->setAttribute('rel', 'noopener noreferrer');
            }
            if ($dataGallery !== '') {
                $node->setAttribute('data-gallery', $dataGallery);
            }
            if ($dataDesc !== '') {
                $node->setAttribute('data-description', mb_substr($dataDesc, 0, 300, 'UTF-8'));
            }
            sanitizeDomChildren($dom, $node);
            continue;
        }

        if ($tag === 'img') {
            $src = trim($node->getAttribute('src'));
            if (!isSafeArticleUrl($src, true)) {
                $parent->removeChild($node);
                continue;
            }
            $dataFull = trim($node->getAttribute('data-full'));
            $alt = trim($node->getAttribute('alt'));

            stripAllAttributes($node);
            $node->setAttribute('src', $src);
            if ($dataFull !== '' && isSafeArticleUrl($dataFull, true) && $dataFull !== $src) {
                $node->setAttribute('data-full', $dataFull);
            }
            $node->setAttribute('alt', mb_substr($alt, 0, 250, 'UTF-8'));
            $node->setAttribute('loading', 'lazy');
            $node->setAttribute('decoding', 'async');
            continue;
        }

        // Unwrap any other non-allowlisted inline/wrapper tag (e.g. <span>, <font>, <section>) while sanitizing children
        sanitizeDomChildren($dom, $node);
        unwrapDomElement($parent, $node);
    }
}

function stripAllAttributes(DOMElement $el): void
{
    $attrNames = [];
    foreach ($el->attributes as $attr) {
        $attrNames[] = $attr->nodeName;
    }
    foreach ($attrNames as $name) {
        $el->removeAttribute($name);
    }
}

/**
 * @param array<string, bool> $allowedMap
 * @return list<string>
 */
function filterAllowedClasses(string $rawClassAttr, array $allowedMap): array
{
    if (trim($rawClassAttr) === '') {
        return [];
    }
    $parts = preg_split('/\s+/', trim($rawClassAttr)) ?: [];
    $kept = [];
    foreach ($parts as $cls) {
        if (isset($allowedMap[$cls]) && !in_array($cls, $kept, true)) {
            $kept[] = $cls;
        }
    }
    return $kept;
}

function unwrapDomElement(DOMNode $parent, DOMElement $el): void
{
    while ($el->firstChild !== null) {
        $parent->insertBefore($el->firstChild, $el);
    }
    $parent->removeChild($el);
}

/**
 * Auto-generate a clean plain-text excerpt from rich HTML content for clanki.preview.
 * Excludes image captions (<figure>) and galleries so only real article text is summarized.
 */
function generateArticleExcerpt(string $html, int $maxLength = 280): string
{
    $trimmed = trim($html);
    if ($trimmed === '') {
        return '';
    }

    $prevUseErrors = libxml_use_internal_errors(true);
    $dom = new DOMDocument('1.0', 'UTF-8');
    $wrapped = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body><div id="__agy_excerpt_root">'
        . $trimmed
        . '</div></body></html>';

    $loaded = $dom->loadHTML($wrapped, LIBXML_NOERROR | LIBXML_NOWARNING);
    libxml_clear_errors();
    libxml_use_internal_errors($prevUseErrors);

    if (!$loaded) {
        $plain = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($trimmed), ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?? '');
    } else {
        $root = $dom->getElementById('__agy_excerpt_root');
        if ($root instanceof DOMElement) {
            // Remove figures, galleries, and subheadings (when body paragraphs exist) before extracting excerpt text
            $xpath = new DOMXPath($dom);
            $mediaNodes = $xpath->query('.//figure | .//figcaption | .//*[contains(concat(" ", normalize-space(@class), " "), " gallery ")]', $root);
            if ($mediaNodes !== false) {
                $toRemove = [];
                foreach ($mediaNodes as $mn) {
                    $toRemove[] = $mn;
                }
                foreach ($toRemove as $mn) {
                    if ($mn->parentNode !== null) {
                        $mn->parentNode->removeChild($mn);
                    }
                }
            }

            // Insert spaces after block elements so adjacent paragraphs don't glue together
            $blockTags = ['p', 'h1', 'h2', 'h3', 'h4', 'li', 'blockquote', 'div', 'br'];
            foreach ($blockTags as $bTag) {
                $elems = $root->getElementsByTagName($bTag);
                $list = [];
                foreach ($elems as $el) {
                    $list[] = $el;
                }
                foreach ($list as $el) {
                    $el->appendChild($dom->createTextNode(' '));
                }
            }

            $cloneWithoutHeadings = $root->cloneNode(true);
            if ($cloneWithoutHeadings instanceof DOMElement) {
                foreach (['h1', 'h2', 'h3', 'h4'] as $hTag) {
                    $hNodes = $cloneWithoutHeadings->getElementsByTagName($hTag);
                    $hList = [];
                    foreach ($hNodes as $hn) {
                        $hList[] = $hn;
                    }
                    foreach ($hList as $hn) {
                        if ($hn->parentNode !== null) {
                            $hn->parentNode->removeChild($hn);
                        }
                    }
                }
                $proseOnly = trim(preg_replace('/\s+/u', ' ', $cloneWithoutHeadings->textContent) ?? '');
            } else {
                $proseOnly = '';
            }

            $plain = $proseOnly !== ''
                ? $proseOnly
                : trim(preg_replace('/\s+/u', ' ', $root->textContent) ?? '');
        } else {
            $plain = '';
        }
    }

    if ($plain === '') {
        return '';
    }

    if (mb_strlen($plain, 'UTF-8') > $maxLength) {
        $slice = mb_substr($plain, 0, $maxLength, 'UTF-8');
        $lastSpace = mb_strrpos($slice, ' ', 0, 'UTF-8');
        if (is_int($lastSpace) && $lastSpace > (int) ($maxLength * 0.6)) {
            $slice = mb_substr($slice, 0, $lastSpace, 'UTF-8');
        }
        $plain = rtrim($slice, " \t\n\r\0\x0B.,;:-–—") . '…';
    }

    return htmlspecialchars($plain, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Normalize stored besedilo HTML for public rendering so both legacy plain/inline articles
 * and new rich block-HTML articles render cleanly inside .article-content.
 */
function formatArticleBodyForDisplay(string $storedHtml): string
{
    $trimmed = trim($storedHtml);
    if ($trimmed === '') {
        return '';
    }

    // Ensure any inline <a href="../clanki/..."><img></a> resolves to a valid high-quality thumbnail
    if (str_contains($trimmed, 'clanki/')) {
        require_once __DIR__ . '/image-thumb.inc.php';
        $trimmed = preg_replace_callback(
            '#(<a\b[^>]*\bhref="\.\./(clanki/[^"]+)"[^>]*>\s*<img\b[^>]*?\bsrc=")([^"]+)("[^>]*>)#i',
            static function (array $m): string {
                $relPath = rawurldecode($m[2]);
                $freshThumb = htmlspecialchars(getThumbUrl($relPath, 960, '../'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                $fullAttr = htmlspecialchars('../' . $m[2], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                $tail = $m[4];
                if (!str_contains($tail, 'data-full=')) {
                    $tail = '" data-full="' . $fullAttr . '"' . substr($tail, 1);
                }
                return $m[1] . $freshThumb . $tail;
            },
            $trimmed
        ) ?? $trimmed;
    }

    if (preg_match('/^\s*<(?:p|h[1-6]|ul|ol|blockquote|figure|div)\b/i', $trimmed) !== 1) {
        $trimmed = '<p>' . $trimmed . '</p>';
    }

    return sanitizeArticleHtml($trimmed);
}
