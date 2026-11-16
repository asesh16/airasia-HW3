<?php
/**
 * Shared page header: document setup, stylesheet, and site title bar.
 *
 * Set before requiring:
 *   $pageTitle  - text for the <title> element
 *   $basePath   - optional path back to the project root ('' or '../')
 */
$pageTitle = $pageTitle ?? 'AirAsia Gift Cards';
$basePath  = $basePath ?? '';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?> | AirAsia Gift Cards</title>
    <link rel="stylesheet" href="<?= $basePath ?>css/styles.css">
</head>
<body>
    <header class="site-header">
        <a class="brand" href="<?= $basePath ?>card-list.php">
            <span class="brand-mark" aria-hidden="true">A</span>
            AirAsia Rewards Gift Cards
        </a>
    </header>
