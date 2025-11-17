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
    <style>
        body {
            padding: 4rem;
        }
        
        .btn {
            margin-top: 2rem;
        }

        /* Estilos responsivos */
        @media (max-width: 768px) {
            body {
                padding: 2rem 1rem;
            }
            
            h2 {
                font-size: 2rem;
            }

            label {
                font-size: 1.3rem;
            }
            
            form {
                width: 100%;
            }
            
            input, button {
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 1rem 0.5rem;
            }
            
            h2 {
                font-size: 2rem;
            }
            
            label {
                font-size: 1.3rem;
            }
            
            input, button {
                font-size: 1rem;
                padding: 0.6rem;
            }
            
            .btn {
                margin-top: 1.5rem;
                font-size: 1rem;
            }
            
            p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
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
            <label for="correo">Direccion:</label>
            <input type="text" name="direccion" id="direccion" required>

            <label for="direccion">Contraseña:</label>
            <input type="password" name="password" id="password" required>

            <button type="submit" class="btn">Registrarme</button>
        </form>
        <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
    </div>
</body>
</html>
