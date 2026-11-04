<?php
	require_once("../admin/includes/dbh.inc.php");
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
	  <title>Koledovanje | Folklorna skupina Karavanke</title>
      
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
		  <div class="container">
			  <h1>Koledovanje</h1>
			  <p>O koledovanju sta med prvimi pri nas pisala že Primož Trubar in Janez Vajkard Valvasor. Poznamo več vrst koledovanja, in sicer ob božičnih praznikih, novem letu, za svete tri kralje, ob sv. Juriju in za koline. Ime kolednik se danes pojavlja le še na Dolenjskem in Gorenjskem.</p>
			  <p>Namen koledovanja je, da prinesejo koledniki k hiši blagoslov za letino, zdravje, srečo ljudem in živini, za kar so prejeli darove, kot so jajca, klobase ali slanina. V skupinah po trije ali štirje koledniki so nekdaj hodili po vaseh in mestih.</p>
			  <p>Koledovanje postaja vedno bolj razširjen in tradicionalen običaj na slovenskih tleh. Nešteto otrok, mladine in starejših je v minulih letih stopalo po tej poti petja, molitve, veselja, druženja – prinašanja voščil in blagoslova ob praznikih in v novem letu. Koledniki so bili vedno prinašalci veselja in povezovalci župnije. Besedila pesmi kolednic so bila skoraj v vsaki vasi drugačna, vendar vse kolednice so imele  enako vsebino. Prvi del  pesmi je bil pozdrav hiši (Bog požegni hišo in ljudi, ki živijo notri v nji),   v drugem delu pesmi so koledniki prinašali v hišo srečo, zdravje in dobro letino. (Bodi zdrava vam družina in živina …), v tretjem delu pesmi so prosili darov s praznične mize.</p>
			  <p>Božične kolednice danes pojejo kot božične pesmi ali pa so ohranjene le v arhivih. V zbirki Karla Štreklja jih je skoraj 200. Tako kot pri ljudskih pesmih tudi pri kolednicah avtor ni znan. Po navadi so jih pisali duhovniki, učitelji in drugi izobraženci. Seveda pa so pesmi, ki so jih peli koledniki, doživljale folklorizacijo.</p>
			  <p>In ker v društvu želimo in strmimo k oživitvi običajev, bomo tudi letos izvedli koledovanje po vaseh Tržiča v izvedbi pevk in pevcev ljudskih pesmi in ostalih članov društva. Preko pesmi in voščil bomo ljudem približali tisto, kar se pozablja in jim zaželeli vse dobro v letu, ki prihaja.</p>
			  
			  <div class="gallery">
                  <div class="row align-items-start">
                      <div class="col">
                          <a class="lightbox" href="../uploads/koledovanje/20191220_172210.jpg">
                              <img src="../uploads/koledovanje/20191220_172210.jpg" alt="">
                          </a>
                      </div>
                      <div class="col">
                          <a class="lightbox" href="../uploads/koledovanje/20191220_172220.jpg">
                              <img src="../uploads/koledovanje/20191220_172220.jpg" alt="">
                          </a>
                      </div>
					  <div class="col">
                          <a class="lightbox" href="../uploads/koledovanje/20191220_172228.jpg">
                              <img src="../uploads/koledovanje/20191220_172228.jpg" alt="">
                          </a>
                      </div>
					  <div class="col">
                          <a class="lightbox" href="../uploads/koledovanje/20191220_173650.jpg">
                              <img src="../uploads/koledovanje/20191220_173650.jpg" alt="">
                          </a>
                      </div>
					  <div class="col">
                          <a class="lightbox" href="../uploads/koledovanje/20191220_174525.jpg">
                              <img src="../uploads/koledovanje/20191220_174525.jpg" alt="">
                          </a>
                      </div>
                  </div>
                  <div class="row align-items-start">
                      <div class="col">
                          <a class="lightbox" href="../uploads/koledovanje/20191220_174912.jpg">
                              <img src="../uploads/koledovanje/20191220_174912.jpg" alt="">
                          </a>
                      </div>
					  <div class="col">
                          <a class="lightbox" href="../uploads/koledovanje/20191220_180514.jpg">
                              <img src="../uploads/koledovanje/20191220_180514.jpg" alt="">
                          </a>
                      </div>
					  <div class="col">
                          <a class="lightbox" href="../uploads/koledovanje/k1.jpg">
                              <img src="../uploads/koledovanje/k1.jpg" alt="">
                          </a>
                      </div>
					  <div class="col">
                          <a class="lightbox" href="../uploads/koledovanje/k2.jpg">
                              <img src="../uploads/koledovanje/k2.jpg" alt="">
                          </a>
                      </div>
					  <div class="col">
                          <a class="lightbox" href="../uploads/koledovanje/received_485103535470032.jpeg">
                              <img src="../uploads/koledovanje/received_485103535470032.jpeg" alt="">
                          </a>
                      </div>
                  </div>
              </div>
			  
		  </div>
	  </main>
	  
	  
	  <?php include_once('../footer.php'); ?>
	  <script>
		  document.getElementById("leto").innerHTML = new Date().getFullYear();
	  </script>
	  <script src="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.1/baguetteBox.min.js"></script>
      <script>
		  baguetteBox.run(".gallery");
      </script>
	  
	  
  </body>
</html>
