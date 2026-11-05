<?php
declare(strict_types=1);

$pageTitle = 'Skupine | Folklorna skupina Karavanke';
$basePrefix = '../';
$activeNav = 'o-nas';

require __DIR__ . '/../header.php';

/**
 * Render a 6-column-per-row static gallery grid with empty trailing cells preserved.
 *
 * @param list<string> $paths
 */
function renderSkupinaGallery(array $paths, string $alt, int $colsPerRow = 6): void
{
    echo '<div class="gallery">';
    $chunks = array_chunk($paths, $colsPerRow);
    foreach ($chunks as $chunk) {
        echo '<div class="row align-items-start">';
        foreach ($chunk as $rel) {
            echo '<div class="col">' . renderGalleryItem($rel, $alt, '../', 960) . '</div>';
        }
        for ($pad = count($chunk); $pad < $colsPerRow; $pad++) {
            echo '<div class="col"></div>';
        }
        echo '</div>';
    }
    echo '</div>';
}
?>
      <main class="domov wrapper flex-grow-1">
          <div class="container">
              <h1>Skupine</h1>

              <h2>Mladinska skupina</h2>
              <p>V mladinsko skupino so vključeni mladi od 15. do 23. leta starosti. Kar nekaj je takih, ki so pri skupini že desetletje, večina jih pleše nad 5 let, nekaj pa je tudi novincev. Mladinci na vajah, ki potekajo 2x tedensko, spoznavajo ne samo izročilo domačega kraja z okolico, ampak tudi izročilo drugih slovenskih pokrajin. Skupina je gostovala tudi v tujini in sicer v pobratenem mestu Saint Marie aux Mines v Franciji in na mednarodnem folklornem festivalu v Ukrajini, kjer je prejela posebno priznanje za izvrstno predstavljen program. Skupina je dvakrat organizirala tridnevno izmenjavo z dvema od slovenskih folklornih skupin, v čast pa si šteje, da je bila redno izbrana na državno srečanje otroških, v zadnjih letih pa tudi odraslih folklornih skupin. Skupino vodita Saša Meglič in Pija Japelj.</p>
              <?php
              renderSkupinaGallery([
                  'uploads/mladinska-skupina/20180601_193424.jpg',
                  'uploads/mladinska-skupina/DSC_0023.jpg',
                  'uploads/mladinska-skupina/DSC_0431.jpg',
                  'uploads/mladinska-skupina/DSC_0610.jpg',
                  'uploads/mladinska-skupina/DSC_0624.jpg',
                  'uploads/mladinska-skupina/DSC_1098.jpg',
                  'uploads/mladinska-skupina/gorenjska1.jpg',
                  'uploads/mladinska-skupina/kje_Ste_deklic-26.jpg',
                  'uploads/mladinska-skupina/kje_Ste_deklic-31.jpg',
                  'uploads/mladinska-skupina/kje_Ste_deklic-32.jpg',
                  'uploads/mladinska-skupina/kje_Ste_deklic-35.jpg',
                  'uploads/mladinska-skupina/kje_Ste_deklic-38.jpg',
                  'uploads/mladinska-skupina/kje_Ste_deklic-39.jpg',
                  'uploads/mladinska-skupina/Mladinci_DSC_0237.jpg',
                  'uploads/mladinska-skupina/Regijsko-2019.jpg',
                  'uploads/mladinska-skupina/Regijsko-OFS-vecja.jpg',
                  'uploads/mladinska-skupina/Untitled-collage4.jpg',
              ], 'Mladinska skupina');
              ?>

              <h2>Starejša otroška skupina</h2>
              <p>Starejša otroška skupina vključuje otroke stare od 8 do 15 let. Večina pri skupini pleše že kar nekaj let, nekaj pa je takih, ki so se skupini pridružili v zadnjih letih. Na vajah, ki potekajo 1x tedensko, se poskušajo preko igre, pesmi, plesov, šeg in navad čim bolje seznaniti s kulturno dediščino Gorenjske. Največji poudarek dajemo načinu življenja otrok v 19. in prvi polovici 20. stoletja. Otroci na vajah spoznavajo, kako so otroci živeli nekoč, katere igre so se igrali, katere pesmice so peli, kako so bili oblečeni, s čim so se preživljali, katere stvari so jim nekoč predstavljale velike dobrine ipd. Vse to je za otroke v današnjem času težko predstavljivo, vendar po drugi strani zelo zanimivo. Otroci preko glasbe in pesmi razvijajo ritmični in melodični posluh, pridobivajo veselje do plesa in petja, z nastopanjem pa iz leta v leto razvijajo smisel za odrsko igro, govorne sposobnosti, samozavest in prenekatere druge veščine, ki jim koristijo tudi v nadaljnjem življenju. Skupino vodi Nika Buček.</p>
              <?php
              renderSkupinaGallery([
                  'uploads/otroska-skupina/DSC_0020.jpg',
                  'uploads/otroska-skupina/DSC_0190.jpg',
                  'uploads/otroska-skupina/folklora-top-3056.jpg',
                  'uploads/otroska-skupina/folklora-top-3057.jpg',
                  'uploads/otroska-skupina/kje_Ste_deklic-12.jpg',
                  'uploads/otroska-skupina/kje_Ste_deklic-13.jpg',
                  'uploads/otroska-skupina/kje_Ste_deklic-9.jpg',
                  'uploads/otroska-skupina/Untitled-collage2.jpg',
              ], 'Starejša otroška skupina');
              ?>

              <h2>Mlajša otroška skupina</h2>
              <p>V Mlajšo otroško folklorno skupino so vključeni naši najmlajši člani stari od 4 do 8 let. Skupina je z delovanjem začela v mesecu oktobru leta 2022, vaje pa obiskujejo enkrat tedensko. Otroci kulturno dediščino najraje spoznavajo preko iger, med katerimi so jim zelo zanimive prstne igre in bibarije. Na vajah so se naučili že več ljudskih pesmi, s katerimi zelo radi tudi nastopajo. Prvič so se predstavili na območnem srečanju otroških folklornih skupin v Tržiču, kjer so nastopili z odrsko postavitvijo En kovač konja kuje.</p>
              <?php
              renderSkupinaGallery([
                  'uploads/mlajsa-otroska-skupina/mlajsa1.jpg',
                  'uploads/mlajsa-otroska-skupina/mlajsa2.jpg',
                  'uploads/mlajsa-otroska-skupina/mlajsa3.jpg',
                  'uploads/mlajsa-otroska-skupina/mlajsa4.jpg',
                  'uploads/mlajsa-otroska-skupina/mlajsa5.jpg',
                  'uploads/mlajsa-otroska-skupina/mlajsa6.jpeg',
              ], 'Mlajša otroška skupina');
              ?>

              <h2>Pevci in pevke ljudskih pesmi</h2>
              <p>Pevci Kulturnega društva Folklorne skupine Karavanke izhajajo iz mladinske folklorne skupine, medtem ko so bile večina pevk članice nekdanje odrasle skupine. Tako pevci kot pevke prepevajo ljudske pesmi iz različnih slovenskih pokrajin. Pri izboru pesmi se poslužujejo zvočnih posnetkov in pisnih virov, preko katerih skušajo ohraniti način petja, ki je bil poznan v preteklosti, poleg tega pa se poslužujejo tudi sodobnih pristopov do poustvarjanja slovenske pesemske dediščine. Znanje pridobivajo na različnih seminarjih. V ljudskih pesmih spoznavajo in z izvajanjem predstavljajo življenje in čustvovanje slovenskega človeka v preteklosti, saj se zavedajo, da se ravno v duhovnem ljudskem izročilu najizraziteje kažejo posebnosti naše kulture in načina življenja. Tako pevci kot pevke se predstavljajo na društvenih koncertih, organizirajo samostojne koncerte in nastopajo na prireditvah v Tržiču in okolici. V zadnjih letih obe pevski skupini že tradicionalno izvajata koledovanje po tržiških vaseh, pevke ljudskih pesmi pa so ob praznovanju 50-letnice društva, leta 2016, izdale zgoščenko tržiških ljudskih pesmi z naslovom Oj dolinca, oj dolinca. Skupino pevcev vodi Pija Japelj, skupino pevk pa vodita Pija Japelj in Manca Perko.</p>
              <?php
              renderSkupinaGallery([
                  'uploads/pevci/20190522_201907.jpg',
                  'uploads/pevci/DSC_0421.jpg',
                  'uploads/pevci/IMG_1952.jpg',
                  'uploads/pevci/IMG_1979.jpg',
                  'uploads/pevci/IMG_3360.jpg',
                  'uploads/pevci/obmocno-pevci.jpg',
                  'uploads/pevke/20190928_103709.jpg',
                  'uploads/pevke/dobrodelni-koncert.jpg',
                  'uploads/pevke/DSC_0470.jpg',
                  'uploads/pevke/DSC_0630.jpg',
                  'uploads/pevke/IMG_20190522_202210.jpg',
                  'uploads/pevke/pevke.jpg',
              ], 'Pevci in pevke ljudskih pesmi');
              ?>

              <h2>Godci ljudskih viž</h2>
              <p>Poustvarjanje slovenske plesne dediščine si ne moremo predstavljati brez ustrezne glasbene spremljave. V preteklosti so ljudske plese in viže igrali glasbeniki v različnih sestavih. V 19. stoletju se na alpskem območju čuti prevlada violine, basa in opreklja, pogosti so tudi klarinet in druga pihala. Proti koncu stoletja je te inštrumente vse bolj izpodrivala diatonična harmonika, ki še danes igra za inštrument, brez katerega pri folklorni skupini skoraj ne gre. Godci v našem društvu igrajo na zgoraj omenjene inštrumente, pri čemer prevladujeta diatonična in klavirska harmonika.</p>
              <?php
              renderSkupinaGallery([
                  'uploads/godci/DSC04420.JPG',
                  'uploads/godci/godci3.jpg',
                  'uploads/godci/godci4.jpg',
              ], 'Godci ljudskih viž', 4);
              ?>
          </div>
      </main>

      <?php require __DIR__ . '/../footer.php'; ?>
  </body>
</html>
