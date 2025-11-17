<?php
$host = "localhost"; // El que te dé InfinityFree
$usuario = "u631217484_User_Revoo"; // Tu usuario exacto
$clave = "Revoo123456";     // La clave que creaste
$bd = "u631217484_Revoo"; // Nombre exacto de la BD

$conn = mysqli_connect($host, $usuario, $clave, $bd);

if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}
// Establecer charset para evitar problemas de codificación y reforzar seguridad frente a inyección basada en charset
mysqli_set_charset($conn, 'utf8mb4');
?>
