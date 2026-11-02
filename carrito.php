<?php
require_once 'includes/conexion.php';
$c = &$_SESSION['carrito'];  // arreglo id_producto => cantidad
$c = $c ?? [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $accion = $_POST['accion'] ?? ''; $id = (int)($_POST['id'] ?? 0);
  if ($accion === 'agregar') { $c[$id] = ($c[$id] ?? 0) + 1; $_SESSION['msg'] = 'Producto agregado al carrito.'; header('Location: index.php'); exit; }
  if ($accion === 'quitar')  { unset($c[$id]); header('Location: carrito.php'); exit; }
  if ($accion === 'confirmar' && $c) {
    // RQ-20: pedido completo en una sola transacción (si una línea falla, no queda nada)
    $doc = trim($_POST['documento'] ?? ''); $dir = trim($_POST['direccion'] ?? '');
    $ok = $pdo->prepare('SELECT 1 FROM Cliente WHERE documento = ?'); $ok->execute([$doc]);
    if (!$ok->fetchColumn()) { $_SESSION['msg'] = 'Ese documento no está registrado. Regístrate primero.'; header('Location: registro.php'); exit; }
    try {
      $pdo->beginTransaction();
      $pdo->prepare('INSERT INTO Pedido (documento_cliente, direccion_envio) VALUES (?,?)')->execute([$doc, $dir]);
      $idPedido = $pdo->lastInsertId(); $total = 0;
      foreach ($c as $idp => $cant) {
        $p = $pdo->prepare('SELECT precio FROM Producto WHERE id_producto = ?'); $p->execute([$idp]);
        $precio = $p->fetchColumn();
        $pdo->prepare('INSERT INTO Detalle_pedido (id_pedido, id_producto, cantidad, precio_unitario) VALUES (?,?,?,?)')
            ->execute([$idPedido, $idp, $cant, $precio]);  // el trigger descuenta el inventario
        $total += $precio * $cant;
      }
      $pdo->prepare('UPDATE Pedido SET valor_a_pagar = ? WHERE id_pedido = ?')->execute([$total, $idPedido]);
      $pdo->commit(); $c = [];
      $_SESSION['msg'] = "Pedido #$idPedido registrado. Total: " . pesos($total);
    } catch (PDOException $ex) {
      $pdo->rollBack();
      $_SESSION['msg'] = 'No se pudo registrar el pedido: ' . (str_contains($ex->getMessage(), 'Inventario') ? 'no hay unidades suficientes.' : 'intenta de nuevo.');
    }
    header('Location: carrito.php'); exit;
  }
}
$items = [];
if ($c) {
  $in = implode(',', array_fill(0, count($c), '?'));
  $st = $pdo->prepare("SELECT * FROM Producto WHERE id_producto IN ($in)"); $st->execute(array_keys($c));
  $items = $st->fetchAll();
}
require 'includes/header.php';
?>
<h1>Tu pedido</h1>
<?php if (!$items): ?>
  <p>El carrito está vacío. <a href="index.php">Ver el catálogo</a></p>
<?php else: $total = 0; ?>
  <table class="tabla">
    <tr><th>Producto</th><th>Cantidad</th><th>Subtotal</th><th></th></tr>
    <?php foreach ($items as $p): $sub = $p['precio'] * $c[$p['id_producto']]; $total += $sub; ?>
      <tr><td><?= e($p['nombre']) ?></td><td><?= $c[$p['id_producto']] ?></td><td><?= pesos($sub) ?></td>
        <td><form method="POST"><input type="hidden" name="accion" value="quitar"><input type="hidden" name="id" value="<?= $p['id_producto'] ?>"><button class="sec">Quitar</button></form></td></tr>
    <?php endforeach; ?>
  </table>
  <p class="total">Total a pagar: <strong><?= pesos($total) ?></strong></p>
  <form class="formulario" method="POST">
    <input type="hidden" name="accion" value="confirmar">
    <label>Documento del cliente <input name="documento" required></label>
    <label>Dirección de envío <input name="direccion" required></label>
    <button>Confirmar pedido</button>
    <a href="registro.php">¿Aún no estás registrado?</a>
  </form>
<?php endif; ?>
<?php require 'includes/footer.php'; ?>
