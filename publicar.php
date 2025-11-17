<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php?error=debes_iniciar");
    exit();
}
include 'php/csrf.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Publicar Artículo - REVOO</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="contenedor-form">
        <h2>Publicar artículo</h2>
        <form action="php/publicar_articulo.php" method="POST" enctype="multipart/form-data">
            <?php echo csrf_input(); ?>
            <label for="titulo">Título:</label>
            <input type="text" name="titulo" id="titulo" required>

            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" id="descripcion" rows="4" required></textarea>

            <label for="precio">Precio (COP):</label>
            <input type="number" name="precio" id="precio" step="0.01" required>

            <label for="stock">Stock disponible:</label>
            <input type="number" name="stock" id="stock" min="0" step="1" value="0" required>

            <label for="imagen">Imagen del producto:</label>
            <input type="file" name="imagen" id="imagen" accept="image/*">

            <button type="submit" class="btn">Publicar</button>
        </form>
        <p><a href="index.php">⬅ Volver al inicio</a></p>
    </div>
</body>
</html>

