<?php
// Use case: View Details of One Gift Card
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/validation.php';
require_once __DIR__ . '/includes/helpers.php';

$card = null;
$errorMessage = null;

$cardId = validate_card_id($_GET['card_id'] ?? null);
if ($cardId === null) {
    http_response_code(400);
    $errorMessage = 'That gift card link is not valid. Please choose a card from the list.';
} else {
    $card = find_gift_card($pdo, $cardId);
    if ($card === null) {
        http_response_code(404);
        $errorMessage = 'No gift card was found with ID ' . $cardId . '. It may have been deleted.';
    }
}

$statusMessage = ($_GET['status'] ?? '') === 'updated' ? 'The gift card was updated.' : null;

$pageTitle = $card ? $card['cardName'] . ' Gift Card' : 'Gift Card Not Found';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>
    <main class="page">
        <section class="panel">
            <?php if ($errorMessage !== null): ?>
                <h1>Gift Card Not Found</h1>
                <p class="alert alert-error" role="alert"><?= h($errorMessage) ?></p>
                <div class="button-row">
                    <a class="button" href="card-list.php">Return to Gift Card List</a>
                </div>
            <?php else: ?>
                <h1>Gift Card Details</h1>

                <?php if ($statusMessage !== null): ?>
                    <p class="alert alert-success" role="status"><?= h($statusMessage) ?></p>
                <?php endif; ?>

                <h2><?= h($card['cardName']) ?></h2>
                <div class="details-layout">
                    <img src="<?= h(card_image_path($card)) ?>" alt="<?= h($card['cardName']) ?> gift card">
                    <dl class="card-details">
                        <div><dt>Card ID:</dt><dd><?= h($card['cardId']) ?></dd></div>
                        <div><dt>Card Name:</dt><dd><?= h($card['cardName']) ?></dd></div>
                        <div><dt>Card Type:</dt><dd><?= h($card['cardType']) ?></dd></div>
                        <div><dt>Card Value:</dt><dd><?= h(format_card_value($card['cardValue'])) ?></dd></div>
                        <div><dt>Points Required:</dt><dd><?= h(format_points($card['points'])) ?></dd></div>
                    </dl>
                </div>

                <div class="button-row">
                    <a class="button" href="card-update.php?card_id=<?= h($card['cardId']) ?>">Update Gift Card</a>
                    <a class="button button-danger" href="card-delete.php?card_id=<?= h($card['cardId']) ?>">Delete Gift Card</a>
                    <a href="card-list.php">Return to Gift Card List</a>
                </div>
            <?php endif; ?>
        </section>
    </main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
