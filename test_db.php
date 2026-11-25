<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

try {
    require_once __DIR__ . '/includes/db.php';

    $connection = getDatabaseConnection();

    echo '<h2>MySQL Connection Successful!</h2>';
    echo '<p>Database: ' . htmlspecialchars(DB_NAME, ENT_QUOTES, 'UTF-8') . '</p>';

    foreach (['eoi', 'jobs', 'about'] as $table) {
        $stmt = $connection->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        if (!$stmt->fetch()) {
            throw new RuntimeException("Missing table: {$table}");
        }
    }
    echo '<p>Tables check: OK (eoi, jobs, about)</p>';

    // Create
    $stmt = $connection->prepare("INSERT INTO eoi (
        job_reference, first_name, last_name, date_of_birth, gender,
        street_address, suburb, state, postcode, email, phone, skills, other_skills, status
    ) VALUES ('FE001', 'Test', 'User', '01/01/2004', 'Other', '1 St', 'Sub', 'VIC', '3000', 't@test.com', '0900000000', 'Teamwork, Communication', 'Test', 'New')");
    $stmt->execute();
    $id = (int) $connection->lastInsertId();
    echo '<p>Create: OK (ID #' . $id . ')</p>';

    // Read
    $stmt = $connection->prepare("SELECT first_name, status FROM eoi WHERE EOInumber = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo '<p>Read: OK (' . htmlspecialchars($row['first_name'] . ' - ' . $row['status'], ENT_QUOTES, 'UTF-8') . ')</p>';

    // Update
    $stmt = $connection->prepare("UPDATE eoi SET status = 'Current' WHERE EOInumber = ?");
    $stmt->execute([$id]);
    echo '<p>Update: OK</p>';

    // Delete
    $stmt = $connection->prepare("DELETE FROM eoi WHERE EOInumber = ?");
    $stmt->execute([$id]);
    echo '<p>Delete: OK</p>';

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