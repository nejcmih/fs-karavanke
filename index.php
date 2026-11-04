<?php
declare(strict_types=1);

require_once __DIR__ . '/admin/includes/dbh.inc.php';
require_once __DIR__ . '/admin/includes/clanki-list.inc.php';
?>
<!DOCTYPE html>
<html lang="sl">
  <head>
	  <meta charset="utf-8">
	  <meta http-equiv="X-UA-Compatible" content="IE=edge">
	  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	  <meta name="keywords" content="folklorna skupina, FS Karavanke, Tržič, kulturno društvo, KD FS">
	  <meta name="description" content="Spletna stran Kulturnega društva Folklorne skupine Karavanke Tržič.">
	  <meta name="author" content="FS Karavanke, Nejc Mihelič">
	  <title>Folklorna skupina Karavanke</title>
      
	  <link rel="icon" href="uploads/logo/favicon.ico" type="image/x-icon">
	  
	  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
	  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
	  
	  <link rel="stylesheet" href="css/slog.css">
	  
	  <script src="https://kit.fontawesome.com/d543c0b4cd.js" crossorigin="anonymous"></script>
	  
	  <link rel="preconnect" href="https://fonts.gstatic.com">
	  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
	  <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
  </head>
    
  <body class="d-flex flex-column min-vh-100">
	  <nav class="navbar navbar-expand-xxl navbar-dark fixed-top bg-dark">
    	<div class="container">
        	<a class="navbar-brand" href="./"><img src="uploads/logo/logo-bel.png" alt="" /></a>
    		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      			<span class="navbar-toggler-icon"></span>
    		</button>
    		<div class="collapse navbar-collapse" id="navbarNavDropdown">
      			<ul class="navbar-nav">
        			<li class="nav-item"><a class="nav-link active" aria-current="page" href="./">Domov</a></li>
					<li class="nav-item dropdown">
          				<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">O nas</a>
          				<ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
            				<li><a class="dropdown-item" href="o-nas/predstavitev">Predstavitev</a></li>
            				<li><a class="dropdown-item" href="o-nas/skupine">Skupine</a></li>
            				<li><a class="dropdown-item" href="o-nas/vodstvo">Vodstvo</a></li>
							<li><a class="dropdown-item" href="o-nas/dosezki-priznanja">Dosežki in priznanja</a></li>
                            <li><a class="dropdown-item" href="o-nas/zaloznistvo">Založništvo</a></li>
            				<li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="https://www.youtube.com/watch?v=hZ822gc8T48">Dokumentarni film</a></li>
            				<li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="https://www.youtube.com/watch?v=HLwjM9tnvpU">Videospot pevk ljudskih pesmi</a></li>
          				</ul>
        			</li>
					<li class="nav-item dropdown">
          				<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Dogodki</a>
          				<ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item" href="dogodki/koncerti">Koncerti in prireditve</a></li>
            				<li><a class="dropdown-item" href="dogodki/nastopi">Nastopi</a></li>
            				<li><a class="dropdown-item" href="dogodki/razstave">Razstave</a></li>
							<li><a class="dropdown-item" href="dogodki/delavnice">Delavnice</a></li>
            				<li><a class="dropdown-item" href="dogodki/koledovanje">Koledovanje</a></li>
							<li><a class="dropdown-item" href="dogodki/zabavni-koticek">Zabavni kotiček</a></li>
            				<li><a class="dropdown-item" href="dogodki/ostalo">Ostalo</a></li>
          				</ul>
        			</li>
        			<li class="nav-item"><a class="nav-link" href="gostovanja">Gostovanja</a></li>
        			<li class="nav-item dropdown">
          				<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Kostumska podoba</a>
          				<ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
            				<li><a class="dropdown-item" href="kostumska-podoba/gorenjska">Gorenjska</a></li>
            				<li><a class="dropdown-item" href="kostumska-podoba/koroska">Koroška</a></li>
            				<li><a class="dropdown-item" href="kostumska-podoba/stajerska">Štajerska</a></li>
							<li><a class="dropdown-item" href="kostumska-podoba/prekmurje">Prekmurje</a></li>
            				<li><a class="dropdown-item" href="kostumska-podoba/bela-krajina">Bela krajina</a></li>
            				<li><a class="dropdown-item" href="kostumska-podoba/primorska">Primorska</a></li>
          				</ul>
        			</li>
					<li class="nav-item"><a class="nav-link" href="napovednik">Napovednik</a></li>
					<li class="nav-item dropdown">
          				<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Povezave</a>
          				<ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
            				<li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="https://www.facebook.com/kdfskaravanke">Facebook</a></li>
            				<li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="https://www.instagram.com/kdfskaravanke/">Instagram</a></li>
            				<li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="https://www.youtube.com/channel/UCvmkwTYu8J5eHY8Olyn4t3w">Youtube</a></li>
										<li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="https://www.mojaobcina.si/trzic/imenik/kd-folklorna-skupina-karavanke.html">MojaObčina.si</a></li>
                    <li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="https://www.youtube.com/watch?v=1S1jRXJ8pcs">Tržič - zmajevo mesto podjetnih in spretnih</a></li>
                    <li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="files/Dohodnina.pdf">Dohodnina</a></li>
          				</ul>
        			</li>
                    <li class="nav-item dropdown">
          				<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Pridruži se nam</a>
          				<ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
          					<li><a class="dropdown-item" href="clanki/128">Vabilo k vpisu</a></li>
            				<li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="files/Statut_drustva.pdf">Statut</a></li>
            				<li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="files/Pristopna_izjava.pdf">Pristopna izjava</a></li>
            				<li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="files/Soglasje_o_uporabi_osebnih_podatkov.pdf">Soglasje GDPR</a></li>
            				<li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="files/Pristopna-izjava-podporni-clan.pdf">Pristopna izjava - podporni člani</a></li>
            				<li><a class="dropdown-item" target="_blank" rel="noopener noreferrer" href="files/Soglasje-podporni-clan.pdf">Soglasje GDPR - podporni člani</a></li>
          				</ul>
        			</li>
      			</ul>
    		</div>
  		</div>
	  </nav>
	  
	  
	  <main class="homepage wrapper flex-grow-1">
		  <div class="carousel slide" data-bs-ride="carousel">
			  <div class="carousel-inner">
				  <div class="carousel-item active" data-bs-interval="4000">
					  <img src="uploads/carousel/carousel1.jpg" class="d-block w-100" alt="Slika 1">
				  </div>
				  <div class="carousel-item" data-bs-interval="4000">
					  <img src="uploads/carousel/carousel2.jpg" class="d-block w-100" alt="Slika 2">
				  </div>
				  <div class="carousel-item" data-bs-interval="4000">
					  <img src="uploads/carousel/carousel3.jpg" class="d-block w-100" alt="Slika 3">
				  </div>
				  <div class="carousel-item" data-bs-interval="4000">
					  <img src="uploads/carousel/carousel4.jpg" class="d-block w-100" alt="Slika 4">
				  </div>
				  <div class="carousel-item" data-bs-interval="4000">
					  <img src="uploads/carousel/carousel5.jpg" class="d-block w-100" alt="Slika 5">
				  </div>
				  <div class="carousel-item" data-bs-interval="4000">
					  <img src="uploads/carousel/carousel6.jpg" class="d-block w-100" alt="Slika 6">
				  </div>
			  </div>
		  </div>
		  
		  
		  <div class="domovVsebina container">
			  <h1>Zadnje novice</h1>
			  
			  <?php
			  renderArticleList($pdo, [
			      'excludeCategory' => 'napovednik',
			      'orderDir' => 'DESC',
			      'limit' => 3,
			  ]);
			  ?>
			  
		  </div>
		  
		  <div id="oDrustvu">
              <div class="container">
                  <div class="row align-items-start">
                      <div class="col-md-6" id="oDrustvuSlika">
                          <img src="uploads/mladinska-skupina/IMG_0561.JPG" alt="O društvu">
                      </div>
                      <div class="col-md-6" id="oDrustvuText">
						  <p><a href="./#oDrustvu">Slovenščina</a> | <a href="./?lang=en#oDrustvu">English</a></p>
						  <?php 
						  	if (isset($_GET["lang"])) {
								if ($_GET["lang"] == "en") {
									echo '<h1>About the group</h1>
									<p>The Karavanke Folklore Group comes from the small town of Tržič, situated in a narrow valley below the Karavanke Mountains in northern Slovenia along the border with Austria, a home to a long-standing shoemaking tradition.</p>
									<p>The very first beginnings of the folklore group date back to the 1950s, while the group was formally established in 1966 at the time of Tržič’s twinning with the French town of Saint Marie aux Mines. At that time, the folklore group was composed of one group of adults under the leadership of Marjan Vodnjov, while the first children’s group was formed in 1972.</p>
									<p>Today, the folklore group has about sixty members in total. All dancers, musicians and singers strive their best to recreate the Slovene folk tradition, thus ensuring continuation of Slovenia&#39;s cultural heritage for the benefit of both, the young and the old generation. Currently, one children&#39;s and one youth group, as well as folk singers, are active within the folklore group.</p>
									<p><a href="o-nas/predstavitev?lang=en">You can read more about the group here.</a></p>';
								}
							}
							else {
								echo '<h1>O društvu</h1>
							    <p>Kulturno društvo Folklorna skupina Karavanke prihaja iz manjšega mesta Tržič, ki leži v tesni dolini pod Karavankami na severu Slovenije ob meji z Avstrijo, kjer je doma dolgoletna čevljarska tradicija.</p>
								<p>Prvi začetki folklorne dejavnosti segajo v petdeseta leta 20. stoletja, skupina pa je bila ustanovljena leta 1966 ob pobratenju Tržiča s francoskim mestom Saint Marie aux Mines. Ob ustanovitvi je pod vodstvom Marjana Vodnjova delovala ena odrasla skupina, prva otroška pa se ji je pridružila že leta 1972.</p>
								<p>Folklorna skupina Karavanke v svojem društvu združuje okoli 60 članov. Vsi plesalci, godci in pevci se trudijo čim bolje poustvarjati slovensko ljudsko izročilo in s tem skrbijo, da kulturna dediščina na Slovenskem ne bo pozabljena. Trenutno v društvu delujeta otroška in mladinska folklorna skupina ter pevci in pevke ljudskih pesmi.</p>
								<p><a href="o-nas/predstavitev">Tukaj lahko preberete več o našem društvu.</a></p>';
							  }
						  ?>
                          
                      </div>
                  </div>
              </div>
          </div>
 
	  </main>
	  

	  
	  
	  <?php include_once('footer.php'); ?>
	  <script>
		  document.getElementById("leto").innerHTML = new Date().getFullYear();
	  </script>
	  
  </body>
</html>
