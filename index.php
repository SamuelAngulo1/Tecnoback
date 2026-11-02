<?php
require_once 'includes/conexion.php';
// RQ-17: buscar por nombre y filtrar por categoría (consulta preparada)
$q = trim($_GET['q'] ?? '');
$cat = (int)($_GET['cat'] ?? 0);
$sql = 'SELECT p.*, c.nombre AS categoria FROM Producto p JOIN Categoria c ON c.id_categoria = p.id_categoria
        WHERE p.nombre LIKE :q AND (:cat = 0 OR p.id_categoria = :cat2) ORDER BY p.nombre';
$st = $pdo->prepare($sql);
$st->execute(['q' => "%$q%", 'cat' => $cat, 'cat2' => $cat]);
$productos = $st->fetchAll();
$categorias = $pdo->query('SELECT id_categoria, nombre FROM Categoria ORDER BY nombre')->fetchAll();
require 'includes/header.php';
?>
<h1>Catálogo de productos</h1>
<form class="filtros" method="GET">
  <input name="q" value="<?= e($q) ?>" placeholder="Buscar productos…">
  <select name="cat">
    <option value="0">Todas las categorías</option>
    <?php foreach ($categorias as $c): ?>
      <option value="<?= $c['id_categoria'] ?>" <?= $cat === (int)$c['id_categoria'] ? 'selected' : '' ?>><?= e($c['nombre']) ?></option>
    <?php endforeach; ?>
  </select>
  <button>Buscar</button>
</form>
<?php if (!$productos): ?><p>No hay productos con ese filtro. Prueba con otra búsqueda.</p><?php endif; ?>
<section class="grid">
<?php foreach ($productos as $p): ?>
  <article class="card">
    <small><?= e($p['categoria']) ?></small>
    <h3><?= e($p['nombre']) ?></h3>
    <p><?= e($p['descripcion']) ?></p>
    <strong><?= pesos($p['precio']) ?></strong>
    <span class="stock"><?= (int)$p['cantidad'] ?> unidades</span>
    <form action="carrito.php" method="POST">
      <input type="hidden" name="accion" value="agregar">
      <input type="hidden" name="id" value="<?= $p['id_producto'] ?>">
      <button <?= $p['cantidad'] < 1 ? 'disabled' : '' ?>>Agregar al carrito</button>
    </form>
  </article>
<?php endforeach; ?>
</section>
<?php require 'includes/footer.php'; ?>
