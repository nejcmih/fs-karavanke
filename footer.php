<footer class="footer mt-auto py-3 bg-light">
    <div class="container">
        <div class="row align-items-start">
            <div class="col-md-4 col-lg-4 col-xl-4">
                <p>&copy;<span id="leto"></span> FS Karavanke, Nejc Mihelič.<br>Vse pravice pridržane.</p>
				<a href="https://www.facebook.com/kdfskaravanke" target="_blank" rel="noopener noreferrer" data-toggle="tooltip" data-placement="top" title="Facebook"><i class="fab fa-facebook fa-2x"></i></a>
                <a href="https://www.instagram.com/kdfskaravanke/" target="_blank" rel="noopener noreferrer" data-toggle="tooltip" data-placement="top" title="Instagram"><i class="fab fa-instagram fa-2x"></i></a>
                <a href="https://www.youtube.com/channel/UCvmkwTYu8J5eHY8Olyn4t3w" target="_blank" rel="noopener noreferrer" data-toggle="tooltip" data-placement="top" title="Youtube"><i class="fab fa-youtube fa-2x" aria-hidden="true"></i></a>
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
<?php
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
?>
<div class="footerSpodaj">
    <div><a href="<?= htmlspecialchars($siteBaseUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>/admin/login">Prijava</a>&nbsp;&nbsp; | &nbsp;&nbsp;<a href="<?= htmlspecialchars($siteBaseUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>/sitemap">Kazalo strani</a></div>
</div>