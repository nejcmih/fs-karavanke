<?php
declare(strict_types=1);
?>
<div class="rich-editor-toolbar" id="richEditorToolbar" role="toolbar" aria-label="Orodna vrstica za oblikovanje članka">
    <button type="button" data-block="P" title="Navaden odstavek">Odstavek</button>
    <button type="button" data-block="H2" title="Podnaslov" class="fw-bold">H2 Podnaslov</button>
    <button type="button" data-block="H3" title="Manjši podnaslov" class="fw-semibold">H3</button>

    <span class="toolbar-divider"></span>

    <button type="button" data-cmd="bold" title="Krepko (Ctrl+B)"><i class="fa-solid fa-bold"></i></button>
    <button type="button" data-cmd="italic" title="Ležeče (Ctrl+I)"><i class="fa-solid fa-italic"></i></button>
    <button type="button" data-cmd="underline" title="Podčrtano (Ctrl+U)"><i class="fa-solid fa-underline"></i></button>

    <span class="toolbar-divider"></span>

    <button type="button" data-cmd="insertUnorderedList" title=" alinejni seznam"><i class="fa-solid fa-list-ul"></i></button>
    <button type="button" data-cmd="insertOrderedList" title="Oštevilčen seznam"><i class="fa-solid fa-list-ol"></i></button>
    <button type="button" data-block="BLOCKQUOTE" title="Poudarjen citat"><i class="fa-solid fa-quote-left"></i></button>

    <span class="toolbar-divider"></span>

    <button type="button" data-action="link" title="Vstavi spletno povezavo"><i class="fa-solid fa-link"></i></button>
    <button type="button" data-action="unlink" title="Odstrani povezavo"><i class="fa-solid fa-link-slash"></i></button>
    <button type="button" data-cmd="removeFormat" title="Počisti oblikovanje"><i class="fa-solid fa-eraser"></i></button>

    <span class="toolbar-divider"></span>

    <button type="button" data-action="insert-image" class="btn-toolbar-media" title="Naloži in vstavi posamezno sliko med besedilo">
        <i class="fa-regular fa-image me-1"></i>Vstavi sliko
    </button>
    <button type="button" data-action="insert-gallery" class="btn-toolbar-media" title="Naloži in vstavi galerijo več slik med besedilo">
        <i class="fa-regular fa-images me-1"></i>Vstavi galerijo
    </button>
</div>

