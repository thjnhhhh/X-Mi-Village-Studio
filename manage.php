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
$_SESSION['manage_token'] ??= bin2hex(random_bytes(32));
$currentPage = 'manage.php';

require_once __DIR__ . '/includes/db.php';

function manageInput(array $source, string $name): string
{
    $value = $source[$name] ?? '';
    return is_string($value) ? trim($value) : '';
}

function manageEscape($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$statuses = ['New', 'Current', 'Final'];
$sortFields = [
    'EOInumber' => 'EOI number',
    'job_reference' => 'Job reference',
    'first_name' => 'First name',
    'last_name' => 'Last name',
    'status' => 'Status',
];

$filters = [];
foreach (['job_reference', 'first_name', 'last_name'] as $column) {
    $value = manageInput($_GET, $column);
    if ($value !== '') {
        $filters[$column] = $value;
    }
}
$sort = manageInput($_GET, 'sort');
if (!isset($sortFields[$sort])) {
    $sort = 'EOInumber';
}

$query = $filters;
if ($sort !== 'EOInumber') {
    $query['sort'] = $sort;
}
$resultsUrl = $query === [] ? 'manage.php' : 'manage.php?' . http_build_query($query);
$isPost = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
$action = $isPost ? manageInput($_POST, 'action') : '';
$notice = $_SESSION['manage_notice'] ?? '';
unset($_SESSION['manage_notice']);
$error = '';
$deleteReference = '';
$confirmation = null;
$applications = [];
$listAvailable = false;

$detailFields = [
    'date_of_birth' => 'Date of birth',
    'gender' => 'Gender',
    'street_address' => 'Street address',
    'suburb' => 'Suburb/Town',
    'state' => 'State',
    'postcode' => 'Postcode',
    'email' => 'Email',
    'phone' => 'Phone',
    'skills' => 'Skills',
    'other_skills' => 'Other skills',
];

if ($isPost && !hash_equals($_SESSION['manage_token'], manageInput($_POST, 'manage_token'))) {
    http_response_code(403);
    $error = 'This request could not be verified. Please try again.';
}

try {
    $database = getDatabaseConnection();

    if ($isPost && $error === '') {
        switch ($action) {
            case 'update_status':
                $eoiNumber = filter_var(manageInput($_POST, 'eoi_number'), FILTER_VALIDATE_INT, [
                    'options' => ['min_range' => 1],
                ]);
                $status = manageInput($_POST, 'status');
                if ($eoiNumber === false || !in_array($status, $statuses, true)) {
                    http_response_code(422);
                    $error = 'Choose a valid EOI and status.';
                    break;
                }

                $statement = $database->prepare(
                    'UPDATE eoi SET status = :status WHERE EOInumber = :eoi_number'
                );
                $statement->execute(['status' => $status, 'eoi_number' => $eoiNumber]);
                $_SESSION['manage_notice'] = $statement->rowCount() > 0
                    ? 'Updated EOI #' . $eoiNumber . ' to ' . $status . '.'
                    : 'No change was made. The EOI may already have that status or may have been removed.';
                header('Location: ' . $resultsUrl, true, 303);
                exit;

            case 'review_delete':
            case 'delete_by_job':
                $deleteReference = manageInput($_POST, 'delete_reference');
                if ($deleteReference === '' || strlen($deleteReference) > 10) {
                    http_response_code(422);
                    $error = 'Enter a job reference of up to 10 characters.';
                    break;
                }

                if ($action === 'review_delete') {
                    $statement = $database->prepare(
                        'SELECT COUNT(*) FROM eoi WHERE job_reference = :job_reference'
                    );
                    $statement->execute(['job_reference' => $deleteReference]);
                    $count = (int) $statement->fetchColumn();
                    if ($count === 0) {
                        $notice = 'No applications match that job reference.';
                    } else {
                        $confirmation = ['reference' => $deleteReference, 'count' => $count];
                        $_SESSION['manage_delete_reference'] = $deleteReference;
                    }
                    break;
                }

                if (($_SESSION['manage_delete_reference'] ?? null) !== $deleteReference) {
                    http_response_code(422);
                    $error = 'Review this deletion before confirming it.';
                    break;
                }
                $statement = $database->prepare(
                    'DELETE FROM eoi WHERE job_reference = :job_reference'
                );
                $statement->execute(['job_reference' => $deleteReference]);
                unset($_SESSION['manage_delete_reference']);
                $count = $statement->rowCount();
                $_SESSION['manage_notice'] = 'Deleted ' . $count
                    . ($count === 1 ? ' application for ' : ' applications for ')
                    . $deleteReference . '.';
                header('Location: ' . $resultsUrl, true, 303);
                exit;

            default:
                http_response_code(400);
                $error = 'Choose a valid management action.';
        }
    } elseif (!$isPost) {
        unset($_SESSION['manage_delete_reference']);
    }

    $columns = array_merge(
        ['EOInumber', 'job_reference', 'first_name', 'last_name', 'status'],
        array_keys($detailFields)
    );
    $sql = 'SELECT ' . implode(', ', $columns) . ' FROM eoi';
    if ($filters !== []) {
        $conditions = [];
        foreach (array_keys($filters) as $column) {
            $conditions[] = $column . ' = :' . $column;
        }
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }
    $sql .= ' ORDER BY ' . $sort . ' ASC';
    if ($sort !== 'EOInumber') {
        $sql .= ', EOInumber ASC';
    }
    $statement = $database->prepare($sql);
    $statement->execute($filters);
    $applications = $statement->fetchAll();
    $listAvailable = true;
} catch (PDOException $exception) {
    error_log('EOI management failed. PDO code: ' . $exception->getCode());
    http_response_code(500);
    $error = 'Could not access the applications. Please try again later.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Applications | Xì Mi Village Studio</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .manage-heading, .manage-actions, .manage-status {
            display: flex;
            flex-wrap: wrap;
            align-items: end;
            gap: 12px;
        }

        .manage-heading {
            justify-content: space-between;
            align-items: start;
        }

        .manage-filters {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 180px), 1fr));
            gap: 16px;
        }

        .manage-filters > .manage-actions {
            grid-column: 1 / -1;
        }

        .manage-panel {
            margin-bottom: 24px;
            padding: clamp(16px, 4vw, 24px);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background-color: var(--card-bg);
        }

        .manage-panel h3, .manage-message {
            margin: 0 0 12px;
        }

        .manage-status select {
            padding: 8px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            background-color: var(--card-bg);
            color: var(--text-color);
        }

        .manage-details summary {
            cursor: pointer;
        }

        .manage-details dl {
            min-width: 12rem;
            overflow-wrap: anywhere;
        }

        .manage-details dt {
            font-weight: bold;
        }

        .manage-details dd {
            margin: 0 0 6px;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <a class="skip-link" href="#main-content">Skip to main content</a>
    <?php require __DIR__ . '/includes/header.php'; ?>

    <main id="main-content" class="content">
        <section class="home-section" aria-labelledby="manage-title">
            <div class="manage-heading">
                <h2 id="manage-title">Manage Applications</h2>
                <form method="post" action="logout.php">
                    <input type="hidden" name="logout_token" value="<?= manageEscape($_SESSION['logout_token']) ?>">
                    <button type="submit" class="secondary-button">Log out</button>
                </form>
            </div>

            <?php if ($error !== ''): ?>
                <p class="manage-message" role="alert"><?= manageEscape($error) ?></p>
            <?php endif; ?>
            <?php if ($notice !== ''): ?>
                <p class="manage-message" role="status"><?= manageEscape($notice) ?></p>
            <?php endif; ?>

            <form class="manage-panel manage-filters" method="get" action="manage.php" aria-label="Filter applications">
                <div class="form-card">
                    <label for="filter-job">Job reference</label>
                    <input id="filter-job" name="job_reference" value="<?= manageEscape($filters['job_reference'] ?? '') ?>">
                </div>
                <div class="form-card">
                    <label for="filter-first">First name</label>
                    <input id="filter-first" name="first_name" value="<?= manageEscape($filters['first_name'] ?? '') ?>">
                </div>
                <div class="form-card">
                    <label for="filter-last">Last name</label>
                    <input id="filter-last" name="last_name" value="<?= manageEscape($filters['last_name'] ?? '') ?>">
                </div>
                <div class="form-card">
                    <label for="sort-field">Sort by</label>
                    <select id="sort-field" name="sort">
                        <?php foreach ($sortFields as $column => $label): ?>
                            <option value="<?= manageEscape($column) ?>"<?= $sort === $column ? ' selected' : '' ?>>
                                <?= manageEscape($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="manage-actions">
                    <button type="submit">Apply filters</button>
                    <a class="button-link secondary-button" href="manage.php">Show all</a>
                </div>
            </form>

            <?php if ($listAvailable): ?>
                <p><?= count($applications) ?> <?= count($applications) === 1 ? 'application' : 'applications' ?></p>
                <?php if ($applications === []): ?>
                    <p>No applications match these filters.</p>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="career-table">
                            <thead>
                                <tr>
                                    <th scope="col">EOI number</th>
                                    <th scope="col">Job reference</th>
                                    <th scope="col">First name</th>
                                    <th scope="col">Last name</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Application details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($applications as $row): ?>
                                    <tr>
                                        <td><?= manageEscape($row['EOInumber']) ?></td>
                                        <td><?= manageEscape($row['job_reference']) ?></td>
                                        <td><?= manageEscape($row['first_name']) ?></td>
                                        <td><?= manageEscape($row['last_name']) ?></td>
                                        <td>
                                            <form class="manage-status" method="post" action="<?= manageEscape($resultsUrl) ?>">
                                                <input type="hidden" name="manage_token" value="<?= manageEscape($_SESSION['manage_token']) ?>">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="eoi_number" value="<?= manageEscape($row['EOInumber']) ?>">
                                                <select name="status" aria-label="Status for EOI <?= manageEscape($row['EOInumber']) ?>">
                                                    <?php foreach ($statuses as $status): ?>
                                                        <option value="<?= manageEscape($status) ?>"<?= $row['status'] === $status ? ' selected' : '' ?>>
                                                            <?= manageEscape($status) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <button type="submit">Save</button>
                                            </form>
                                        </td>
                                        <td>
                                            <details class="manage-details">
                                                <summary>View details</summary>
                                                <dl>
                                                    <?php foreach ($detailFields as $column => $label): ?>
                                                        <dt><?= manageEscape($label) ?></dt>
                                                        <?php $detail = $row[$column] ?? ''; ?>
                                                        <dd><?= $detail === '' ? '—' : manageEscape($detail) ?></dd>
                                                    <?php endforeach; ?>
                                                </dl>
                                            </details>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </section>

        <?php if ($listAvailable): ?>
            <section id="delete-applications" class="manage-panel" aria-labelledby="delete-title">
                <h3 id="delete-title">Delete applications by job reference</h3>
                <?php if ($confirmation !== null): ?>
                    <p>Delete all applications for <strong><?= manageEscape($confirmation['reference']) ?></strong>?
                        <?= $confirmation['count'] ?> currently match. This cannot be undone.</p>
                    <form class="manage-actions" method="post" action="<?= manageEscape($resultsUrl) ?>#delete-applications">
                        <input type="hidden" name="manage_token" value="<?= manageEscape($_SESSION['manage_token']) ?>">
                        <input type="hidden" name="action" value="delete_by_job">
                        <input type="hidden" name="delete_reference" value="<?= manageEscape($confirmation['reference']) ?>">
                        <button type="submit">Confirm deletion</button>
                        <a class="button-link secondary-button" href="<?= manageEscape($resultsUrl) ?>">Cancel</a>
                    </form>
                <?php else: ?>
                    <form class="manage-actions" method="post" action="<?= manageEscape($resultsUrl) ?>#delete-applications">
                        <input type="hidden" name="manage_token" value="<?= manageEscape($_SESSION['manage_token']) ?>">
                        <input type="hidden" name="action" value="review_delete">
                        <div class="form-card">
                            <label for="delete-reference">Job reference to delete</label>
                            <input id="delete-reference" name="delete_reference"
                                   value="<?= manageEscape($deleteReference) ?>" maxlength="10" required>
                        </div>
                        <button type="submit" class="secondary-button">Review deletion</button>
                    </form>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </main>

    <?php require __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
