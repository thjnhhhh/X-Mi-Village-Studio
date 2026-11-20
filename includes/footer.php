<?php
$currentPage = $currentPage ?? '';
?>

<footer class="site-footer">
    <p><strong>Xì Mi Village Studio</strong></p>

    <nav aria-label="Footer navigation">
        <ul class="footer-nav">
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

    <p>
        Jira:
        <a href="https://phamnhatvuonghoaky.atlassian.net/jira/software/projects/VINGROUP/summary"
           target="_blank" rel="noopener noreferrer">
            Project Board
        </a>
    </p>

    <p>
        GitHub:
        <a href="https://github.com/thjnhhhh/Applied-Web-Project-Part-1-of-2-Group-Submission-"
           target="_blank" rel="noopener noreferrer">
            Source Code Repository
        </a>
    </p>

    <p>
        Email:
        <a href="mailto:langximi@gmail.com">langximi@gmail.com</a>
    </p>

    <p>&copy; <?php echo date('Y'); ?> Xì Mi Village Studio.</p>

    <p><a href="#top">Back to top</a></p>
</footer>