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
    $item_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $accion = $_POST['accion'] ?? '';

    // Validar pertenencia del item al usuario
    $sqlChk = "SELECT c.id, c.cantidad, a.stock, c.articulo_id FROM carrito c JOIN articulos a ON c.articulo_id = a.id WHERE c.id = ? AND c.usuario_id = ?";
    $stmt = $conn->prepare($sqlChk);
    $stmt->bind_param('ii', $item_id, $usuario_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $item = $res->fetch_assoc();
    $stmt->close();

    if (!$item) {
        header('Location: ../carrito.php');
        exit;
    }

    if ($accion === 'sumar') {
        $cant = (int)$item['cantidad'];
        $stock = (int)$item['stock'];
        if ($cant < $stock) {
            $sql = "UPDATE carrito SET cantidad = cantidad + 1 WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $item_id);
            $stmt->execute();
            $stmt->close();
        }
    } elseif ($accion === 'restar') {
        if ((int)$item['cantidad'] > 1) {
            $sql = "UPDATE carrito SET cantidad = cantidad - 1 WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $item_id);
            $stmt->execute();
            $stmt->close();
        } else {
            // Si llega a 1 y se resta, eliminar
            $sql = "DELETE FROM carrito WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $item_id);
            $stmt->execute();
            $stmt->close();
        }
    } elseif ($accion === 'eliminar') {
        $sql = "DELETE FROM carrito WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $item_id);
        $stmt->execute();
        $stmt->close();
    }
}

header('Location: ../carrito.php');
exit;
