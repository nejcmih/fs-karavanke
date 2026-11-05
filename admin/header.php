<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.inc.php';
startSecureSession();

$adminPageTitle = isset($adminPageTitle) && is_string($adminPageTitle) && trim($adminPageTitle) !== ''
    ? $adminPageTitle . ' | Uredništvo FS Karavanke'
    : 'Uredništvo | FS Karavanke';
?>
<!DOCTYPE html>
<html lang="sl">
  <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <meta name="robots" content="noindex, nofollow">
      <title><?= escapeHtml($adminPageTitle) ?></title>

      <link rel="icon" href="../uploads/logo/favicon.ico" type="image/x-icon">

      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Raleway:wght@500;600;700&display=swap" rel="stylesheet">

      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
      <link rel="stylesheet" href="../css/admin-slog.css?v=4">

      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous" defer></script>
  </head>
