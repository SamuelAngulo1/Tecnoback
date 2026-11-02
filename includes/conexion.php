<?php
// Credenciales: por defecto las de XAMPP. En un hosting, crea includes/config.php (no se sube a GitHub).
$cfg = file_exists(__DIR__ . '/config.php') ? require __DIR__ . '/config.php'
     : ['host' => 'localhost', 'db' => 'tecnoback', 'user' => 'root', 'pass' => ''];
try {
  $pdo = new PDO("mysql:host={$cfg['host']};dbname={$cfg['db']};charset=utf8mb4", $cfg['user'], $cfg['pass'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
} catch (PDOException $e) {
  die('No se pudo conectar a la base de datos.');
}
session_start();
function e($t) { return htmlspecialchars((string)$t); }
function pesos($n) { return '$' . number_format($n, 0, ',', '.'); }
