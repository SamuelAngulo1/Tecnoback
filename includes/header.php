<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tecnoback · Tecnología para un mejor mañana</title>
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header class="top">
  <a class="logo" href="index.php"><span class="rayo">⚡</span>TECNO<b>BACK</b></a>
  <nav>
    <a href="index.php">Catálogo</a>
    <a href="registro.php">Registrarme</a>
    <a href="carrito.php">Carrito (<?= array_sum($_SESSION['carrito'] ?? []) ?>)</a>
  </nav>
</header>
<main>
<?php if (!empty($_SESSION['msg'])): ?><p class="aviso"><?= e($_SESSION['msg']) ?></p><?php unset($_SESSION['msg']); endif; ?>
