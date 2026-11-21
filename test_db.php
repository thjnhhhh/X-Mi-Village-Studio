<?php

ini_set('display_errors', '1');
error_reporting(E_ALL);

try {
    require_once __DIR__ . '/includes/db.php';

    $connection = getDatabaseConnection();

    echo '<h2>MySQL Connection Successful!</h2>';
    echo '<p>Database: ' . htmlspecialchars(DB_NAME) . '</p>';

} catch (Throwable $e) {

    http_response_code(500);

    echo '<h2>MySQL Connection Failed!</h2>';

    echo '<pre>';
    echo htmlspecialchars(
        get_class($e) . ': ' . $e->getMessage(),
        ENT_QUOTES,
        'UTF-8'
    );
    echo '</pre>';
}