<?php
require_once 'includes/conexion.php';
// RQ-04 y RQ-05: registrar cliente (documento es la llave; edad opcional y no negativa)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $doc = trim($_POST['documento'] ?? ''); $nom = trim($_POST['nombre'] ?? '');
  $cel = trim($_POST['celular'] ?? ''); $edad = ($_POST['edad'] ?? '') === '' ? null : (int)$_POST['edad'];
  if ($doc === '' || $nom === '' || $cel === '' || ($edad !== null && $edad < 0)) {
    $_SESSION['msg'] = 'Revisa los datos: documento, nombre y celular son obligatorios.';
  } else {
    try {
      $pdo->prepare('INSERT INTO Cliente (documento, nombre_cliente, edad, celular) VALUES (?,?,?,?)')->execute([$doc, $nom, $edad, $cel]);
      $_SESSION['msg'] = 'Cliente registrado. Ya puedes confirmar tu pedido.';
      header('Location: carrito.php'); exit;
    } catch (PDOException $ex) { $_SESSION['msg'] = 'Ese documento ya está registrado.'; }
  }
  header('Location: registro.php'); exit;
}
require 'includes/header.php';
?>
<h1>Registro de cliente</h1>
<form class="formulario" method="POST">
  <label>Documento <input name="documento" required></label>
  <label>Nombre del cliente <input name="nombre" required></label>
  <label>Edad (opcional) <input name="edad" type="number" min="0"></label>
  <label>Celular <input name="celular" required></label>
  <button>Registrar cliente</button>
</form>
<?php require 'includes/footer.php'; ?>
