<?php
// Utilidades simples de protección CSRF

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_input(): string {
    $t = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="csrf_token" value="' . $t . '">';
}

function csrf_verify(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sent = $_POST['csrf_token'] ?? '';
        $sess = $_SESSION['csrf_token'] ?? '';
        if (!$sent || !$sess || !hash_equals($sess, $sent)) {
            http_response_code(400);
            die('Solicitud inválida (CSRF)');
        }
    }
}

?>
