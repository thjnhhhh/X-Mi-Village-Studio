<?php
$currentPage = $currentPage ?? '';
?>

<nav aria-label="Main navigation">
    <ul class="nav-list">
        <li>
            <a href="index.php"
                <?php echo $currentPage === 'index.php' ? 'aria-current="page"' : ''; ?>>
                Home
            </a>
        </li>

        <li>
            <a href="jobs.php"
                <?php echo $currentPage === 'jobs.php' ? 'aria-current="page"' : ''; ?>>
                Jobs
            </a>
        </li>

        <li>
            <a href="apply.php"
                <?php echo $currentPage === 'apply.php' ? 'aria-current="page"' : ''; ?>>
                Apply
            </a>
        </li>

        <li>
            <a href="about.php"
                <?php echo $currentPage === 'about.php' ? 'aria-current="page"' : ''; ?>>
                About
            </a>
        </li>
    </ul>
</nav>