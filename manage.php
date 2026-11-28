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

$_SESSION['logout_token'] ??= bin2hex(random_bytes(32));
$currentPage = 'manage.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Applications | Xì Mi Village Studio</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <a class="skip-link" href="#main-content">Skip to main content</a>
    <?php require __DIR__ . '/includes/header.php'; ?>

    <main id="main-content" class="content">
        <section class="home-section" aria-labelledby="manage-title">
            <h2 id="manage-title">Manage Applications</h2>
            <p>You are logged in as a manager.</p>
            <form class="form-action" method="post" action="logout.php">
                <input type="hidden" name="logout_token"
                       value="<?= htmlspecialchars($_SESSION['logout_token'], ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit">Log out</button>
            </form>
        </section>
    </main>

    <?php require __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
