<?php
declare(strict_types=1);

require_once __DIR__ . '/admin/includes/image-thumb.inc.php';

$pageTitle = isset($pageTitle) && is_string($pageTitle) && trim($pageTitle) !== ''
    ? $pageTitle
    : 'Folklorna skupina Karavanke';
$basePrefix = isset($basePrefix) && is_string($basePrefix) ? $basePrefix : '';
$activeNav = isset($activeNav) && is_string($activeNav) ? $activeNav : '';

$bp = htmlspecialchars($basePrefix, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$homeHref = $basePrefix === '' ? './' : $bp;
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
      <title><?= htmlspecialchars($pageTitle, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></title>

      <link rel="icon" href="<?= $bp ?>uploads/logo/favicon.ico" type="image/x-icon">

      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600&family=Raleway:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">

      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox@3.3.0/dist/css/glightbox.min.css">
      <link rel="stylesheet" href="<?= $bp ?>css/slog.css?v=3">

      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous" defer></script>
  </head>
  <body class="d-flex flex-column min-vh-100">
      <nav class="navbar navbar-expand-xxl navbar-dark fixed-top bg-dark">
          <div class="container">
              <a class="navbar-brand" href="<?= $homeHref ?>"><img src="<?= $bp ?>uploads/logo/logo-bel.png" alt="FS Karavanke logotip"></a>
              <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Odpri navigacijo">
                  <span class="navbar-toggler-icon"></span>
              </button>
              <div class="collapse navbar-collapse" id="navbarNavDropdown">
                  <ul class="navbar-nav">
                      <li class="nav-item">
                          <a class="nav-link<?= $activeNav === 'domov' ? ' active" aria-current="page' : '' ?>" href="<?= $homeHref ?>">Domov</a>
                      </li>
                      <li class="nav-item dropdown">
                          <a class="nav-link dropdown-toggle<?= $activeNav === 'o-nas' ? ' active' : '' ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">O nas</a>
                          <ul class="dropdown-menu">
                              <li><a class="dropdown-item" href="<?= $bp ?>o-nas/predstavitev">Predstavitev</a></li>
                              <li><a class="dropdown-item" href="<?= $bp ?>o-nas/skupine">Skupine</a></li>
                              <li><a class="dropdown-item" href="<?= $bp ?>o-nas/vodstvo">Vodstvo</a></li>
                              <li><a class="dropdown-item" href="<?= $bp ?>o-nas/dosezki-priznanja">Dosežki in priznanja</a></li>
                              <li><a class="dropdown-item" href="<?= $bp ?>o-nas/zaloznistvo">Založništvo</a></li>
                              <li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="https://www.youtube.com/watch?v=hZ822gc8T48">Dokumentarni film</a></li>
                              <li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="https://www.youtube.com/watch?v=HLwjM9tnvpU">Videospot pevk ljudskih pesmi</a></li>
                          </ul>
                      </li>
                      <li class="nav-item dropdown">
                          <a class="nav-link dropdown-toggle<?= $activeNav === 'dogodki' ? ' active' : '' ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Dogodki</a>
                          <ul class="dropdown-menu">
                              <li><a class="dropdown-item" href="<?= $bp ?>dogodki/koncerti">Koncerti in prireditve</a></li>
                              <li><a class="dropdown-item" href="<?= $bp ?>dogodki/nastopi">Nastopi</a></li>
                              <li><a class="dropdown-item" href="<?= $bp ?>dogodki/razstave">Razstave</a></li>
                              <li><a class="dropdown-item" href="<?= $bp ?>dogodki/delavnice">Delavnice</a></li>
                              <li><a class="dropdown-item" href="<?= $bp ?>dogodki/koledovanje">Koledovanje</a></li>
                              <li><a class="dropdown-item" href="<?= $bp ?>dogodki/zabavni-koticek">Zabavni kotiček</a></li>
                              <li><a class="dropdown-item" href="<?= $bp ?>dogodki/ostalo">Ostalo</a></li>
                          </ul>
                      </li>
                      <li class="nav-item">
                          <a class="nav-link<?= $activeNav === 'gostovanja' ? ' active" aria-current="page' : '' ?>" href="<?= $bp ?>gostovanja">Gostovanja</a>
                      </li>
                      <li class="nav-item dropdown">
                          <a class="nav-link dropdown-toggle<?= $activeNav === 'kostumska-podoba' ? ' active' : '' ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Kostumska podoba</a>
                          <ul class="dropdown-menu">
                              <li><a class="dropdown-item" href="<?= $bp ?>kostumska-podoba/gorenjska">Gorenjska</a></li>
                              <li><a class="dropdown-item" href="<?= $bp ?>kostumska-podoba/koroska">Koroška</a></li>
                              <li><a class="dropdown-item" href="<?= $bp ?>kostumska-podoba/stajerska">Štajerska</a></li>
                              <li><a class="dropdown-item" href="<?= $bp ?>kostumska-podoba/prekmurje">Prekmurje</a></li>
                              <li><a class="dropdown-item" href="<?= $bp ?>kostumska-podoba/bela-krajina">Bela krajina</a></li>
                              <li><a class="dropdown-item" href="<?= $bp ?>kostumska-podoba/primorska">Primorska</a></li>
                          </ul>
                      </li>
                      <li class="nav-item">
                          <a class="nav-link<?= $activeNav === 'napovednik' ? ' active" aria-current="page' : '' ?>" href="<?= $bp ?>napovednik">Napovednik</a>
                      </li>
                      <li class="nav-item dropdown">
                          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Povezave</a>
                          <ul class="dropdown-menu">
                              <li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="https://www.facebook.com/kdfskaravanke">Facebook</a></li>
                              <li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="https://www.instagram.com/kdfskaravanke/">Instagram</a></li>
                              <li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="https://www.youtube.com/channel/UCvmkwTYu8J5eHY8Olyn4t3w">Youtube</a></li>
                              <li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="https://www.mojaobcina.si/trzic/imenik/kd-folklorna-skupina-karavanke.html">MojaObčina.si</a></li>
                              <li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="https://www.youtube.com/watch?v=1S1jRXJ8pcs">Tržič - zmajevo mesto podjetnih in spretnih</a></li>
                              <li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="<?= $bp ?>files/Dohodnina.pdf">Dohodnina</a></li>
                          </ul>
                      </li>
                      <li class="nav-item dropdown">
                          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Pridruži se nam</a>
                          <ul class="dropdown-menu">
                              <li><a class="dropdown-item" href="<?= $bp ?>clanki/128">Vabilo k vpisu</a></li>
                              <li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="<?= $bp ?>files/Statut_drustva.pdf">Statut</a></li>
                              <li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="<?= $bp ?>files/Pristopna_izjava.pdf">Pristopna izjava</a></li>
                              <li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="<?= $bp ?>files/Soglasje_o_uporabi_osebnih_podatkov.pdf">Soglasje GDPR</a></li>
                              <li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="<?= $bp ?>files/Pristopna-izjava-podporni-clan.pdf">Pristopna izjava - podporni člani</a></li>
                              <li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="<?= $bp ?>files/Soglasje-podporni-clan.pdf">Soglasje GDPR - podporni člani</a></li>
                          </ul>
                      </li>
                  </ul>
              </div>
          </div>
      </nav>

