<?php
session_start();
include 'php/conexion.php';
include 'php/csrf.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['id'];
$usuario_nombre = $_SESSION['nombre'];

// Consultar carrito
$sql = "SELECT c.id, a.titulo, a.precio, a.imagen, a.stock, c.cantidad 
        FROM carrito c
        JOIN articulos a ON c.articulo_id = a.id
        WHERE c.usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();
$total = 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Carrito - REVOO</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header>
    <div class="navbar">
        <div class="auth">
            <span class="usuario">👤 <?php echo htmlspecialchars($usuario_nombre); ?></span>
            <a href="logout.php">Cerrar sesión</a>
        </div>
        <div class="logo">REVOO</div>
        <nav class="nav-links">
            <a href="inicio.php">Inicio</a>
            <a href="publicar.php">Publicar</a>
            <a href="carrito.php">Carrito</a>
        </nav>
    </div>
</header>

<section class="carrito">
    <h1>🛒 Tu Carrito</h1>
    <?php
        // Para mostrar conteo de ítems y total, necesitamos recorrer resultado.
        // Como ya usamos $resultado en la tabla, obtenemos los datos primero y luego renderizamos.
        $items = [];
        $resultado->data_seek(0);
        while ($r = $resultado->fetch_assoc()) { $items[] = $r; }
        $cantidad_items = count($items);
        $total = 0;
        foreach ($items as $it) { $total += (float)$it['precio'] * (int)$it['cantidad']; }
    ?>
    <?php if ($cantidad_items > 0): ?>
    <div class="resumen-carrito" style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; margin: 10px 0 14px; gap: 10px;">
        <div><strong><?php echo $cantidad_items; ?></strong> artículo(s) — <strong>Total:</strong> $<?php echo number_format($total, 2); ?></div>
        <div style="display:flex; gap:10px; align-items:center;">
            <a class="btn secondary" href="inicio.php" title="Seguir comprando">Seguir comprando</a>
            <a class="btn" href="pagar.php" title="Ir a pagar">Ir a pagar</a>
            <form action="php/vaciar_carrito.php" method="POST" style="display:inline;">
                <?php echo csrf_input(); ?>
                <button type="submit" class="btn ghost" title="Vaciar carrito">Vaciar</button>
            </form>
        </div>
    </div>
    <?php endif; ?>
    <?php if ($cantidad_items > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Título</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Stock</th>
                    <th>Subtotal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $row): ?>
                    <?php $subtotal = (float)$row['precio'] * (int)$row['cantidad']; ?>
                    <tr>
                        <td><img src="uploads/<?php echo htmlspecialchars($row['imagen'] ?? 'no-img.png'); ?>" alt="Imagen" width="60"></td>
                        <td><?php echo htmlspecialchars($row['titulo']); ?></td>
                        <td>$<?php echo number_format($row['precio'], 2); ?></td>
                        <td>
                            <form class="qty-controls" action="php/actualizar_carrito.php" method="POST" style="display:inline-flex; align-items:center; gap:6px;">
                                <?php echo csrf_input(); ?>
                                <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
                                <button type="submit" name="accion" value="restar" class="btn" title="Restar">-</button>
                                <span><?php echo (int)$row['cantidad']; ?></span>
                                <button type="submit" name="accion" value="sumar" class="btn" title="Sumar" <?php echo ((int)$row['cantidad'] >= (int)$row['stock']) ? 'disabled class="btn deshabilitado"' : ''; ?>>+</button>
                            </form>
                        </td>
                        <td><?php echo (int)$row['stock']; ?></td>
                        <td>$<?php echo number_format($subtotal, 2); ?></td>
                        <td>
                            <form action="php/actualizar_carrito.php" method="POST" style="display:inline;">
                                <?php echo csrf_input(); ?>
                                <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
                                <button type="submit" name="accion" value="eliminar" class="btn" title="Eliminar">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" style="text-align:right;">Total:</th>
                    <th>$<?php echo number_format($total, 2); ?></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    <?php else: ?>
        <p>Tu carrito está vacío.</p>
    <?php endif; ?>
</section>
</body>
</html>
