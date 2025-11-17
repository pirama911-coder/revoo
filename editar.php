<?php
session_start();
include 'php/conexion.php';
include 'php/csrf.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = (int)$_SESSION['id'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT * FROM articulos WHERE id = ? AND usuario_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $id, $usuario_id);
$stmt->execute();
$res = $stmt->get_result();
$art = $res->fetch_assoc();
$stmt->close();

if (!$art) {
    header('Location: index.php?error=no_autorizado');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar artículo - REVOO</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="contenedor-form">
        <h2>Editar publicación</h2>
        <form action="php/actualizar_articulo.php" method="POST" enctype="multipart/form-data">
            <?php echo csrf_input(); ?>
            <input type="hidden" name="id" value="<?php echo (int)$art['id']; ?>">

            <label for="titulo">Título:</label>
            <input type="text" name="titulo" id="titulo" value="<?php echo htmlspecialchars($art['titulo']); ?>" required>

            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" id="descripcion" rows="4" required><?php echo htmlspecialchars($art['descripcion']); ?></textarea>

            <label for="precio">Precio (COP):</label>
            <input type="number" name="precio" id="precio" step="0.01" value="<?php echo htmlspecialchars($art['precio']); ?>" required>

            <label for="stock">Stock disponible:</label>
            <input type="number" name="stock" id="stock" min="0" step="1" value="<?php echo (int)$art['stock']; ?>" required>

            <label>Imagen actual:</label>
            <div>
                <img src="uploads/<?php echo htmlspecialchars($art['imagen'] ?? 'no-img.png'); ?>" alt="Imagen actual" style="max-width:160px; border-radius:8px;">
            </div>
            <label for="imagen">Reemplazar imagen (opcional):</label>
            <input type="file" name="imagen" id="imagen" accept="image/*">

            <button type="submit" class="btn">Guardar cambios</button>
        </form>
        <p><a href="articulo.php?id=<?php echo (int)$art['id']; ?>">⬅ Volver al detalle</a></p>
    </div>
</body>
</html>