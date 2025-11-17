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
$id = (int)($_POST['id'] ?? 0);
$titulo = trim($_POST['titulo'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$precio = (float)($_POST['precio'] ?? 0);
$stock = max(0, (int)($_POST['stock'] ?? 0));

// Verificar propiedad y traer imagen actual
$sql = "SELECT id, usuario_id, imagen FROM articulos WHERE id = ? AND usuario_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $id, $usuario_id);
$stmt->execute();
$res = $stmt->get_result();
$art = $res->fetch_assoc();
$stmt->close();

if (!$art) {
    header('Location: ../inicio.php?error=no_autorizado');
    exit;
}

$nombre_imagen = $art['imagen'];

// Si se sube nueva imagen, validarla y reemplazar
if (!empty($_FILES['imagen']['name']) && is_uploaded_file($_FILES['imagen']['tmp_name'])) {
    $permitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $tamMax = 4 * 1024 * 1024; // 4MB
    $mime = mime_content_type($_FILES['imagen']['tmp_name']);
    $size = (int)$_FILES['imagen']['size'];
    if (!isset($permitidos[$mime])) {
        http_response_code(400);
        die('Formato de imagen no permitido');
    }
    if ($size <= 0 || $size > $tamMax) {
        http_response_code(400);
        die('La imagen es muy pesada (máx 4MB)');
    }
    $ext = $permitidos[$mime];
    $carpeta = realpath(__DIR__ . '/../uploads');
    if ($carpeta === false) {
        $carpeta = __DIR__ . '/../uploads';
        @mkdir($carpeta, 0755, true);
    }
    $nuevo_nombre = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    $ruta_imagen = $carpeta . DIRECTORY_SEPARATOR . $nuevo_nombre;
    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_imagen)) {
        // Borrar imagen anterior si no es placeholder
        if (!empty($nombre_imagen) && $nombre_imagen !== 'no-img.png') {
            $ruta_old = realpath(__DIR__ . '/../uploads/' . $nombre_imagen);
            if ($ruta_old && strpos($ruta_old, realpath(__DIR__ . '/../uploads')) === 0) {
                @unlink($ruta_old);
            }
        }
        $nombre_imagen = $nuevo_nombre;
    }
}

$sqlUp = "UPDATE articulos SET titulo = ?, descripcion = ?, precio = ?, stock = ?, imagen = ? WHERE id = ? AND usuario_id = ?";
$stmt = $conn->prepare($sqlUp);
$stmt->bind_param('ssdisii', $titulo, $descripcion, $precio, $stock, $nombre_imagen, $id, $usuario_id);
$stmt->execute();
$stmt->close();

header('Location: ../articulo.php?id=' . $id . '&actualizado=ok');
exit;
