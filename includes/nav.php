<?php
// Shared primary navigation.
$currentPage = $currentPage ?? '';
?>

<nav aria-label="Main navigation">
    <ul class="nav-list">

        <li>
            <a href="index.html"
                <?php echo $currentPage === 'index.html' ? 'aria-current="page"' : ''; ?>>
                Home
            </a>
        </li>

        <li>
            <a href="jobs.html"
                <?php echo $currentPage === 'jobs.html' ? 'aria-current="page"' : ''; ?>>
                Jobs
            </a>
        </li>

        <li>
            <a href="apply.html"
                <?php echo $currentPage === 'apply.html' ? 'aria-current="page"' : ''; ?>>
                Apply
            </a>
        </li>

        <li>
            <a href="about.html"
                <?php echo $currentPage === 'about.html' ? 'aria-current="page"' : ''; ?>>
                About
            </a>
        </li>

    </ul>
</nav>