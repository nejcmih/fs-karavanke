<?php
declare(strict_types=1);

require_once __DIR__ . '/../admin/includes/dbh.inc.php';
require_once __DIR__ . '/../admin/includes/clanki-list.inc.php';
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
	  <title>Zabavni kotiček | Folklorna skupina Karavanke</title>
      
	  <link rel="icon" href="../uploads/logo/favicon.ico" type="image/x-icon">
	  
	  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
	  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
	  
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
          				<a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Dogodki</a>
          				<ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item" href="koncerti">Koncerti in prireditve</a></li>
            				<li><a class="dropdown-item" href="nastopi">Nastopi</a></li>
            				<li><a class="dropdown-item" href="razstave">Razstave</a></li>
							<li><a class="dropdown-item" href="delavnice">Delavnice</a></li>
            				<li><a class="dropdown-item" href="koledovanje">Koledovanje</a></li>
							<li><a class="dropdown-item" href="zabavni-koticek">Zabavni kotiček</a></li>
            				<li><a class="dropdown-item" href="ostalo">Ostalo</a></li>
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
          <div class="domovVsebina container">
			  <h1>Zabavni kotiček</h1>
			  
			  <?php
			  	renderArticleList($pdo, [
					'category' => 'zabavno',
					'orderDir' => 'DESC',
					'basePrefix' => '../',
					'showReadMore' => false,
				]);
			  ?>
			  
		  </div>

	  </main>
	  

	  <?php include_once('../footer.php'); ?>
	  <script>
		  document.getElementById("leto").innerHTML = new Date().getFullYear();
	  </script>
	  
	  
  </body>
</html>
