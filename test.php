<?php
require_once 'includes/conexion.php';
echo 'Conexión exitosa a ' . $cfg['db'] . ' · Productos: ' . $pdo->query('SELECT COUNT(*) FROM Producto')->fetchColumn();
