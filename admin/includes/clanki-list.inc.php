<?php
declare(strict_types=1);

require_once __DIR__ . '/image-thumb.inc.php';

/**
 * Fetch and render article preview rows using PDO prepared statements.
 *
 * @param PDO $pdo
 * @param array{
 *   category?: string,
 *   excludeCategory?: string,
 *   orderDir?: 'ASC'|'DESC',
 *   limit?: int|null,
 *   basePrefix?: string,
 *   dateMode?: 'full'|'empty'|'none',
 *   showReadMore?: bool
 * } $options
 */
function renderArticleList(PDO $pdo, array $options = []): void
{
    $category = $options['category'] ?? null;
    $excludeCategory = $options['excludeCategory'] ?? null;
    $orderDir = ($options['orderDir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
    $limit = $options['limit'] ?? null;
    $basePrefix = $options['basePrefix'] ?? '';
    $dateMode = $options['dateMode'] ?? 'full';
    $showReadMore = $options['showReadMore'] ?? true;

    $whereClauses = [];
    $params = [];

    if ($category !== null) {
        $whereClauses[] = 'kategorija = :category';
        $params[':category'] = $category;
    }
    if ($excludeCategory !== null) {
        $whereClauses[] = 'kategorija != :excludeCategory';
        $params[':excludeCategory'] = $excludeCategory;
    }

    $whereSql = $whereClauses !== [] ? 'WHERE ' . implode(' AND ', $whereClauses) : '';
    $limitSql = is_int($limit) && $limit > 0 ? 'LIMIT :limit' : '';

    $sql = sprintf(
        'SELECT ID, naslov, kraj, DATE_FORMAT(datum, "%%d. %%m. %%Y") AS lepdatum, preview, naslovnaslika
         FROM clanki
         %s
         ORDER BY datum %s, ID %s
         %s',
        $whereSql,
        $orderDir,
        $orderDir,
        $limitSql
    );

    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val, PDO::PARAM_STR);
    }
    if (is_int($limit) && $limit > 0) {
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    }
    $stmt->execute();

    $rows = $stmt->fetchAll();

    foreach ($rows as $row) {
        $id = (int) $row['ID'];
        $naslovRaw = (string) $row['naslov'];
        $naslov = htmlspecialchars($naslovRaw, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $krajRaw = trim((string) ($row['kraj'] ?? ''));
        $datum = htmlspecialchars((string) $row['lepdatum'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $previewRaw = trim((string) ($row['preview'] ?? ''));
        $previewPlain = trim(
            preg_replace(
                '/\s+/u',
                ' ',
                html_entity_decode(strip_tags($previewRaw), ENT_QUOTES | ENT_HTML5, 'UTF-8')
            ) ?? ''
        );
        $preview = htmlspecialchars($previewPlain, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $coverRaw = trim((string) ($row['naslovnaslika'] ?? ''));

        $articleUrl = htmlspecialchars($basePrefix . 'clanki/' . $id, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        echo '<div class="novica row align-items-start">';
        echo '<div class="col-md-6">';
        echo '<a href="' . $articleUrl . '"><h2>' . $naslov . '</h2></a>';

        if ($dateMode === 'full') {
            $datumText = $krajRaw === ''
                ? $datum
                : htmlspecialchars($krajRaw, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . ', ' . $datum;
            echo '<div class="datum">' . $datumText . '</div>';
        } elseif ($dateMode === 'empty') {
            echo '<div class="datum"></div>';
        }

        if ($showReadMore) {
            if ($preview !== '') {
                echo '<p>' . $preview . ' &nbsp;<a href="' . $articleUrl . '" class="povezava">Preberi več...</a></p>';
            } else {
                echo '<p><a href="' . $articleUrl . '" class="povezava">Preberi več...</a></p>';
            }
        } elseif ($preview !== '') {
            echo '<p>' . $preview . '</p>';
        }

        echo '</div>';
        echo '<div class="col-md-6">';
        if ($coverRaw !== '') {
            $relCoverPath = 'clanki/' . $id . '/' . $coverRaw;
            echo '<a href="' . $articleUrl . '">'
                . renderProgressiveImg($relCoverPath, $naslovRaw, $basePrefix, 960)
                . '</a>';
        }
        echo '</div></div>';
    }
}
