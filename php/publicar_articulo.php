<?php
session_start();
include 'conexion.php';
include 'csrf.php';

if (!isset($_SESSION['id'])) {
    header("Location: ../login.php?error=debes_iniciar");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    csrf_verify();
    $usuario_id = $_SESSION['id'];
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = floatval($_POST['precio'] ?? 0);
    $stock = max(0, (int)($_POST['stock'] ?? 0));

    // Manejo de imagen
    $nombre_imagen = null;
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
        $nombre_imagen = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $ruta_imagen = $carpeta . DIRECTORY_SEPARATOR . $nombre_imagen;
        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_imagen)) {
            $nombre_imagen = null; // si falla no guarda imagen
        }
    }

    $sql = "INSERT INTO articulos (usuario_id, titulo, descripcion, precio, imagen, stock) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issdsi", $usuario_id, $titulo, $descripcion, $precio, $nombre_imagen, $stock);

    if ($stmt->execute()) {
        header("Location: ../inicio.php?publicacion=ok");
        exit();
    } else {
        http_response_code(400);
        echo "Error: " . htmlspecialchars($conn->error);
    }
    $stmt->close();
}
$conn->close();
?>
