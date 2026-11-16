<?php
// Use case: View a List of Gift Cards
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/validation.php';
require_once __DIR__ . '/includes/helpers.php';

// No user input in this query, so a plain query() is safe here.
$cards = $pdo->query(
    'SELECT cardId, cardName, cardType, cardValue, points
     FROM GIFTCARD
     ORDER BY cardName COLLATE NOCASE'
)->fetchAll();

// Confirmation messages after a redirect. Only known keys are shown,
// so the URL cannot inject arbitrary text into the page.
$statusMessages = [
    'added'   => 'The gift card was added.',
    'deleted' => 'The gift card was deleted.',
];
$status = $_GET['status'] ?? '';
$statusMessage = is_string($status) ? ($statusMessages[$status] ?? null) : null;

$pageTitle   = 'Gift Card List';
$currentPage = 'card-list';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>
    <main class="page">
        <section class="panel">
            <div class="page-heading">
                <h1>Gift Card List</h1>
                <a class="button" href="card-add.php">Add Gift Card</a>
            </div>

            <?php if ($statusMessage !== null): ?>
                <p class="alert alert-success" role="status"><?= h($statusMessage) ?></p>
            <?php endif; ?>

            <h2>Available Cards (<?= count($cards) ?>)</h2>

            <?php if ($cards === []): ?>
                <p>There are no gift cards yet. <a href="card-add.php">Add the first gift card</a>.</p>
            <?php else: ?>
                <div class="gift-card-list">
                    <?php foreach ($cards as $card): ?>
                        <?php $detailsUrl = 'card-details.php?card_id=' . urlencode((string) $card['cardId']); ?>
                        <article class="gift-card">
                            <a href="<?= h($detailsUrl) ?>">
                                <img src="<?= h(card_image_path($card)) ?>" alt="<?= h($card['cardName']) ?> gift card">
                            </a>
                            <div>
                                <h3><a href="<?= h($detailsUrl) ?>"><?= h($card['cardName']) ?></a></h3>
                                <p>Points required: <?= h(format_points($card['points'])) ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
