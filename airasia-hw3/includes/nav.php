<?php
/**
 * Shared navigation bar.
 * Set $currentPage (e.g. 'card-list') before requiring to highlight a link.
 */
$basePath    = $basePath ?? '';
$currentPage = $currentPage ?? '';

$navLinks = [
    'card-list' => 'Gift Card List',
    'card-add'  => 'Add Gift Card',
];
?>
    <nav class="site-nav" aria-label="Main navigation">
        <?php foreach ($navLinks as $page => $label): ?>
            <a href="<?= $basePath . $page ?>.php"<?= $page === $currentPage ? ' aria-current="page"' : '' ?>><?= $label ?></a>
        <?php endforeach; ?>
    </nav>
