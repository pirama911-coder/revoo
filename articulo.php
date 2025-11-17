<?php
session_start();
include 'php/conexion.php';
include 'php/csrf.php';

$usuario_id = $_SESSION['id'] ?? null;
$usuario_nombre = $_SESSION['nombre'] ?? null;
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    http_response_code(404);
    echo 'Artículo no encontrado';
    exit;
}

$sql = "SELECT a.*, u.nombre AS vendedor
        FROM articulos a
        JOIN usuarios u ON a.usuario_id = u.id
        WHERE a.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$art = $res->fetch_assoc();
$stmt->close();

if (!$art) {
    http_response_code(404);
    echo 'Artículo no encontrado';
    exit;
}

$esPropio = ($usuario_id && (int)$usuario_id === (int)$art['usuario_id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($art['titulo']); ?> - REVOO</title>
    <link rel="stylesheet" href="css/estilos.css">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
      .detalle { max-width: 1100px; margin: 22px auto; padding: 0 20px; display:grid; grid-template-columns: 1.2fr .8fr; gap:22px; }
      .detalle .panel { background:#fff; border:1px solid #e2e8f0; border-radius:12px; box-shadow: 0 6px 18px rgba(16,24,40,.08); overflow:hidden; }
      .detalle .panel .body{ padding:16px; }
      .detalle .galeria img{ width:100%; height:420px; object-fit:cover; background:#f1f5f9; }
      .precio-xl{ font-size:28px; font-weight:800; color:#0F3C8A; }
      .meta{ color:#64748b; }
      @media (max-width: 900px){ .detalle{ grid-template-columns:1fr; } .detalle .galeria img{ height: 300px; } }
    </style>
</head>
<body>
<header>
    <div class="navbar">
        <div class="auth">
            <?php if ($usuario_id): ?>
                <span class="usuario">👤 <?php echo htmlspecialchars($usuario_nombre); ?></span>
                <a href="logout.php">Cerrar sesión</a>
            <?php else: ?>
                <a href="login.php">Iniciar sesión</a>
                <a href="registro.php">Registrarse</a>
            <?php endif; ?>
        </div>
        <div class="logo">REVOO</div>
        <nav class="nav-links">
            <a href="inicio.php">Inicio</a>
            <a href="publicar.php">Publicar</a>
            <a href="carrito.php">Carrito</a>
        </nav>
    </div>
</header>

<section class="detalle">
    <div class="panel galeria">
        <img src="uploads/<?php echo htmlspecialchars($art['imagen'] ?? 'no-img.png'); ?>" alt="Imagen del producto">
    </div>
    <div class="panel">
        <div class="body">
            <h1><?php echo htmlspecialchars($art['titulo']); ?></h1>
            <p class="meta">Vendedor: <?php echo htmlspecialchars($art['vendedor']); ?></p>
            <p class="precio-xl">$<?php echo number_format($art['precio'], 2); ?></p>
            <p class="meta">Stock: <?php echo (int)($art['stock'] ?? 0); ?></p>
            <p><?php echo nl2br(htmlspecialchars($art['descripcion'])); ?></p>

            <?php if ($esPropio): ?>
                <p><a class="btn secondary" href="editar.php?id=<?php echo (int)$art['id']; ?>">Editar publicación</a></p>
                <form action="php/eliminar_articulo.php" method="POST" onsubmit="return confirm('¿Eliminar este artículo? Esta acción no se puede deshacer.');">
                    <?php echo csrf_input(); ?>
                    <input type="hidden" name="id" value="<?php echo (int)$art['id']; ?>">
                    <button type="submit" class="btn ghost">Eliminar publicación</button>
                </form>
            <?php elseif ($usuario_id): ?>
                <?php if ((int)($art['stock'] ?? 0) > 0): ?>
                    <form action="php/agregar_carrito.php" method="POST">
                        <?php echo csrf_input(); ?>
                        <input type="hidden" name="articulo_id" value="<?php echo (int)$art['id']; ?>">
                        <button type="submit" class="btn">Agregar al carrito</button>
                    </form>
                <?php else: ?>
                    <button class="btn deshabilitado" disabled>Sin stock</button>
                <?php endif; ?>
            <?php else: ?>
                <button class="btn deshabilitado" disabled>Debes iniciar sesión</button>
            <?php endif; ?>
        </div>
    </div>
</section>

</body>
</html>