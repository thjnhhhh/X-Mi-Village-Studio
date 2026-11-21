<?php
declare(strict_types=1);
/**
 * PDO database connection helper.
 */


require_once dirname(__DIR__) . '/settings.php';

/**
 * Create and reuse a PDO connection.
 */
function getDatabaseConnection(): PDO
{
    static $connection = null;

    if ($connection instanceof PDO) {
        return $connection;
    }

    $config = getDatabaseConfig();

    if ($config['database'] === '' || $config['username'] === '') {
        throw new RuntimeException(
            'Database is not configured. Set DB_NAME and DB_USER environment variables.'
        );
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $config['host'],
        $config['port'],
        $config['database'],
        $config['charset']
    );

    $connection = new PDO(
        $dsn,
        $config['username'],
        $config['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    return $connection;
}
