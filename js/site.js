(function () {
    'use strict';

    // 1. Dynamic copyright year in footer
    var yearEl = document.getElementById('leto');
    if (yearEl) {
        yearEl.textContent = String(new Date().getFullYear());
    }

    // 2. Group galleries & initialize GLightbox
    function initGalleries() {
        var galleries = document.querySelectorAll('.gallery');
        galleries.forEach(function (galleryEl, gIdx) {
            var groupName = galleryEl.getAttribute('data-gallery-group') || ('gallery-' + gIdx);
            var links = galleryEl.querySelectorAll('a.lightbox');
            links.forEach(function (link) {
                if (!link.getAttribute('data-gallery')) {
                    link.setAttribute('data-gallery', groupName);
                }
            });
        });

        var inlineFigures = document.querySelectorAll('.article-inline-image');
        inlineFigures.forEach(function (fig, fIdx) {
            var link = fig.querySelector('a.lightbox');
            if (!link) {
                return;
            }
            if (!link.getAttribute('data-gallery')) {
                link.setAttribute('data-gallery', 'inline-img-' + fIdx);
            }
            var caption = fig.querySelector('figcaption');
            if (caption && caption.textContent.trim() !== '' && !link.getAttribute('data-description')) {
                link.setAttribute('data-description', caption.textContent.trim());
            }
        });

        if (typeof window.GLightbox === 'function' && document.querySelector('a.lightbox')) {
            window.GLightbox({
                selector: 'a.lightbox',
                touchNavigation: true,
                loop: true,
                zoomable: true,
                draggable: true,
                openEffect: 'zoom',
                closeEffect: 'fade'
            });
        }
    }

    // 3. Background full-resolution image upgrader & idle gallery cache warmer
    function initBackgroundImageLoader() {
        var conn = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
        if (conn && conn.saveData) {
            return;
        }

        var scheduleIdle = window.requestIdleCallback
            ? function (cb) { window.requestIdleCallback(cb, { timeout: 2000 }); }
            : function (cb) { window.setTimeout(cb, 200); };

        var highQueue = [];
        var lowQueue = [];
        var queuedUrls = new Set();
        var activeLoads = 0;
        var maxConcurrent = 2;

        function pumpQueue() {
            if (activeLoads >= maxConcurrent) {
                return;
            }
            var task = highQueue.shift() || lowQueue.shift();
            if (!task) {
                return;
            }

            activeLoads++;
            scheduleIdle(function () {
                var preloader = new Image();
                preloader.decoding = 'async';

                var finish = function () {
                    activeLoads--;
                    pumpQueue();
                };

                preloader.onload = function () {
                    var applySwap = function () {
                        if (typeof task.onReady === 'function') {
                            task.onReady();
                        }
                        finish();
                    };

                    if (typeof preloader.decode === 'function') {
                        preloader.decode().then(applySwap).catch(applySwap);
                    } else {
                        applySwap();
                    }
                };

                preloader.onerror = finish;
                preloader.src = task.url;
            });
        }

        function enqueueUpgrade(imgEl) {
            var fullUrl = imgEl.getAttribute('data-full');
            if (!fullUrl || imgEl.dataset.upgraded === '1') {
                return;
            }
            imgEl.dataset.upgraded = '1';

            highQueue.push({
                url: fullUrl,
                onReady: function () {
                    imgEl.src = fullUrl;
                    imgEl.removeAttribute('data-full');
                    imgEl.classList.add('is-full-loaded');
                }
            });
            pumpQueue();
        }

        function enqueueWarmCache(url, urgent) {
            if (!url || queuedUrls.has(url)) {
                return;
            }
            queuedUrls.add(url);
            var item = { url: url, onReady: null };
            if (urgent) {
                highQueue.push(item);
            } else {
                lowQueue.push(item);
            }
            pumpQueue();
        }

        // Upgrade standalone / cover / carousel [data-full] images when near viewport
        var progressiveImgs = document.querySelectorAll('img[data-full]');
        if ('IntersectionObserver' in window) {
            var imgObserver = new IntersectionObserver(function (entries, obs) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        obs.unobserve(entry.target);
                        enqueueUpgrade(entry.target);
                    }
                });
            }, { rootMargin: '350px 0px' });

            progressiveImgs.forEach(function (img) {
                imgObserver.observe(img);
            });

            // Warm lightbox originals in background when a gallery enters near viewport
            var galleryObserver = new IntersectionObserver(function (entries, obs) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) {
                        return;
                    }
                    obs.unobserve(entry.target);
                    var links = entry.target.querySelectorAll('a.lightbox[href]');
                    links.forEach(function (link, idx) {
                        if (idx < 12) {
                            enqueueWarmCache(link.href, false);
                        }
                    });
                });
            }, { rootMargin: '250px 0px' });

            document.querySelectorAll('.gallery').forEach(function (gal) {
                galleryObserver.observe(gal);
            });
        } else {
            progressiveImgs.forEach(enqueueUpgrade);
        }

        // Immediate hover/touch boost for any lightbox link
        document.addEventListener('pointerenter', function (e) {
            var link = e.target && e.target.closest ? e.target.closest('a.lightbox[href]') : null;
            if (link && link.href) {
                enqueueWarmCache(link.href, true);
            }
        }, true);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initGalleries);
    } else {
        initGalleries();
    }

    if (document.readyState === 'complete') {
        window.setTimeout(initBackgroundImageLoader, 150);
    } else {
        window.addEventListener('load', function () {
            window.setTimeout(initBackgroundImageLoader, 150);
        });
    }
})();

