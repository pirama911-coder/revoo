<?php
session_start();
include 'conexion.php';
include 'csrf.php';

if (!isset($_SESSION['id'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $usuario_id = (int)$_SESSION['id'];
    $sql = "DELETE FROM carrito WHERE usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $usuario_id);
    $stmt->execute();
    $stmt->close();
}

header('Location: ../carrito.php');
exit;
