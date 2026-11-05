<?php
declare(strict_types=1);

$footerBasePrefix = isset($basePrefix) && is_string($basePrefix) ? $basePrefix : '';
$footerBp = htmlspecialchars($footerBasePrefix, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

$siteBaseUrl = '';
if (!empty($_SERVER['DOCUMENT_ROOT'])) {
    $docRootReal = realpath((string) $_SERVER['DOCUMENT_ROOT']);
    $appRootReal = realpath(__DIR__);
    if ($docRootReal !== false && $appRootReal !== false && str_starts_with($appRootReal, $docRootReal)) {
        $relDir = trim(str_replace('\\', '/', substr($appRootReal, strlen($docRootReal))), '/');
        if ($relDir !== '') {
            $siteBaseUrl = '/' . implode('/', array_map('rawurlencode', explode('/', $relDir)));
        }
    }
}
$siteBaseSafe = htmlspecialchars($siteBaseUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<footer class="footer mt-auto py-3 bg-light">
    <div class="container">
        <div class="row align-items-start">
            <div class="col-md-4 col-lg-4 col-xl-4">
                <p>&copy;<span id="leto"><?= (int) date('Y') ?></span> FS Karavanke, Nejc Mihelič.<br>Vse pravice pridržane.</p>
                <a href="https://www.facebook.com/kdfskaravanke" target="_blank" rel="noopener noreferrer" title="Facebook" class="me-2"><i class="fab fa-facebook fa-2x"></i></a>
                <a href="https://www.instagram.com/kdfskaravanke/" target="_blank" rel="noopener noreferrer" title="Instagram" class="me-2"><i class="fab fa-instagram fa-2x"></i></a>
                <a href="https://www.youtube.com/channel/UCvmkwTYu8J5eHY8Olyn4t3w" target="_blank" rel="noopener noreferrer" title="Youtube"><i class="fab fa-youtube fa-2x" aria-hidden="true"></i></a>
                <hr>
            </div>
            <div class="col-md-4 col-lg-4 col-xl-4">
                <p>Kulturno društvo Folklorna skupina Karavanke</p>
                <div>Balos 4</div>
                <p>4290 Tržič</p>
                <p>Davčna številka: 56733623</p>
                <hr>
            </div>
            <div class="col-md-4 col-lg-4 col-xl-4">
                <div>Predsednica Saša Meglič</div>
                <div><a href="mailto:kdfskaravanke@gmail.com">kdfskaravanke@gmail.com</a></div>
                <p><a href="tel:051 332 787">051 332 787</a></p>
                <p>FS Karavanke ima status društva v javnem interesu, zato nas lahko podprete z donacijo od dohodnine.</p>
            </div>
        </div>
    </div>
</footer>
<div class="footerSpodaj">
    <div><a href="<?= $siteBaseSafe ?>/admin/login">Prijava</a>&nbsp;&nbsp; | &nbsp;&nbsp;<a href="<?= $siteBaseSafe ?>/sitemap">Kazalo strani</a></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/glightbox@3.3.0/dist/js/glightbox.min.js" defer></script>
<script src="<?= $footerBp ?>js/site.js?v=2" defer></script>
