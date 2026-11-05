(function () {
    'use strict';

    var editor = document.getElementById('richEditor');
    var hiddenTextarea = document.getElementById('besediloInput');
    var form = document.getElementById('articleEditorForm');

    // Cover image preview (works even if rich editor is absent)
    var coverInput = document.getElementById('coverFileInput');
    var coverPreviewWrap = document.getElementById('coverPreviewWrap');
    var coverPreviewImg = document.getElementById('coverPreviewImg');
    var removeCoverCheck = document.getElementById('removeCoverCheck');

    if (coverInput && coverPreviewImg && coverPreviewWrap) {
        coverInput.addEventListener('change', function () {
            var file = coverInput.files && coverInput.files[0];
            if (!file) {
                return;
            }
            var reader = new FileReader();
            reader.onload = function (ev) {
                coverPreviewImg.src = String(ev.target.result || '');
                coverPreviewWrap.classList.remove('d-none');
                if (removeCoverCheck) {
                    removeCoverCheck.checked = false;
                }
            };
            reader.readAsDataURL(file);
        });
    }

    if (!editor || !hiddenTextarea || !form) {
        return;
    }

    try {
        document.execCommand('defaultParagraphSeparator', false, 'p');
    } catch (e) {
        // Ignore if unsupported
    }

    var csrfToken = form.getAttribute('data-csrf') || '';
    var articleId = form.getAttribute('data-article-id') || '';
    var draftToken = form.getAttribute('data-draft-token') || '';
    var statusBanner = document.getElementById('editorUploadStatus');
    var singleImgInput = document.getElementById('inlineSingleImageInput');
    var galleryImgInput = document.getElementById('inlineGalleryInput');
    var toolbar = document.getElementById('richEditorToolbar');
    var appendTargetGalleryRow = null;
    var savedRange = null;
    var statusTimeout = null;

    function showStatus(msg, isError, isSuccess) {
        if (!statusBanner) {
            return;
        }
        if (statusTimeout) {
            clearTimeout(statusTimeout);
            statusTimeout = null;
        }
        if (!msg) {
            statusBanner.className = 'd-none';
            statusBanner.innerHTML = '';
            return;
        }
        var alertClass = isError ? 'alert-danger' : (isSuccess ? 'alert-success' : 'alert-info');
        var iconClass = isError
            ? 'fa-circle-exclamation'
            : (isSuccess ? 'fa-circle-check' : 'fa-circle-info');
        statusBanner.className = 'alert py-2 px-3 mb-2 small d-flex align-items-center gap-2 ' + alertClass;
        statusBanner.innerHTML = '<i class="fa-solid ' + iconClass + '"></i><span></span>';
        statusBanner.querySelector('span').textContent = msg;

        if (isSuccess) {
            statusTimeout = setTimeout(function () {
                showStatus('', false, false);
            }, 3200);
        }
    }

    function showUploadProgress(label, percent, indeterminate) {
        if (!statusBanner) {
            return;
        }
        if (statusTimeout) {
            clearTimeout(statusTimeout);
            statusTimeout = null;
        }
        var pct = Math.max(0, Math.min(100, Math.round(percent || 0)));
        var pctText = indeterminate ? 'Obdelujem predoglede…' : (pct + '%');
        var barWidth = indeterminate ? 100 : Math.max(6, pct);

        statusBanner.className = 'editor-upload-progress-card';
        statusBanner.innerHTML =
            '<div class="d-flex justify-content-between align-items-center mb-1 small fw-semibold">' +
                '<span class="d-flex align-items-center gap-2">' +
                    '<span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></span>' +
                    '<span class="upload-progress-label"></span>' +
                '</span>' +
                '<span class="upload-progress-pct">' + pctText + '</span>' +
            '</div>' +
            '<div class="progress" role="progressbar" aria-valuenow="' + pct + '" aria-valuemin="0" aria-valuemax="100">' +
                '<div class="progress-bar progress-bar-striped progress-bar-animated" style="width: ' + barWidth + '%"></div>' +
            '</div>';
        var lblNode = statusBanner.querySelector('.upload-progress-label');
        if (lblNode) {
            lblNode.textContent = label;
        }
    }

    function hasAncestorTag(node, tagNames) {
        var cur = node && node.nodeType === 3 ? node.parentNode : node;
        while (cur && cur !== editor) {
            if (cur.nodeType === 1 && tagNames.indexOf(cur.tagName.toUpperCase()) !== -1) {
                return true;
            }
            cur = cur.parentNode;
        }
        return false;
    }

    function getActiveBlockTag(node) {
        var cur = node && node.nodeType === 3 ? node.parentNode : node;
        while (cur && cur !== editor) {
            if (cur.nodeType === 1) {
                var tag = cur.tagName.toUpperCase();
                if (tag === 'H2' || tag === 'H3' || tag === 'BLOCKQUOTE' || tag === 'P') {
                    return tag;
                }
                if (tag === 'UL' || tag === 'OL') {
                    return tag;
                }
            }
            cur = cur.parentNode;
        }
        return 'P';
    }

    function updateToolbarState() {
        if (!toolbar) {
            return;
        }

        var sel = window.getSelection();
        var inEditor = false;
        var anchorNode = null;

        if (sel && sel.rangeCount > 0) {
            anchorNode = sel.anchorNode;
            if (anchorNode && editor.contains(anchorNode)) {
                inEditor = true;
            }
        }
        if (!inEditor && savedRange && editor.contains(savedRange.commonAncestorContainer)) {
            inEditor = true;
            anchorNode = savedRange.startContainer;
        }

        var cmdMap = {
            bold: ['B', 'STRONG'],
            italic: ['I', 'EM'],
            underline: ['U'],
            insertUnorderedList: ['UL'],
            insertOrderedList: ['OL']
        };

        toolbar.querySelectorAll('button[data-cmd]').forEach(function (btn) {
            var cmd = btn.getAttribute('data-cmd');
            if (!cmd || cmd === 'removeFormat') {
                return;
            }
            var isActive = false;
            if (inEditor) {
                try {
                    isActive = Boolean(document.queryCommandState(cmd));
                } catch (e) {
                    isActive = false;
                }
                if (!isActive && anchorNode && cmdMap[cmd]) {
                    isActive = hasAncestorTag(anchorNode, cmdMap[cmd]);
                }
            }
            btn.classList.toggle('active', isActive);
            btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });

        var currentBlock = inEditor && anchorNode ? getActiveBlockTag(anchorNode) : '';
        toolbar.querySelectorAll('button[data-block]').forEach(function (btn) {
            var blockTag = (btn.getAttribute('data-block') || '').toUpperCase();
            var isBlockActive = inEditor && currentBlock === blockTag;
            btn.classList.toggle('active', isBlockActive);
            btn.setAttribute('aria-pressed', isBlockActive ? 'true' : 'false');
        });

        var linkBtn = toolbar.querySelector('button[data-action="link"]');
        if (linkBtn) {
            var inLink = inEditor && anchorNode ? hasAncestorTag(anchorNode, ['A']) : false;
            linkBtn.classList.toggle('active', inLink);
            linkBtn.setAttribute('aria-pressed', inLink ? 'true' : 'false');
        }
    }

    function saveSelection() {
        var sel = window.getSelection();
        if (!sel || sel.rangeCount === 0) {
            return;
        }
        var range = sel.getRangeAt(0);
        if (editor.contains(range.commonAncestorContainer)) {
            savedRange = range.cloneRange();
        }
        updateToolbarState();
    }

    function restoreSelection() {
        editor.focus();
        if (!savedRange) {
            return;
        }
        var sel = window.getSelection();
        if (sel) {
            sel.removeAllRanges();
            sel.addRange(savedRange);
        }
    }

    ['keyup', 'mouseup', 'touchend', 'click', 'focus', 'input'].forEach(function (evName) {
        editor.addEventListener(evName, saveSelection);
    });

    document.addEventListener('selectionchange', function () {
        if (document.activeElement === editor) {
            saveSelection();
        }
    });

    // Decorate existing inline figures & galleries loaded in Edit mode with editor UI controls
    function hydrateEditorMediaBlocks() {
        var figures = editor.querySelectorAll('figure.article-inline-image');
        figures.forEach(function (fig) {
            fig.setAttribute('contenteditable', 'false');
            if (!fig.querySelector('.editor-media-toolbar')) {
                var isMed = fig.classList.contains('size-medium');
                var bar = document.createElement('div');
                bar.className = 'editor-media-toolbar';
                bar.setAttribute('data-editor-ui', '1');
                bar.innerHTML =
                    '<button type="button" data-size-toggle="full" class="' + (!isMed ? 'active' : '') + '" title="Polna širina">100%</button>' +
                    '<button type="button" data-size-toggle="medium" class="' + (isMed ? 'active' : '') + '" title="Srednja širina">70%</button>' +
                    '<button type="button" data-media-delete="1" class="text-danger" title="Odstrani sliko"><i class="fa-solid fa-trash-can"></i></button>';
                fig.insertBefore(bar, fig.firstChild);
            }
            var cap = fig.querySelector('figcaption');
            if (!cap) {
                cap = document.createElement('figcaption');
                fig.appendChild(cap);
            }
            cap.setAttribute('contenteditable', 'true');
            cap.setAttribute('data-placeholder', 'Dodaj napis pod sliko (neobvezno)…');
        });

        var galleries = editor.querySelectorAll('.gallery');
        galleries.forEach(function (gal) {
            gal.classList.add('article-inline-gallery');
            gal.setAttribute('contenteditable', 'false');
            if (!gal.querySelector('.editor-media-toolbar')) {
                var bar = document.createElement('div');
                bar.className = 'editor-media-toolbar';
                bar.setAttribute('data-editor-ui', '1');
                bar.innerHTML =
                    '<span class="me-auto small fw-semibold"><i class="fa-regular fa-images me-1"></i>Galerija v besedilu</span>' +
                    '<button type="button" data-gallery-add="1" title="Dodaj slike v to galerijo"><i class="fa-solid fa-plus me-1"></i>Dodaj slike</button>' +
                    '<button type="button" data-media-delete="1" class="text-danger" title="Odstrani galerijo"><i class="fa-solid fa-trash-can"></i></button>';
                gal.insertBefore(bar, gal.firstChild);
            }
            gal.querySelectorAll('[data-col-delete]').forEach(function (btn) {
                var parent = btn.parentElement;
                if (!parent || parent.tagName !== 'DIV' || parent.classList.contains('row')) {
                    btn.remove();
                }
            });
            gal.querySelectorAll('.row > div').forEach(function (col) {
                if (!col.querySelector('[data-col-delete]')) {
                    var delBtn = document.createElement('button');
                    delBtn.type = 'button';
                    delBtn.className = 'editor-photo-remove';
                    delBtn.setAttribute('data-col-delete', '1');
                    delBtn.setAttribute('data-editor-ui', '1');
                    delBtn.title = 'Odstrani to sliko';
                    delBtn.innerHTML = '&times;';
                    col.appendChild(delBtn);
                }
            });
        });

        // Prevent clicking lightbox <a> from navigating away while editing
        editor.querySelectorAll('a.lightbox').forEach(function (a) {
            a.addEventListener('click', function (e) {
                e.preventDefault();
            });
        });
    }

    // Serialize clean semantic HTML into hidden textarea (stripping editor-only controls)
    function syncToTextarea() {
        var clone = editor.cloneNode(true);
        clone.querySelectorAll('[data-editor-ui]').forEach(function (el) {
            el.remove();
        });
        clone.querySelectorAll('[contenteditable]').forEach(function (el) {
            el.removeAttribute('contenteditable');
        });
        clone.querySelectorAll('[data-placeholder]').forEach(function (el) {
            el.removeAttribute('data-placeholder');
        });
        clone.querySelectorAll('figcaption').forEach(function (cap) {
            if (cap.textContent.trim() === '') {
                cap.remove();
            }
        });
        hiddenTextarea.value = clone.innerHTML.trim();
    }

    // Find top-level child of #richEditor at current cursor so blocks never nest inside <p>
    function insertBlockAtCursor(blockNode) {
        restoreSelection();
        var sel = window.getSelection();
        var topRef = null;

        if (sel && sel.rangeCount > 0) {
            var node = sel.getRangeAt(0).commonAncestorContainer;
            while (node && node !== editor && node.parentNode !== editor) {
                node = node.parentNode;
            }
            if (node && node.parentNode === editor) {
                topRef = node;
            }
        }

        var trailingP = document.createElement('p');
        trailingP.innerHTML = '<br>';

        if (topRef) {
            if (topRef.tagName === 'P' && topRef.textContent.trim() === '' && !topRef.querySelector('img')) {
                editor.replaceChild(blockNode, topRef);
            } else {
                editor.insertBefore(blockNode, topRef.nextSibling);
            }
            editor.insertBefore(trailingP, blockNode.nextSibling);
        } else {
            editor.appendChild(blockNode);
            editor.appendChild(trailingP);
        }

        hydrateEditorMediaBlocks();

        var newRange = document.createRange();
        newRange.setStart(trailingP, 0);
        newRange.collapse(true);
        if (sel) {
            sel.removeAllRanges();
            sel.addRange(newRange);
        }
        savedRange = newRange.cloneRange();
        syncToTextarea();
    }

    function createGalleryColElement(item) {
        var col = document.createElement('div');
        col.className = 'col';

        var link = document.createElement('a');
        link.className = 'lightbox';
        link.href = item.fullUrl;
        link.addEventListener('click', function (e) { e.preventDefault(); });

        var img = document.createElement('img');
        img.src = item.thumb640Url;
        if (item.thumb640Url !== item.fullUrl) {
            img.setAttribute('data-full', item.fullUrl);
        }
        img.alt = '';
        img.loading = 'lazy';
        img.decoding = 'async';

        link.appendChild(img);
        col.appendChild(link);
        return col;
    }

    function setMediaToolbarButtonsLoading(isLoading, activeAction) {
        if (!toolbar) {
            return;
        }
        toolbar.querySelectorAll('button[data-action="insert-image"], button[data-action="insert-gallery"]').forEach(function (btn) {
            var act = btn.getAttribute('data-action');
            if (isLoading) {
                if (!btn.hasAttribute('data-orig-html')) {
                    btn.setAttribute('data-orig-html', btn.innerHTML);
                }
                btn.disabled = true;
                if (act === activeAction) {
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Nalagam…';
                }
            } else {
                btn.disabled = false;
                if (btn.hasAttribute('data-orig-html')) {
                    btn.innerHTML = btn.getAttribute('data-orig-html');
                    btn.removeAttribute('data-orig-html');
                }
            }
        });
    }

    function createInlineUploadPlaceholder(labelText) {
        var box = document.createElement('div');
        box.className = 'editor-upload-placeholder';
        box.setAttribute('data-editor-ui', '1');
        box.setAttribute('contenteditable', 'false');
        box.innerHTML =
            '<div class="d-flex align-items-center gap-2">' +
                '<span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></span>' +
                '<span class="placeholder-label"></span>' +
                '<span class="placeholder-pct fw-bold">0%</span>' +
            '</div>' +
            '<div class="progress">' +
                '<div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 8%"></div>' +
            '</div>';
        var lbl = box.querySelector('.placeholder-label');
        if (lbl) {
            lbl.textContent = labelText;
        }
        return box;
    }

    function updatePlaceholderProgress(placeholder, pct, indeterminate) {
        if (!placeholder) {
            return;
        }
        var pctEl = placeholder.querySelector('.placeholder-pct');
        var barEl = placeholder.querySelector('.progress-bar');
        var clamped = Math.max(0, Math.min(100, Math.round(pct || 0)));
        if (pctEl) {
            pctEl.textContent = indeterminate ? 'Obdelujem…' : (clamped + '%');
        }
        if (barEl) {
            barEl.style.width = (indeterminate ? 100 : Math.max(8, clamped)) + '%';
        }
    }

    function uploadMediaFiles(fileList, mode) {
        if (!fileList || fileList.length === 0) {
            return;
        }

        var count = fileList.length;
        var formData = new FormData();
        formData.append('csrf_token', csrfToken);
        if (articleId) {
            formData.append('article_id', articleId);
        } else {
            formData.append('draft_token', draftToken);
        }

        for (var i = 0; i < count; i++) {
            formData.append('files[]', fileList[i]);
        }

        var isGalleryMode = (mode === 'gallery' || mode === 'append-gallery' || count > 1);
        var labelText = isGalleryMode
            ? ('Nalagam galerijo (' + count + ' ' + (count === 1 ? 'slika' : (count === 2 ? 'sliki' : (count <= 4 ? 'slike' : 'slik'))) + ')…')
            : 'Nalagam sliko v članek…';

        setMediaToolbarButtonsLoading(true, isGalleryMode ? 'insert-gallery' : 'insert-image');
        showUploadProgress(labelText, 0, false);

        var placeholder = null;
        if (mode !== 'append-gallery') {
            placeholder = createInlineUploadPlaceholder(labelText);
            insertBlockAtCursor(placeholder);
        }

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'includes/upload-media.inc.php', true);
        xhr.withCredentials = true;

        xhr.upload.addEventListener('progress', function (ev) {
            if (ev.lengthComputable && ev.total > 0) {
                var pct = Math.round((ev.loaded / ev.total) * 100);
                var processing = pct >= 100;
                showUploadProgress(labelText, pct, processing);
                updatePlaceholderProgress(placeholder, pct, processing);
            }
        });

        xhr.addEventListener('load', function () {
            setMediaToolbarButtonsLoading(false, '');
            var data = null;
            try {
                data = JSON.parse(xhr.responseText);
            } catch (err) {
                data = null;
            }

            if (!data || !data.ok || !Array.isArray(data.items) || data.items.length === 0) {
                if (placeholder && placeholder.parentNode) {
                    placeholder.remove();
                }
                appendTargetGalleryRow = null;
                showStatus((data && data.error) ? data.error : 'Napaka pri nalaganju slik.', true, false);
                syncToTextarea();
                return;
            }

            if (mode === 'append-gallery' && appendTargetGalleryRow) {
                data.items.forEach(function (item) {
                    appendTargetGalleryRow.appendChild(createGalleryColElement(item));
                });
                appendTargetGalleryRow = null;
                hydrateEditorMediaBlocks();
                syncToTextarea();
                showStatus('Slike so bile dodane v galerijo.', false, true);
                return;
            }

            var newBlock = null;
            if (mode === 'single' && data.items.length === 1) {
                var item = data.items[0];
                var fig = document.createElement('figure');
                fig.className = 'article-inline-image';

                var link = document.createElement('a');
                link.className = 'lightbox';
                link.href = item.fullUrl;

                var img = document.createElement('img');
                img.src = item.thumb960Url;
                if (item.thumb960Url !== item.fullUrl) {
                    img.setAttribute('data-full', item.fullUrl);
                }
                img.alt = '';
                img.loading = 'lazy';
                img.decoding = 'async';

                var cap = document.createElement('figcaption');

                link.appendChild(img);
                fig.appendChild(link);
                fig.appendChild(cap);
                newBlock = fig;
            } else {
                var gal = document.createElement('div');
                gal.className = 'gallery article-inline-gallery';

                var row = document.createElement('div');
                row.className = 'row row-cols-2 row-cols-sm-3 row-cols-md-4 g-2 align-items-start';

                data.items.forEach(function (item) {
                    row.appendChild(createGalleryColElement(item));
                });

                gal.appendChild(row);
                newBlock = gal;
            }

            if (placeholder && placeholder.parentNode) {
                placeholder.parentNode.replaceChild(newBlock, placeholder);
                hydrateEditorMediaBlocks();
                syncToTextarea();
            } else {
                insertBlockAtCursor(newBlock);
            }

            showStatus(
                data.items.length === 1 ? 'Slika je bila uspešno vstavljena.' : ('Galerija (' + data.items.length + ' slik) je bila uspešno vstavljena.'),
                false,
                true
            );
        });

        xhr.addEventListener('error', function () {
            setMediaToolbarButtonsLoading(false, '');
            if (placeholder && placeholder.parentNode) {
                placeholder.remove();
            }
            appendTargetGalleryRow = null;
            showStatus('Povezava s strežnikom ni uspela. Poskusi znova.', true, false);
            syncToTextarea();
        });

        xhr.send(formData);
    }

    // Toolbar button actions
    if (toolbar) {
        toolbar.addEventListener('mousedown', function (e) {
            // Preserve selection inside editor when clicking toolbar buttons
            if (e.target.closest('button')) {
                e.preventDefault();
            }
        });

        toolbar.addEventListener('click', function (e) {
            var btn = e.target.closest('button');
            if (!btn || btn.disabled) {
                return;
            }

            var cmd = btn.getAttribute('data-cmd');
            var block = btn.getAttribute('data-block');
            var action = btn.getAttribute('data-action');

            if (cmd) {
                restoreSelection();
                document.execCommand(cmd, false, null);
                saveSelection();
                updateToolbarState();
                syncToTextarea();
                return;
            }

            if (block) {
                restoreSelection();
                var activeBlock = savedRange ? getActiveBlockTag(savedRange.startContainer) : String(document.queryCommandValue('formatBlock') || '').toUpperCase();
                var targetTag = (activeBlock === block.toUpperCase() && block.toUpperCase() !== 'P')
                    ? 'P'
                    : block.toUpperCase();
                document.execCommand('formatBlock', false, '<' + targetTag + '>');
                saveSelection();
                updateToolbarState();
                syncToTextarea();
                return;
            }

            if (action === 'link') {
                restoreSelection();
                var rawUrl = window.prompt('Vnesi spletno povezavo (URL):', 'https://');
                if (!rawUrl || rawUrl.trim() === '' || rawUrl.trim() === 'https://') {
                    return;
                }
                var url = rawUrl.trim();
                if (!/^(https?:\/\/|mailto:|tel:|\/|\.\.?\/|#)/i.test(url)) {
                    url = 'https://' + url;
                }
                document.execCommand('createLink', false, url);
                editor.querySelectorAll('a:not(.lightbox)').forEach(function (a) {
                    var href = a.getAttribute('href') || '';
                    if (/^https?:\/\//i.test(href)) {
                        a.setAttribute('target', '_blank');
                        a.setAttribute('rel', 'noopener noreferrer');
                    }
                });
                saveSelection();
                updateToolbarState();
                syncToTextarea();
                return;
            }

            if (action === 'unlink') {
                restoreSelection();
                document.execCommand('unlink', false, null);
                saveSelection();
                updateToolbarState();
                syncToTextarea();
                return;
            }

            if (action === 'insert-image' && singleImgInput) {
                saveSelection();
                singleImgInput.value = '';
                singleImgInput.click();
                return;
            }

            if (action === 'insert-gallery' && galleryImgInput) {
                saveSelection();
                appendTargetGalleryRow = null;
                galleryImgInput.value = '';
                galleryImgInput.click();
            }
        });
    }

    if (singleImgInput) {
        singleImgInput.addEventListener('change', function () {
            if (singleImgInput.files && singleImgInput.files.length > 0) {
                uploadMediaFiles(singleImgInput.files, 'single');
            }
        });
    }

    if (galleryImgInput) {
        galleryImgInput.addEventListener('change', function () {
            if (galleryImgInput.files && galleryImgInput.files.length > 0) {
                var mode = appendTargetGalleryRow ? 'append-gallery' : (galleryImgInput.files.length === 1 ? 'single' : 'gallery');
                uploadMediaFiles(galleryImgInput.files, mode);
            }
        });
    }

    // Delegated clicks for inline media controls (resize, remove block, remove single gallery photo, add to gallery)
    editor.addEventListener('click', function (e) {
        var sizeBtn = e.target.closest('[data-size-toggle]');
        if (sizeBtn) {
            e.preventDefault();
            var fig = sizeBtn.closest('figure.article-inline-image');
            if (fig) {
                var size = sizeBtn.getAttribute('data-size-toggle');
                fig.classList.toggle('size-medium', size === 'medium');
                fig.querySelectorAll('[data-size-toggle]').forEach(function (b) {
                    b.classList.toggle('active', b === sizeBtn);
                });
                syncToTextarea();
            }
            return;
        }

        var delBlockBtn = e.target.closest('[data-media-delete]');
        if (delBlockBtn) {
            e.preventDefault();
            var blockEl = delBlockBtn.closest('figure.article-inline-image, .gallery');
            if (blockEl) {
                blockEl.remove();
                if (editor.innerHTML.trim() === '') {
                    editor.innerHTML = '<p><br></p>';
                }
                syncToTextarea();
            }
            return;
        }

        var delColBtn = e.target.closest('[data-col-delete]');
        if (delColBtn) {
            e.preventDefault();
            var colEl = delColBtn.closest('.row > div');
            var galEl = delColBtn.closest('.gallery');
            if (colEl) {
                colEl.remove();
            }
            if (galEl && galEl.querySelectorAll('img').length === 0) {
                galEl.remove();
            }
            syncToTextarea();
            return;
        }

        var addGalBtn = e.target.closest('[data-gallery-add]');
        if (addGalBtn && galleryImgInput) {
            e.preventDefault();
            var parentGal = addGalBtn.closest('.gallery');
            var rowEl = parentGal ? parentGal.querySelector('.row') : null;
            if (rowEl) {
                appendTargetGalleryRow = rowEl;
                galleryImgInput.value = '';
                galleryImgInput.click();
            }
        }
    });

    // Drag & drop image files directly into the rich text editor
    editor.addEventListener('dragover', function (e) {
        if (e.dataTransfer && e.dataTransfer.types && Array.prototype.indexOf.call(e.dataTransfer.types, 'Files') !== -1) {
            e.preventDefault();
            editor.classList.add('is-dragover');
        }
    });

    editor.addEventListener('dragleave', function () {
        editor.classList.remove('is-dragover');
    });

    editor.addEventListener('drop', function (e) {
        editor.classList.remove('is-dragover');
        if (!e.dataTransfer || !e.dataTransfer.files || e.dataTransfer.files.length === 0) {
            return;
        }
        e.preventDefault();
        var imageFiles = Array.prototype.filter.call(e.dataTransfer.files, function (f) {
            return /^image\/(jpeg|png|webp)$/i.test(f.type);
        });
        if (imageFiles.length === 0) {
            showStatus('Dovoljeni formati slik so JPG, PNG in WebP.', true);
            return;
        }
        uploadMediaFiles(imageFiles, imageFiles.length === 1 ? 'single' : 'gallery');
    });

    // Clean paste handler (strips Word/Google Docs inline font styles while preserving structure)
    editor.addEventListener('paste', function (e) {
        var clipboard = e.clipboardData;
        if (!clipboard) {
            return;
        }

        var htmlData = clipboard.getData('text/html');
        var textData = clipboard.getData('text/plain');

        e.preventDefault();

        if (htmlData && htmlData.trim() !== '') {
            var parser = new DOMParser();
            var doc = parser.parseFromString(htmlData, 'text/html');
            doc.querySelectorAll('script, style, meta, link, iframe, object').forEach(function (n) { n.remove(); });
            doc.body.querySelectorAll('*').forEach(function (el) {
                var keepHref = el.tagName === 'A' ? el.getAttribute('href') : null;
                Array.prototype.slice.call(el.attributes).forEach(function (attr) {
                    el.removeAttribute(attr.name);
                });
                if (keepHref && /^(https?:\/\/|mailto:|tel:)/i.test(keepHref)) {
                    el.setAttribute('href', keepHref);
                    el.setAttribute('target', '_blank');
                    el.setAttribute('rel', 'noopener noreferrer');
                }
            });
            document.execCommand('insertHTML', false, doc.body.innerHTML);
        } else if (textData) {
            var escaped = textData
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
            var paragraphs = escaped.split(/\r?\n\r?\n/).map(function (block) {
                return '<p>' + block.replace(/\r?\n/g, '<br>') + '</p>';
            }).join('');
            document.execCommand('insertHTML', false, paragraphs);
        }

        syncToTextarea();
    });

    editor.addEventListener('input', syncToTextarea);

    form.addEventListener('submit', function () {
        syncToTextarea();
        var submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Shranjujem in obdelujem slike…';
        }
    });

    // Initialize existing content on page load
    if (editor.innerHTML.trim() === '') {
        editor.innerHTML = '<p><br></p>';
    }
    hydrateEditorMediaBlocks();
    updateToolbarState();
    syncToTextarea();
})();

