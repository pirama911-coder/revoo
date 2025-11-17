<?php
session_start();
include 'php/conexion.php';
include 'php/csrf.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = (int)$_SESSION['id'];
$usuario_nombre = $_SESSION['nombre'] ?? '';

// Obtener items del carrito
$sql = "SELECT c.id, a.titulo, a.precio, a.imagen, c.cantidad 
        FROM carrito c
        JOIN articulos a ON c.articulo_id = a.id
        WHERE c.usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $usuario_id);
$stmt->execute();
$res = $stmt->get_result();
$items = [];
$total = 0;
while ($r = $res->fetch_assoc()) { $items[] = $r; $total += (float)$r['precio'] * (int)$r['cantidad']; }
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pago - REVOO</title>
    <link rel="stylesheet" href="css/estilos.css">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
      .checkout { max-width: 900px; margin: 22px auto; padding: 0 20px; display:grid; grid-template-columns: 1.4fr .8fr; gap:18px; }
      .panel { background:#fff; border:1px solid #e2e8f0; border-radius:12px; box-shadow: 0 6px 18px rgba(16,24,40,.08); }
      .panel h2{ margin:0; padding:16px 16px 0; }
      .panel .body{ padding:16px; }
      .resumen-total { font-size:20px; font-weight:800; }
      @media (max-width: 880px){ .checkout{ grid-template-columns: 1fr; } }
    </style>
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
            <a href="index.php">Inicio</a>
            <a href="publicar.php">Publicar</a>
            <a href="carrito.php">Carrito</a>
        </nav>
    </div>
</header>

<section class="checkout">
    <div class="panel">
        <h2>Resumen del pedido</h2>
        <div class="body">
            <?php if (count($items) === 0): ?>
                <p>Tu carrito está vacío. <a href="index.php">Seguir comprando</a></p>
            <?php else: ?>
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f1f5f9;">
                            <th style="text-align:left; padding:10px">Producto</th>
                            <th style="text-align:right; padding:10px">Cantidad</th>
                            <th style="text-align:right; padding:10px">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $it): $sub = (float)$it['precio']*(int)$it['cantidad']; ?>
                            <tr>
                                <td style="padding:10px;">
                                    <strong><?php echo htmlspecialchars($it['titulo']); ?></strong><br>
                                    <small>$<?php echo number_format($it['precio'],2); ?></small>
                                </td>
                                <td style="text-align:right; padding:10px;"><?php echo (int)$it['cantidad']; ?></td>
                                <td style="text-align:right; padding:10px;">$<?php echo number_format($sub,2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" style="text-align:right; padding:10px;">Total:</th>
                            <th style="text-align:right; padding:10px;">$<?php echo number_format($total,2); ?></th>
                        </tr>
                    </tfoot>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <div class="panel">
        <h2>Pagar</h2>
        <div class="body">
            <?php if (count($items) === 0): ?>
                <p>Agrega productos para continuar.</p>
            <?php else: ?>
                <p class="resumen-total">Total a pagar: $<?php echo number_format($total,2); ?></p>
                <form action="#" method="POST" onsubmit="alert('Demo: integra tu pasarela (e.g. PayU, MercadoPago, Stripe)'); return false;">
                    <?php echo csrf_input(); ?>
                    <button type="submit" class="btn" style="width:100%;">Confirmar pago</button>
                </form>
                <p style="margin-top:10px; text-align:center;"><a class="btn secondary" href="index.php">Seguir comprando</a></p>
            <?php endif; ?>
        </div>
    </div>
</section>

</body>
</html>