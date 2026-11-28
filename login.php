<?php
session_start([
    'use_strict_mode' => 1,
    'cookie_httponly' => 1,
    'cookie_samesite' => 'Lax',
    'cookie_secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
]);

require_once __DIR__ . '/includes/db.php';

$_SESSION['login_token'] ??= bin2hex(random_bytes(32));
$error = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $token = $_POST['login_token'] ?? '';

    if (!is_string($token) || !hash_equals($_SESSION['login_token'], $token)) {
        $error = 'Please reload the page and try again.';
    } elseif (!is_string($username) || !is_string($password)) {
        $error = 'Invalid username or password.';
    } else {
        try {
            $statement = getDatabaseConnection()->prepare(
                'SELECT id, password_hash FROM users WHERE username = :username'
            );
            $statement->execute(['username' => trim($username)]);
            $user = $statement->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                if (!session_regenerate_id(true)) {
                    http_response_code(500);
                    $error = 'Login is temporarily unavailable.';
                } else {
                    $_SESSION['manager_id'] = (int) $user['id'];
                    unset($_SESSION['login_token']);
                    header('Location: manage.php', true, 303);
                    exit;
                }
            } else {
                $error = 'Invalid username or password.';
            }
        } catch (PDOException $exception) {
            error_log('Manager login failed. PDO code: ' . $exception->getCode());
            http_response_code(500);
            $error = 'Login is temporarily unavailable.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Login | Xì Mi Village Studio</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <main class="manager-login">
        <section class="manager-login__card" aria-labelledby="manager-login-title">
            <a class="manager-login__logo" href="index.php"
               aria-label="Xì Mi Village Studio home">
                <img src="images/logo.png" alt="">
            </a>

            <h1 id="manager-login-title">Manager Login</h1>

            <?php if ($error !== ''): ?>
                <p class="manager-login__error" role="alert">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php endif; ?>

            <form class="manager-login__form" method="post" action="login.php">
                <input type="hidden" name="login_token"
                       value="<?= htmlspecialchars($_SESSION['login_token'], ENT_QUOTES, 'UTF-8') ?>">

                <div class="form-card">
                    <label for="username">Username</label>
                    <input id="username" name="username"
                           autocomplete="username" required>
                </div>

                <div class="form-card">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password"
                           autocomplete="current-password" required>
                </div>

                <button type="submit">Log in</button>
            </form>
        </section>
    </main>
</body>
</html>