<?php
declare(strict_types=1);

$pageTitle = 'Predstavitev | Folklorna skupina Karavanke';
$basePrefix = '../';
$activeNav = 'o-nas';

require __DIR__ . '/../header.php';

$isEn = (($_GET['lang'] ?? '') === 'en');
?>
      <main class="domov wrapper flex-grow-1">
          <div class="container">
              <p><a href="predstavitev">Slovenščina</a> | <a href="predstavitev?lang=en">English</a></p>
              <?php if ($isEn): ?>
                  <h1>The Karavanke Folklore Group and Society</h1>
                  <p>The Karavanke Folklore Group comes from the small town of Tržič, situated in a narrow valley below the Karavanke Mountains in northern Slovenia along the border with Austria, a home to a long-standing shoemaking tradition.</p>
                  <p>The very first beginnings of the folklore group date back to the 1950s, while the group was formally established in 1966 at the time of Tržič’s twinning with the French town of Saint Marie aux Mines. At that time, the folklore group was composed of one group of adults under the leadership of Marjan Vodnjov, while the first children’s group was formed in 1972.</p>
                  <p>Today, the folklore group has about sixty members in total. All dancers, musicians and singers strive their best to recreate the Slovene folk tradition, thus ensuring continuation of Slovenia&#39;s cultural heritage for the benefit of both, the young and the old generation. Currently, two children&#39;s and one youth group, as well as folk singers, are active within the folklore group.</p>
              <?php else: ?>
                  <h1>Predstavitev KD FS Karavanke</h1>
                  <p>Kulturno društvo Folklorna skupina Karavanke prihaja iz manjšega mesta Tržič, ki leži v tesni dolini pod Karavankami na severu Slovenije ob meji z Avstrijo, kjer je doma dolgoletna čevljarska tradicija.</p>
                  <p>Prvi začetki folklorne dejavnosti segajo v petdeseta leta 20. stoletja, skupina pa je bila ustanovljena leta 1966 ob pobratenju Tržiča s francoskim mestom Sainte Marie aux Mines. Ob ustanovitvi je pod vodstvom Marjana Vodnjova delovala ena odrasla skupina, prva otroška pa se ji je pridružila že leta 1972.</p>
                  <p>Folklorna skupina Karavanke v svojem društvu združuje okoli 60 članov. Vsi plesalci, godci in pevci se trudijo čim bolje poustvarjati slovensko ljudsko izročilo in s tem skrbijo, da kulturna dediščina na Slovenskem ne bo pozabljena. Trenutno v društvu delujejo mlajša in starejša otroška folklorna skupina, mladinska folklorna skupina ter pevci in pevke ljudskih pesmi.</p>
              <?php endif; ?>

              <div class="gallery">
                  <div class="row align-items-start">
                      <div class="col">
                          <?= renderGalleryItem('uploads/pevke/pevke.jpg', 'Pevke ljudskih pesmi', '../', 960) ?>
                      </div>
                      <div class="col">
                          <?= renderGalleryItem('uploads/otroska-skupina/kje_Ste_deklic-13.jpg', 'Otroška folklorna skupina', '../', 960) ?>
                      </div>
                      <div class="col">
                          <?= renderGalleryItem('uploads/pevci/IMG_1952.jpg', 'Pevci ljudskih pesmi', '../', 960) ?>
                      </div>
                  </div>
              </div>

              <?php if ($isEn): ?>
                  <p>The folklore group&#39;s current programme mainly encompasses stage performances reproducing the folk tradition of the group&#39;s hometown and its surroundings. That said, we are also pleased to learn more about the folk tradition of other Slovenian regions, which we also include in our programme. The rich repertoire of Slovenian folk songs performed by young singers, some of whom perform solos, is also part of our programme.</p>
                  <p>However, the group&#39;s real treasure is its vast number of costumes, many of which are made based on originals exhibited in museums or written sources describing the clothing culture of Slovenes. Dani Meglič has been the exemplary custodian of our costumes for over a decade.</p>
                  <p>In 2016, the Group celebrated its fiftieth anniversary and received the plaque of the Municipality of Tržič for outstanding achievements. The Group has also been awarded several “Kurnik” plaques, and in our collection we also cherish the “Marolt” badge and certificate. All our group leaders have completed an introductory seminar to receive the title of a folklore group professional leader, and they regularly attend advance seminars on traditional dance, singing, music and clothing culture of Slovenes.</p>
                  <p>In addition to appearing at events in Slovenia and visiting venues abroad, the group regularly participates in district and regional folklore group competitions and performances and is honoured to have been selected several times to appear at the national competition, which is the greatest achievement that any folklore group can aspire to.</p>
              <?php else: ?>
                  <p>Sedanji program društva zajema predvsem odrske postavitve, ki temeljijo na poustvarjanju ljudskega izročila domačega kraja z okolico, z veseljem pa se spoznavamo tudi z ljudskim izročilom ostalih slovenskih pokrajin, ki ga vključujemo v program. Le-tega bogati tudi pester repertoar slovenskih ljudskih pesmi v izvedbi mladih pevcev in pevk, ki na prireditvah nastopajo tudi samostojno.</p>
                  <p>Pravo bogastvo društva je veliko število kostumov, od katerih je marsikateri izdelan na podlagi muzejskih originalov ali pisnih virov, ki govore o oblačilni kulturi Slovencev. Za garderobo že več kot desetletje zgledno skrbi Dani Meglič.</p>
                  <p>Skupina je v letu 2016 praznovala 50-letnico delovanja, ko je prejela plaketo Občine Tržič za izredne dosežke. Društvo se lahko pohvali tudi s številnimi Kurnikovimi plaketami, v svoji zbirki pa imamo tudi Maroltovo značko in listino. Vsi vodje so opravili začetni seminar in s tem pridobili naziv strokovni vodja folklorne skupine, redno pa se udeležujejo tudi nadaljevalnih seminarjev o plesni, pevski, glasbeni in oblačilni kulturi Slovencev.</p>
                  <p>Poleg nastopov doma in gostovanj v tujini skupina redno sodeluje na območnih in regijskih srečanjih folklornih skupin, v čast pa si šteje, da je bila že večkrat izbrana na državno srečanje, kar je največ, kar folklorna skupina lahko doseže.</p>
              <?php endif; ?>

              <?= renderProgressiveImg('uploads/mladinska-skupina/Untitled-collage4.jpg', 'Kolaž', '../', 960, 'predstavitev-slika') ?>
          </div>
      </main>

      <?php require __DIR__ . '/../footer.php'; ?>
  </body>
</html>
