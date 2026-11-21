<?php
require_once __DIR__ . '/settings.php';

$currentPage = 'jobs.php';
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

        <section class="job" id="fe001">
            <h2>Front-End Web Developer</h2>
            <p>Reference: <span class="job-ref">FE001</span></p>
            <p>
                Build responsive, accessible websites for our clients
                using HTML5, CSS3 and modern front-end practices. Work
                closely with designers to turn concepts into polished,
                validated pages.
            </p>
            <p>
                <strong>Salary:</strong> $85,000 to $100,000 plus superannuation.
                <strong>Reports to:</strong> Head of Development.
            </p>

            <h3>Key Responsibilities</h3>
            <ul>
                <li>Convert design mockups into semantic, standards-compliant HTML and CSS.</li>
                <li>Ensure client websites follow WCAG accessibility guidelines.</li>
                <li>Maintain shared component libraries and style guides.</li>
                <li>Collaborate with designers and back-end developers in agile sprints.</li>
            </ul>

            <h3>Requirements</h3>
            <h4>Essential</h4>
            <ol>
                <li>Strong knowledge of HTML5 and CSS3, including Flexbox and Grid.</li>
                <li>Experience making websites accessible and validating code.</li>
                <li>Working knowledge of Git and GitHub.</li>
            </ol>

            <h4>Preferable</h4>
            <ul>
                <li>Experience with a JavaScript framework such as React or Vue.</li>
                <li>Familiarity with Jira or other agile project tools.</li>
            </ul>

            <p>
                <a class="button-link" href="apply.php?job=FE001">Apply for FE001</a>
            </p>
        </section>

        <section class="job" id="ux002">
            <h2>UI/UX Designer</h2>
            <p>Reference: <span class="job-ref">UX002</span></p>
            <p>
                Shape the look, feel and usability of client websites
                and digital products. Research user needs, create
                wireframes and prototypes, and develop design systems.
            </p>
            <p>
                <strong>Salary:</strong> $80,000 to $95,000 plus superannuation.
                <strong>Reports to:</strong> Creative Director.
            </p>

            <h3>Key Responsibilities</h3>
            <ul>
                <li>Create wireframes, prototypes and high-fidelity layouts.</li>
                <li>Run user research and usability testing.</li>
                <li>Maintain consistent colour and typography standards.</li>
                <li>Present design concepts to clients and respond to feedback.</li>
            </ul>

            <h3>Requirements</h3>
            <h4>Essential</h4>
            <ol>
                <li>A portfolio showing web or app design work.</li>
                <li>Proficiency in Figma or Adobe XD.</li>
                <li>An understanding of accessible colour contrast and layout.</li>
            </ol>

            <h4>Preferable</h4>
            <ul>
                <li>Basic HTML and CSS knowledge.</li>
                <li>Experience designing for government or education clients.</li>
            </ul>

            <p>
                <a class="button-link" href="apply.php?job=UX002">Apply for UX002</a>
            </p>
        </section>

    </main>

    <?php require __DIR__ . '/includes/footer.php'; ?>

</body>
</html>