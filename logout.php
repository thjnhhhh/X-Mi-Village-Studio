<?php
session_start([
    'use_strict_mode' => 1,
    'cookie_httponly' => 1,
    'cookie_samesite' => 'Lax',
    'cookie_secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
]);

if (empty($_SESSION['manager_id'])) {
    header('Location: login.php', true, 303);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Use the Log out button on the management page.');
}

$expectedToken = $_SESSION['logout_token'] ?? null;
$submittedToken = $_POST['logout_token'] ?? null;
if (!is_string($expectedToken) || !is_string($submittedToken) ||
    !hash_equals($expectedToken, $submittedToken)) {
    http_response_code(403);
    exit('Open the management page and try logging out again.');
}

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $cookie = session_get_cookie_params();
    unset($cookie['lifetime']);
    $cookie['expires'] = time() - 3600;
    setcookie(session_name(), '', $cookie);
}
session_destroy();

header('Location: login.php', true, 303);
exit;
