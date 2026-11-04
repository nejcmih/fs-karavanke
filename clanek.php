<?php
declare(strict_types=1);

require_once __DIR__ . '/admin/includes/dbh.inc.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Ensure direct requests to /clanek.php?id=<ID> always redirect to /clanki/<ID>
if (is_int($id) && $id > 0 && isset($_SERVER['REQUEST_URI']) && preg_match('#/clanek(?:\.php)?(?:$|\?)#i', (string) $_SERVER['REQUEST_URI']) === 1) {
    $reqPath = (string) parse_url((string) $_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $baseDir = rtrim(str_replace('\\', '/', dirname($reqPath)), '/');
    header('Location: ' . $baseDir . '/clanki/' . $id, true, 301);
    exit();
}

$article = null;

if (is_int($id) && $id > 0) {
    $stmt = $pdo->prepare(
        'SELECT ID, naslov, kraj, DATE_FORMAT(datum, "%d. %m. %Y") AS lepdatum, besedilo, slike
         FROM clanki
         WHERE ID = :id
         LIMIT 1'
    );
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    if (is_array($row)) {
        $article = $row;
    }
}

if ($article === null) {
    http_response_code(404);
}

$articleId = $article !== null ? (int) $article['ID'] : 0;
$pageTitle = $article !== null
    ? htmlspecialchars((string) $article['naslov'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . ' | Folklorna skupina Karavanke'
    : 'Članek ni bil najden | Folklorna skupina Karavanke';
?>
<!DOCTYPE html>
<html lang="sl">
  <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <meta name="keywords" content="folklorna skupina, FS Karavanke, Tržič, kulturno društvo, KD FS">
      <meta name="description" content="Spletna stran Kulturnega društva Folklorna skupina Karavanke Tržič.">
      <meta name="author" content="FS Karavanke, Nejc Mihelič">
      <title><?= $pageTitle ?></title>

      <link rel="icon" href="../uploads/logo/favicon.ico" type="image/x-icon">

      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.1/baguetteBox.min.css">

      <link rel="stylesheet" href="../css/slog.css">

      <script src="https://kit.fontawesome.com/d543c0b4cd.js" crossorigin="anonymous"></script>

      <link rel="preconnect" href="https://fonts.gstatic.com">
      <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
      <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
  </head>
  <body class="d-flex flex-column min-vh-100">
      <nav class="navbar navbar-expand-xxl navbar-dark fixed-top bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../"><img src="../uploads/logo/logo-bel.png" alt="FS Karavanke logotip" /></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="../">Domov</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">O nas</a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item" href="../o-nas/predstavitev">Predstavitev</a></li>
                            <li><a class="dropdown-item" href="../o-nas/skupine">Skupine</a></li>
                            <li><a class="dropdown-item" href="../o-nas/vodstvo">Vodstvo</a></li>
                            <li><a class="dropdown-item" href="../o-nas/dosezki-priznanja">Dosežki in priznanja</a></li>
                            <li><a class="dropdown-item" href="../o-nas/zaloznistvo">Založništvo</a></li>
                            <li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="https://www.youtube.com/watch?v=hZ822gc8T48">Dokumentarni film</a></li>
                            <li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="https://www.youtube.com/watch?v=HLwjM9tnvpU">Videospot pevk ljudskih pesmi</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Dogodki</a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item" href="../dogodki/koncerti">Koncerti in prireditve</a></li>
                            <li><a class="dropdown-item" href="../dogodki/nastopi">Nastopi</a></li>
                            <li><a class="dropdown-item" href="../dogodki/razstave">Razstave</a></li>
                            <li><a class="dropdown-item" href="../dogodki/delavnice">Delavnice</a></li>
                            <li><a class="dropdown-item" href="../dogodki/koledovanje">Koledovanje</a></li>
                            <li><a class="dropdown-item" href="../dogodki/zabavni-koticek">Zabavni kotiček</a></li>
                            <li><a class="dropdown-item" href="../dogodki/ostalo">Ostalo</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="../gostovanja">Gostovanja</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Kostumska podoba</a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item" href="../kostumska-podoba/gorenjska">Gorenjska</a></li>
                            <li><a class="dropdown-item" href="../kostumska-podoba/koroska">Koroška</a></li>
                            <li><a class="dropdown-item" href="../kostumska-podoba/stajerska">Štajerska</a></li>
                            <li><a class="dropdown-item" href="../kostumska-podoba/prekmurje">Prekmurje</a></li>
                            <li><a class="dropdown-item" href="../kostumska-podoba/bela-krajina">Bela krajina</a></li>
                            <li><a class="dropdown-item" href="../kostumska-podoba/primorska">Primorska</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="../napovednik">Napovednik</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Povezave</a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="https://www.facebook.com/kdfskaravanke">Facebook</a></li>
                            <li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="https://www.instagram.com/kdfskaravanke/">Instagram</a></li>
                            <li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="https://www.youtube.com/channel/UCvmkwTYu8J5eHY8Olyn4t3w">Youtube</a></li>
                            <li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="https://www.mojaobcina.si/trzic/imenik/kd-folklorna-skupina-karavanke.html">MojaObčina.si</a></li>
                            <li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="https://www.youtube.com/watch?v=1S1jRXJ8pcs">Tržič - zmajevo mesto podjetnih in spretnih</a></li>
                            <li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="../files/Dohodnina.pdf">Dohodnina</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Pridruži se nam</a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item" href="../clanki/128">Vabilo k vpisu</a></li>
                            <li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="../files/Statut_drustva.pdf">Statut</a></li>
                            <li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="../files/Pristopna_izjava.pdf">Pristopna izjava</a></li>
                            <li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="../files/Soglasje_o_uporabi_osebnih_podatkov.pdf">Soglasje GDPR</a></li>
                            <li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="../files/Pristopna-izjava-podporni-clan.pdf">Pristopna izjava - podporni člani</a></li>
                            <li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="../files/Soglasje-podporni-clan.pdf">Soglasje GDPR - podporni člani</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
      </nav>

      <main class="domov wrapper flex-grow-1">
          <div class="container">
              <?php if ($article === null): ?>
                  <h1>Članek ni bil najden</h1>
                  <p>Zahtevani članek ne obstaja ali pa je bil odstranjen. <a href="../" class="povezava">Nazaj na domačo stran</a></p>
              <?php else: ?>
                  <?php
                  $naslovSafe = htmlspecialchars((string) $article['naslov'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                  $krajRaw = trim((string) ($article['kraj'] ?? ''));
                  $datumSafe = htmlspecialchars((string) $article['lepdatum'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                  $datumLine = $krajRaw === ''
                      ? $datumSafe
                      : htmlspecialchars($krajRaw, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . ', ' . $datumSafe;

                  $slikeRaw = trim((string) ($article['slike'] ?? ''));
                  $images = $slikeRaw !== '' ? array_values(array_filter(explode('|', $slikeRaw), static fn(string $s): bool => trim($s) !== '')) : [];
                  ?>
                  <h1><?= $naslovSafe ?></h1>
                  <div class="datum"><?= $datumLine ?></div>
                  <p><?= (string) $article['besedilo'] ?></p>

                  <?php if ($images !== []): ?>
                      <div class="gallery">
                          <div class="row align-items-start">
                              <?php foreach ($images as $idx => $imgFile): ?>
                                  <?php if ($idx > 0 && $idx % 6 === 0): ?>
                                      </div><div class="row align-items-start">
                                  <?php endif; ?>
                                  <?php
                                  $imgUrl = '../clanki/' . $articleId . '/' . rawurlencode($imgFile);
                                  $imgUrlSafe = htmlspecialchars($imgUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                                  ?>
                                  <div class="col">
                                      <a class="lightbox" href="<?= $imgUrlSafe ?>">
                                          <img src="<?= $imgUrlSafe ?>" alt="<?= $naslovSafe ?>" loading="lazy">
                                      </a>
                                  </div>
                              <?php endforeach; ?>
                          </div>
                      </div>
                  <?php endif; ?>
              <?php endif; ?>
          </div>
      </main>

      <?php include_once __DIR__ . '/footer.php'; ?>
      <script>
          document.getElementById("leto").innerHTML = new Date().getFullYear();
      </script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.1/baguetteBox.min.js"></script>
      <script>
          baguetteBox.run(".gallery");
      </script>
  </body>
</html>
