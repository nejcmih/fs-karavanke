<?php
declare(strict_types=1);

$pageTitle = 'Koledovanje | Folklorna skupina Karavanke';
$basePrefix = '../';
$activeNav = 'dogodki';

require __DIR__ . '/../header.php';

$koledovanjeRows = [
    [
        'uploads/koledovanje/20191220_172210.jpg',
        'uploads/koledovanje/20191220_172220.jpg',
        'uploads/koledovanje/20191220_172228.jpg',
        'uploads/koledovanje/20191220_173650.jpg',
        'uploads/koledovanje/20191220_174525.jpg',
    ],
    [
        'uploads/koledovanje/20191220_174912.jpg',
        'uploads/koledovanje/20191220_180514.jpg',
        'uploads/koledovanje/k1.jpg',
        'uploads/koledovanje/k2.jpg',
        'uploads/koledovanje/received_485103535470032.jpeg',
    ],
];
?>
      <main class="domov wrapper flex-grow-1">
          <div class="container">
              <h1>Koledovanje</h1>
              <p>O koledovanju sta med prvimi pri nas pisala že Primož Trubar in Janez Vajkard Valvasor. Poznamo več vrst koledovanja, in sicer ob božičnih praznikih, novem letu, za svete tri kralje, ob sv. Juriju in za koline. Ime kolednik se danes pojavlja le še na Dolenjskem in Gorenjskem.</p>
              <p>Namen koledovanja je, da prinesejo koledniki k hiši blagoslov za letino, zdravje, srečo ljudem in živini, za kar so prejeli darove, kot so jajca, klobase ali slanina. V skupinah po trije ali štirje koledniki so nekdaj hodili po vaseh in mestih.</p>
              <p>Koledovanje postaja vedno bolj razširjen in tradicionalen običaj na slovenskih tleh. Nešteto otrok, mladine in starejših je v minulih letih stopalo po tej poti petja, molitve, veselja, druženja – prinašanja voščil in blagoslova ob praznikih in v novem letu. Koledniki so bili vedno prinašalci veselja in povezovalci župnije. Besedila pesmi kolednic so bila skoraj v vsaki vasi drugačna, vendar vse kolednice so imele enako vsebino. Prvi del pesmi je bil pozdrav hiši (Bog požegni hišo in ljudi, ki živijo notri v nji), v drugem delu pesmi so koledniki prinašali v hišo srečo, zdravje in dobro letino. (Bodi zdrava vam družina in živina …), v tretjem delu pesmi so prosili darov s praznične mize.</p>
              <p>Božične kolednice danes pojejo kot božične pesmi ali pa so ohranjene le v arhivih. V zbirki Karla Štreklja jih je skoraj 200. Tako kot pri ljudskih pesmih tudi pri kolednicah avtor ni znan. Po navadi so jih pisali duhovniki, učitelji in drugi izobraženci. Seveda pa so pesmi, ki so jih peli koledniki, doživljale folklorizacijo.</p>
              <p>In ker v društvu želimo in strmimo k oživitvi običajev, bomo tudi letos izvedli koledovanje po vaseh Tržiča v izvedbi pevk in pevcev ljudskih pesmi in ostalih članov društva. Preko pesmi in voščil bomo ljudem približali tisto, kar se pozablja in jim zaželeli vse dobro v letu, ki prihaja.</p>

              <div class="gallery">
                  <?php foreach ($koledovanjeRows as $rowImgs): ?>
                      <div class="row align-items-start">
                          <?php foreach ($rowImgs as $imgRel): ?>
                              <div class="col">
                                  <?= renderGalleryItem($imgRel, 'Koledovanje', '../', 960) ?>
                              </div>
                          <?php endforeach; ?>
                      </div>
                  <?php endforeach; ?>
              </div>
          </div>
      </main>

      <?php require __DIR__ . '/../footer.php'; ?>
  </body>
</html>
