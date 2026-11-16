<?php
// Use case: Delete a Gift Card
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/validation.php';
require_once __DIR__ . '/includes/helpers.php';

$card = null;
$errorMessage = null;

// GET shows the confirmation page; only a POST can delete the record.
$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
$cardId = validate_card_id($isPost ? ($_POST['card_id'] ?? null) : ($_GET['card_id'] ?? null));

if ($cardId === null) {
    http_response_code(400);
    $errorMessage = 'That gift card link is not valid. Please choose a card from the list.';
} else {
    $card = find_gift_card($pdo, $cardId);
    if ($card === null) {
        http_response_code(404);
        $errorMessage = 'No gift card was found with ID ' . $cardId . '. It may already have been deleted.';
    }
}

if ($card !== null && $isPost && ($_POST['confirm'] ?? '') === 'yes') {
    $statement = $pdo->prepare('DELETE FROM GIFTCARD WHERE cardId = :cardId');
    $statement->execute([':cardId' => $cardId]);

    header('Location: card-list.php?status=deleted');
    exit;
}

$pageTitle = $card ? 'Delete ' . $card['cardName'] : 'Gift Card Not Found';
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
                <h1>Delete Gift Card</h1>
                <p class="alert alert-warning">
                    Are you sure you want to permanently delete the
                    <strong><?= h($card['cardName']) ?></strong> gift card? This cannot be undone.
                </p>

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

                <form method="post" class="button-row">
                    <input type="hidden" name="card_id" value="<?= h($card['cardId']) ?>">
                    <input type="hidden" name="confirm" value="yes">
                    <button type="submit" class="button-danger">Yes, Delete This Gift Card</button>
                    <a href="card-details.php?card_id=<?= h($card['cardId']) ?>">Cancel</a>
                </form>
            <?php endif; ?>
        </section>
    </main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
