<?php
// Use case: Update a Gift Card
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/validation.php';

$errors = [];
$card = null;
$errorMessage = null;

// The id comes from the URL on the first visit and from a hidden field
// when the form is submitted. Either way it is validated before use.
$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
$cardId = validate_card_id($isPost ? ($_POST['card_id'] ?? null) : ($_GET['card_id'] ?? null));

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

if ($card !== null) {
    // Prepopulate the form with the values currently in the database.
    $formValues = [
        'cardName'  => $card['cardName'],
        'cardType'  => $card['cardType'],
        'cardValue' => number_format((float) $card['cardValue'], 2, '.', ''),
        'points'    => (string) $card['points'],
    ];

    if ($isPost) {
        $result = validate_gift_card($_POST);
        $errors = $result['errors'];

        if ($errors === []) {
            $statement = $pdo->prepare(
                'UPDATE GIFTCARD
                 SET cardName = :cardName,
                     cardType = :cardType,
                     cardValue = :cardValue,
                     points = :points
                 WHERE cardId = :cardId'
            );
            $statement->execute([
                ':cardName'  => $result['data']['cardName'],
                ':cardType'  => $result['data']['cardType'],
                ':cardValue' => $result['data']['cardValue'],
                ':points'    => $result['data']['points'],
                ':cardId'    => $cardId,
            ]);

            // Show the updated record on the details page.
            header('Location: card-details.php?card_id=' . $cardId . '&status=updated');
            exit;
        }

        // Validation failed: redisplay what the user submitted.
        foreach ($formValues as $field => $unused) {
            $formValues[$field] = is_string($_POST[$field] ?? null) ? $_POST[$field] : '';
        }
    }
}

$pageTitle = $card ? 'Update ' . $card['cardName'] : 'Gift Card Not Found';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>
    <main class="page">
        <section class="panel form-panel">
            <?php if ($errorMessage !== null): ?>
                <h1>Gift Card Not Found</h1>
                <p class="alert alert-error" role="alert"><?= h($errorMessage) ?></p>
                <div class="button-row">
                    <a class="button" href="card-list.php">Return to Gift Card List</a>
                </div>
            <?php else: ?>
                <h1>Update Gift Card</h1>
                <p class="form-intro">Editing card #<?= h($card['cardId']) ?>: <strong><?= h($card['cardName']) ?></strong></p>
                <?php
                $submitLabel = 'Save Changes';
                $cancelUrl   = 'card-details.php?card_id=' . $cardId;
                require __DIR__ . '/includes/card-form.php';
                ?>
            <?php endif; ?>
        </section>
    </main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
