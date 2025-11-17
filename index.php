<?php
session_start();
include 'php/conexion.php';
include 'php/csrf.php';

// Verificar si hay sesión activa
$usuario_nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : null;
$usuario_id = isset($_SESSION['id']) ? $_SESSION['id'] : null;

// Obtener artículos publicados (búsqueda opcional + filtro "Mis artículos")
$q = trim($_GET['q'] ?? '');
$solo_mis = isset($_GET['mis']) && $_GET['mis'] === '1' && $usuario_id;

if ($q !== '' && $solo_mis) {
    $sql = "SELECT a.*, u.nombre AS vendedor
        FROM articulos a
        JOIN usuarios u ON a.usuario_id = u.id
        WHERE (a.titulo LIKE ? OR a.descripcion LIKE ?) AND a.usuario_id = ?
        ORDER BY a.fecha_publicacion DESC";
    $stmt = $conn->prepare($sql);
    $like = "%$q%";
    $stmt->bind_param("ssi", $like, $like, $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
} elseif ($q !== '') {
    $sql = "SELECT a.*, u.nombre AS vendedor
        FROM articulos a
        JOIN usuarios u ON a.usuario_id = u.id
        WHERE a.titulo LIKE ? OR a.descripcion LIKE ?
        ORDER BY a.fecha_publicacion DESC";
    $stmt = $conn->prepare($sql);
    $like = "%$q%";
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $resultado = $stmt->get_result();
} elseif ($solo_mis) {
    $sql = "SELECT a.*, u.nombre AS vendedor
        FROM articulos a
        JOIN usuarios u ON a.usuario_id = u.id
        WHERE a.usuario_id = ?
        ORDER BY a.fecha_publicacion DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
} else {
    $sql = "SELECT a.*, u.nombre AS vendedor 
        FROM articulos a 
        JOIN usuarios u ON a.usuario_id = u.id 
        ORDER BY a.fecha_publicacion DESC";
    $resultado = $conn->query($sql);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Tienda - Artículos de Segunda Mano</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <!-- Barra de navegación -->
<header>
    <div class="navbar">
        <!-- Grupo izquierda -->
        <div class="auth">
            <?php if ($usuario_nombre): ?>
                <span class="usuario">👤 <?php echo htmlspecialchars($usuario_nombre); ?></span>
                <a href="logout.php">Cerrar sesión</a>
            <?php else: ?>
                <a href="login.php">Iniciar sesión</a>
                <a href="registro.php">Registrarse</a>
            <?php endif; ?>
        </div>

        <!-- Logo centrado -->
        <div class="logo">REVOO</div>

        <!-- Grupo derecha -->
        <nav class="nav-links">
            <a href="index.php">Inicio</a>
            <a href="publicar.php">Publicar</a>
            <a href="carrito.php">Carrito</a>
            <?php if ($usuario_id): ?>
                <a href="index.php?mis=1">Mis artículos</a>
            <?php endif; ?>
        </nav>
    </div>
</header>


    <!-- Bienvenida -->
    <section class="bienvenida">
        <h1>Bienvenido a <span class="marca">REVOO</span></h1>
        <p>Compra y vende artículos de segunda mano de forma fácil y rápida.</p>
        <form class="buscador" action="index.php" method="GET">
            <input type="text" name="q" placeholder="Buscar productos..." value="<?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>">
            <?php if ($usuario_id): ?>
            <label style="display:flex; align-items:center; gap:6px; font-size:14px; color:#475569;">
                <input type="checkbox" name="mis" value="1" <?php echo $solo_mis ? 'checked' : ''; ?>> Mis artículos
            </label>
            <?php endif; ?>
            <button type="submit" class="btn">Buscar</button>
        </form>
    </section>

    <!-- Listado de artículos -->
    <section class="articulos">
        <h2>
            <?php if ($solo_mis && $q !== ''): ?>
                Mis artículos que coinciden con "<?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>"
            <?php elseif ($solo_mis): ?>
                Mis artículos
            <?php elseif ($q !== ''): ?>
                Resultados para "<?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>"
            <?php else: ?>
                Artículos publicados
            <?php endif; ?>
        </h2>
        <div class="grid-articulos">
            <?php if ($resultado->num_rows > 0): ?>
                <?php while ($row = $resultado->fetch_assoc()): ?>
                    <div class="articulo">
                        <a href="articulo.php?id=<?php echo (int)$row['id']; ?>">
                            <img src="uploads/<?php echo htmlspecialchars($row['imagen'] ?? 'no-img.png'); ?>" alt="Imagen">
                        </a>
                        <h3><a href="articulo.php?id=<?php echo (int)$row['id']; ?>" style="color:inherit; text-decoration:none;"><?php echo htmlspecialchars($row['titulo']); ?></a></h3>
                        <p><?php echo htmlspecialchars($row['descripcion']); ?></p>
                        <p class="precio">$<?php echo number_format($row['precio'], 2); ?></p>
                        <p class="vendedor">Vendedor: <?php echo htmlspecialchars($row['vendedor']); ?></p>
                        <p class="vendedor">Stock: <?php echo isset($row['stock']) ? (int)$row['stock'] : 0; ?></p>

                        <!-- Lógica de botones -->
                        <?php if ($usuario_id && $usuario_id == $row['usuario_id']): ?>
                            <p class="propio">📌 Este artículo es tuyo</p>
                        <?php elseif ($usuario_id): ?>
                            <?php if ((int)($row['stock'] ?? 0) > 0): ?>
                            <form action="php/agregar_carrito.php" method="POST">
                                <?php echo csrf_input(); ?>
                                <input type="hidden" name="articulo_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn">Agregar al carrito</button>
                            </form>
                            <?php else: ?>
                                <button class="btn deshabilitado" disabled>Sin stock</button>
                            <?php endif; ?>
                        <?php else: ?>
                            <button class="btn deshabilitado" disabled>Debes iniciar sesión</button>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No hay artículos publicados todavía.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- FOOTER -->
<footer class="footer">
  <p>© 2025 REVOO - Todos los derechos reservados</p>
  <div class="footer-links">
    <a href="#">Ayuda</a>
    <a href="#">Contacto</a>
    <a href="#">Políticas</a>
  </div>
</footer>

</body>
</html>
