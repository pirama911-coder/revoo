<?php
include 'php/conexion.php';
include 'php/csrf.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - REVOO</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
include 'php/csrf.php';
        <h2>Crear cuenta en REVOO</h2>
        <form action="php/registrar_usuario.php" method="POST">
            <?php echo csrf_input(); ?>
            <label for="nombre">Nombre completo:</label>
            <input type="text" name="nombre" id="nombre" required>

            <label for="correo">Correo electrónico:</label>
            <input type="email" name="correo" id="correo" required>

            <label for="telefono">Teléfono:</label>
            <input type="text" name="telefono" id="telefono" required>

                <?php echo csrf_input(); ?>
            <label for="correo">Correo electrónico:</label>
            <input type="text" name="direccion" id="direccion" required>

            <label for="direccion">Direccion:</label>
            <input type="password" name="password" id="password" required>

            <button type="submit" class="btn">Registrarme</button>
        </form>
        <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
    </div>
</body>
</html>
