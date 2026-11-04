<?php
declare(strict_types=1);

$cardId = (string) ($_GET['id'] ?? '');
if (!preg_match('/^[0-9]{2}[a-z]?$/', $cardId)) {
    http_response_code(404);
    exit('Kartica ne obstaja.');
}

/** @var array<string, array{title: string, content: string}> $kartice */
$kartice = require __DIR__ . '/kartice_data.php';

if (!isset($kartice[$cardId])) {
    http_response_code(404);
    exit('Kartica ne obstaja.');
}

$card = $kartice[$cardId];
?>
<!DOCTYPE html>
<html lang="sl">
  <head>
	  <meta charset="utf-8">
	  <meta http-equiv="X-UA-Compatible" content="IE=edge">
	  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	  <meta name="author" content="FS Karavanke, Nejc Mihelič">
	  <title><?= htmlspecialchars($card['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></title>

	  <link rel="icon" href="../uploads/logo/druzabnaigra.ico" type="image/x-icon">

	  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
	  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>

	  <link rel="stylesheet" href="../css/druzabnaigra-slog.css">

	  <script src="https://kit.fontawesome.com/d543c0b4cd.js" crossorigin="anonymous"></script>

	  <link rel="preconnect" href="https://fonts.gstatic.com">
	  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
	  <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
  </head>

  <body class="d-flex flex-column min-vh-100">
    <div class="container">
<?= $card['content'] ?>
    </div>
  </body>
</html>

