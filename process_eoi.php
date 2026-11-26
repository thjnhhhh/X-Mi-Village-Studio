<?php

require_once __DIR__ . '/includes/db.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: apply.php', true, 303);
    exit;
}

$fields = [
    'job_reference' => ['Job reference number', '/\A[A-Za-z0-9]{5}\z/'],
    'first_name' => ['First name', "/\\A[\\p{L}\\p{M} '\\-]{1,20}\\z/u"],
    'last_name' => ['Last name', "/\\A[\\p{L}\\p{M} '\\-]{1,20}\\z/u"],
    'date_of_birth' => ['Date of birth', '/\A(?:0[1-9]|[12][0-9]|3[01])\/(?:0[1-9]|1[0-2])\/[0-9]{4}\z/'],
    'gender' => ['Gender', '/\A(?:Male|Female|Other|Prefer not to say)\z/'],
    'street_address' => ['Street address', '/\A[^\r\n]{1,40}\z/u'],
    'suburb' => ['Suburb/Town', '/\A[^\r\n]{1,40}\z/u'],
    'state' => ['State', '/\A(?:VIC|NSW|QLD|NT|WA|SA|TAS|ACT)\z/'],
    'postcode' => ['Postcode', '/\A[0-9]{4}\z/'],
    'email' => ['Email', null],
    'phone' => ['Phone number', '/\A\+?[0-9]{8,12}\z/'],
];

$application = [];
$errors = [];
foreach ($fields as $field => [$label, $pattern]) {
    $submitted = $_POST[$field] ?? '';
    $value = is_string($submitted) ? trim(stripslashes($submitted)) : '';
    if ($value === '') {
        $errors[] = "$label is required.";
        continue;
    }
    if ($pattern !== null && preg_match($pattern, $value) !== 1) {
        $errors[] = "$label has an invalid format.";
        continue;
    }
    $application[$field] = $value;
}

if (isset($application['email'])) {
    $email = $application['email'];
    if (strlen($email) > 254 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $errors[] = 'Enter a valid email address.';
    }
}

if (isset($application['date_of_birth'])) {
    [$day, $month, $year] = explode('/', $application['date_of_birth']);
    if (!checkdate((int) $month, (int) $day, (int) $year)) {
        $errors[] = 'Enter a valid date of birth.';
    } elseif ("$year-$month-$day" > date('Y-m-d')) {
        $errors[] = 'Date of birth cannot be in the future.';
    }
}

$allowedSkills = [
    'Web Design',
    'Branding',
    'Digital Content Services',
    'Front-End Development',
    'UI/UX Design',
    ];
$skills = $_POST['skills'] ?? [];
if (!is_array($skills) || count($skills) !== count(array_filter($skills, 'is_string'))) {
    $errors[] = 'Select skills from the options on the form.';
    $skills = [];
}
if (array_diff($skills, $allowedSkills) !== []) {
    $errors[] = 'Select skills from the options on the form.';
}
$application['skills'] = implode(', ', array_unique($skills));

$otherSkills = $_POST['other_skills'] ?? '';
if (!is_string($otherSkills)) {
    $errors[] = 'Enter other skills as text.';
    $otherSkills = '';
}
$otherSkills = trim(stripslashes($otherSkills));
if (preg_match('//u', $otherSkills) !== 1 || strlen($otherSkills) > 65535) {
    $errors[] = 'Other skills contains invalid text or is too long.';
}
$application['other_skills'] = $otherSkills === '' ? null : $otherSkills;

$eoiNumber = null;
if ($errors !== []) {
    http_response_code(422);
} else {
    try {
        $database = getDatabaseConnection();
        $database->exec(<<<'SQL'
            CREATE TABLE IF NOT EXISTS eoi (
                EOInumber INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                job_reference VARCHAR(10) NOT NULL,
                first_name VARCHAR(20) NOT NULL,
                last_name VARCHAR(20) NOT NULL,
                date_of_birth VARCHAR(10) NOT NULL,
                gender VARCHAR(20) NOT NULL,
                street_address VARCHAR(40) NOT NULL,
                suburb VARCHAR(40) NOT NULL,
                state VARCHAR(10) NOT NULL,
                postcode VARCHAR(4) NOT NULL,
                email VARCHAR(254) NOT NULL,
                phone VARCHAR(13) NOT NULL,
                skills VARCHAR(255) DEFAULT '',
                other_skills TEXT DEFAULT NULL,
                status ENUM('New', 'Current', 'Final') NOT NULL DEFAULT 'New'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
            SQL);

        $statement = $database->prepare(<<<'SQL'
            INSERT INTO eoi (
                job_reference, first_name, last_name, date_of_birth, gender,
                street_address, suburb, state, postcode, email, phone, skills, other_skills
            ) VALUES (
                :job_reference, :first_name, :last_name, :date_of_birth, :gender,
                :street_address, :suburb, :state, :postcode, :email, :phone, :skills, :other_skills
            )
            SQL);
        $statement->execute($application);
        $eoiNumber = $database->lastInsertId();
    } catch (PDOException $exception) {
        http_response_code(500);
        error_log('Unable to save EOI. PDO error code: ' . $exception->getCode());
        $errors[] = 'We could not save your application. Please try again later.';
    }
}

$currentPage = 'apply.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xì Mi Village Studio | Application Result</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php require __DIR__ . '/includes/header.php'; ?>
    <main id="main-content" class="content">
        <section class="home-section">
            <?php if ($eoiNumber !== null): ?>
                <h2>Application submitted</h2>
                <p>Your EOI number is <strong><?= htmlspecialchars($eoiNumber, ENT_QUOTES, 'UTF-8') ?></strong>.</p>
            <?php else: ?>
                <h2>Application could not be submitted</h2>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <a class="button-link" href="apply.php">Back to Apply</a>
        </section>
    </main>
    <?php require __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
