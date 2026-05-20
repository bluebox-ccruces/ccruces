<?php

require_once __DIR__ . '/includes/bootstrap.php';

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}

session_destroy();
start_secure_session();
session_regenerate_id(true);

set_flash('success', 'Sesión cerrada correctamente.');
header('Location: ' . app_url('index.php'));
exit;
