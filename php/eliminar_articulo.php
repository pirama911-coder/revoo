<?php
session_start();
include 'conexion.php';
include 'csrf.php';

if (!isset($_SESSION['id'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../inicio.php');
    exit;
}

csrf_verify();
$usuario_id = (int)$_SESSION['id'];
$articulo_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

// Verificar que el artículo pertenezca al usuario y obtener imagen
$sql = "SELECT id, usuario_id, imagen FROM articulos WHERE id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $articulo_id);
$stmt->execute();
$res = $stmt->get_result();
$art = $res->fetch_assoc();
$stmt->close();

if (!$art || (int)$art['usuario_id'] !== $usuario_id) {
    header('Location: ../inicio.php?error=no_autorizado');
    exit;
}

// Borrar referencias del carrito
$sqlDelCar = "DELETE FROM carrito WHERE articulo_id = ?";
$stmt = $conn->prepare($sqlDelCar);
$stmt->bind_param('i', $articulo_id);
$stmt->execute();
$stmt->close();

// Borrar artículo
$sqlDel = "DELETE FROM articulos WHERE id = ?";
$stmt = $conn->prepare($sqlDel);
$stmt->bind_param('i', $articulo_id);
$ok = $stmt->execute();
$stmt->close();

// Borrar imagen física si existe y no es placeholder
if ($ok && !empty($art['imagen']) && $art['imagen'] !== 'no-img.png') {
    $ruta = realpath(__DIR__ . '/../uploads/' . $art['imagen']);
    if ($ruta && strpos($ruta, realpath(__DIR__ . '/../uploads')) === 0) {
        @unlink($ruta);
    }
}

header('Location: ../inicio.php?eliminado=ok');
exit;
