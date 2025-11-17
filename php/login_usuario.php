<?php
session_start();
include 'conexion.php';
include 'csrf.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    csrf_verify();
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    // Consulta preparada
    $sql = "SELECT id, nombre, correo, password FROM usuarios WHERE correo = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();

        if (password_verify($password, $usuario['password'])) {
            // Guardar datos de sesión
            session_regenerate_id(true);
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['correo'] = $usuario['correo'];

            header("Location: ../inicio.php");
            exit();
        } else {
            http_response_code(401);
            echo "Credenciales inválidas.";
        }
    } else {
        http_response_code(401);
        echo "Credenciales inválidas.";
    }
    $stmt->close();
}
$conn->close();
?>
