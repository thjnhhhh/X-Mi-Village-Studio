<?php
// Shared site header.
$currentPage = $currentPage ?? '';

$brandDescription = $brandDescription
    ?? 'We combine design, technology and innovation to create meaningful digital experiences for businesses and their audiences.';
?>

<header class="site-header" id="top">
    <div class="company-brand">
        <a class="logo" href="index.html">
            <img src="images/logo.png"
                 alt="Xì Mi Village Studio logo"
                 width="180"
                 height="180">
        </a>

        <h1>Xì Mi Village Studio</h1>

        <p class="tagline">Small Village. Big Ideas.</p>

        <p class="brand-description">
            <?php echo htmlspecialchars($brandDescription, ENT_QUOTES, 'UTF-8'); ?>
        </p>
    </div>

    <div class="theme-control">
        <input type="checkbox" id="theme-toggle">
        <label for="theme-toggle">Dark mode</label>
    </div>

    <?php require __DIR__ . '/nav.php'; ?>
</header>