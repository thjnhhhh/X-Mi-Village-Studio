<?php
require_once __DIR__ . '/settings.php';

$currentPage = 'jobs.php';

$config = getDatabaseConfig();
$conn = new mysqli(
    $config['host'],
    $config['username'],
    $config['password'],
    $config['database'],
    (int) $config['port']
);

if ($conn->connect_error) {
    die('Database connection failed.');
}
$conn->set_charset($config['charset']);

$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    $stmt = $conn->prepare(
    'SELECT * FROM jobs
     WHERE title LIKE ? OR description LIKE ? OR job_reference LIKE ? OR salary LIKE ?
     ORDER BY job_reference'
);
$term = '%' . $search . '%';
$stmt->bind_param('ssss', $term, $term, $term, $term);
    $stmt->execute();
    $jobs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $result = $conn->query('SELECT * FROM jobs ORDER BY job_reference');
        $jobs = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Explore Front-End Web Developer and UI/UX Designer opportunities at Xì Mi Village Studio.">
    <title>Xì Mi Village Studio | Jobs</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="jobs-page">

    <a class="skip-link" href="#main-content">Skip to main content</a>

    <?php require __DIR__ . '/includes/header.php'; ?>

    <main id="main-content" class="content">

        <section class="intro">
            <h2>Current Vacancies</h2>
            <p>
                Xì Mi Village Studio is a creative agency that designs
                and builds websites, brand identities and digital content
                for clients across Vietnam. We are growing our web team
                and are looking for people who care about clean code,
                thoughtful design and accessibility.
            </p>
            <p>
                We welcome applications from Aboriginal and Torres Strait
                Islander peoples and are committed to building a workplace
                that respects and values First Nations perspectives.
            </p>
        </section>

        <aside class="how-to-apply home-section">
            <h2>How to Apply</h2>
            <p>Read each role carefully, then complete the online application form.</p>
            <ol>
                <li>Note the reference number for the role.</li>
                <li>Check that you meet the essential requirements.</li>
                <li>Complete the application form.</li>
            </ol>
            <p>
                <a class="button-link" href="apply.php">Apply Now</a>
            </p>
        </aside>

        <form method="get" action="jobs.php" class="job-search">
            <label for="search">Search jobs by title, reference, description or salary</label>
            <input type="text" id="search" name="search" value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
        </form>

        <?php if (empty($jobs)): ?>
            <p>No jobs match your search.</p>
        <?php endif; ?>

        <?php foreach ($jobs as $job): ?>
            <section class="job" id="<?= htmlspecialchars(strtolower($job['job_reference'])) ?>">
                <h2><?= htmlspecialchars($job['title']) ?></h2>
                <p>Reference: <span class="job-ref"><?= htmlspecialchars($job['job_reference']) ?></span></p>
                <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
                <p>
                    <strong>Salary:</strong> <?= htmlspecialchars($job['salary']) ?>.
                    <strong>Reports to:</strong> <?= htmlspecialchars($job['reports_to']) ?>.
                </p>

                <h3>Key Responsibilities</h3>
                <ul>
                    <?php foreach (explode(';', $job['responsibilities']) as $item): ?>
                        <?php if (trim($item) !== ''): ?>
                            <li><?= htmlspecialchars(trim($item)) ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>

                <h3>Requirements</h3>
                <h4>Essential</h4>
                <ol>
                    <?php foreach (explode(';', $job['essential_requirements']) as $item): ?>
                        <?php if (trim($item) !== ''): ?>
                            <li><?= htmlspecialchars(trim($item)) ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>

                <h4>Preferable</h4>
                <ul>
                    <?php foreach (explode(';', $job['preferable_requirements']) as $item): ?>
                        <?php if (trim($item) !== ''): ?>
                            <li><?= htmlspecialchars(trim($item)) ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>

                <p>
                    <a class="button-link" href="apply.php?job=<?= urlencode($job['job_reference']) ?>">
                        Apply for <?= htmlspecialchars($job['job_reference']) ?>
                    </a>
                </p>
            </section>
        <?php endforeach; ?>

    </main>

    <?php require __DIR__ . '/includes/footer.php'; ?>

</body>
</html>