<?php
session_start();
include 'conexion.php';
include 'csrf.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit();
}

$usuario_id = (int)($_SESSION['id'] ?? 0);
csrf_verify();
$articulo_id = isset($_POST['articulo_id']) ? (int)$_POST['articulo_id'] : null;

if ($articulo_id) {
    // Anti-doble clic: ignora repetidos del mismo artículo en <1.2s
    $now = microtime(true);
    $last = $_SESSION['last_add'] ?? null;
    if ($last && isset($last['articulo_id'], $last['ts']) && (int)$last['articulo_id'] === $articulo_id && ($now - (float)$last['ts']) < 1.2) {
        header("Location: ../carrito.php");
        exit();
    }
    // Obtener stock disponible del artículo
    $sqlStock = "SELECT stock FROM articulos WHERE id = ?";
    $st = $conn->prepare($sqlStock);
    $st->bind_param("i", $articulo_id);
    $st->execute();
    $resSt = $st->get_result();
    $art = $resSt->fetch_assoc();
    $st->close();

    if (!$art) {
        header("Location: ../carrito.php?error=articulo_no_encontrado");
        exit();
    }
    $stock = (int)$art['stock'];
    if ($stock <= 0) {
        header("Location: ../carrito.php?error=sin_stock");
        exit();
    }

    // Verificar cantidad actual en el carrito
    $sqlSel = "SELECT id, cantidad FROM carrito WHERE usuario_id = ? AND articulo_id = ?";
    $stmt = $conn->prepare($sqlSel);
    $stmt->bind_param("ii", $usuario_id, $articulo_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $id = (int)$row['id'];
        $cantidad = (int)$row['cantidad'];
        if ($cantidad >= $stock) {
            header("Location: ../carrito.php?error=limite_stock");
            exit();
        }
        $sqlUpd = "UPDATE carrito SET cantidad = cantidad + 1 WHERE id = ?";
        $stmt2 = $conn->prepare($sqlUpd);
        $stmt2->bind_param("i", $id);
        $stmt2->execute();
        $stmt2->close();
    } else {
        $sqlIns = "INSERT INTO carrito (usuario_id, articulo_id, cantidad) VALUES (?, ?, 1)";
        $stmt2 = $conn->prepare($sqlIns);
        $stmt2->bind_param("ii", $usuario_id, $articulo_id);
        $stmt2->execute();
        $stmt2->close();
    }
    $stmt->close();

    // Marcar última adición para evitar doble proceso
    $_SESSION['last_add'] = ['articulo_id' => $articulo_id, 'ts' => $now];
}

// Redirigir al carrito
header("Location: ../carrito.php");
exit();
