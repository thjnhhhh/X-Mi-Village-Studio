<?php
declare(strict_types=1);
/**
 * Central application configuration.
 * Database credentials must be supplied through environment variables.
 */

// application settings.
define('APP_NAME', 'Xì Mi Village Studio');
define('APP_ENV', getenv('APP_ENV') ?: 'development');
define('APP_DEBUG', filter_var(
    getenv('APP_DEBUG') ?: 'false',
    FILTER_VALIDATE_BOOLEAN
));
define('APP_TIMEZONE', getenv('APP_TIMEZONE') ?: 'Asia/Ho_Chi_Minh');

// database settings.
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'ximi_village_studio');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');

// set timezone.
date_default_timezone_set(APP_TIMEZONE);

// error handling.
ini_set('display_errors', APP_DEBUG ? '1' : '0');
ini_set('log_errors', '1');

/**
 * return database configuration.
 */
function getDatabaseConfig(): array
{
    return [
        'host' => DB_HOST,
        'port' => DB_PORT,
        'database' => DB_NAME,
        'username' => DB_USER,
        'password' => DB_PASS,
        'charset' => DB_CHARSET,
    ];
}
