<?php
include 'php/conexion.php';
include 'php/csrf.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - REVOO</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="contenedor-form">
        <h2>Iniciar Sesión</h2>
        <form action="php/login_usuario.php" method="POST">
            <?php echo csrf_input(); ?>
            <label for="correo">Correo electrónico:</label>
            <input type="email" name="correo" id="correo" required>

            <label for="password">Contraseña:</label>
            <input type="password" name="password" id="password" required>

            <button type="submit" class="btn">Ingresar</button>
        </form>
        <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
    </div>
</body>
</html>
