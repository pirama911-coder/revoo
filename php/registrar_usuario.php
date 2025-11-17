<?php
include 'conexion.php';
include 'csrf.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    csrf_verify();
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $password_plain = $_POST['password'] ?? '';

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        die('Correo inválido');
    }
    if (strlen($password_plain) < 6) {
        http_response_code(400);
        die('La contraseña debe tener al menos 6 caracteres');
    }

    $password = password_hash($password_plain, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nombre, correo, telefono, direccion, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $nombre, $correo, $telefono, $direccion, $password);

    if ($stmt->execute()) {
        header("Location: ../login.php?registro=ok");
        exit();
    } else {
        http_response_code(400);
        echo "Error: " . htmlspecialchars($conn->error);
    }
    $stmt->close();
}
$conn->close();
?>
