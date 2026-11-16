<?php
// Use case: Add a Gift Card
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/validation.php';

$errors = [];
$formValues = ['cardName' => '', 'cardType' => '', 'cardValue' => '', 'points' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = validate_gift_card($_POST);
    $errors = $result['errors'];

    if ($errors === []) {
        $statement = $pdo->prepare(
            'INSERT INTO GIFTCARD (cardName, cardType, cardValue, points)
             VALUES (:cardName, :cardType, :cardValue, :points)'
        );
        $statement->execute([
            ':cardName'  => $result['data']['cardName'],
            ':cardType'  => $result['data']['cardType'],
            ':cardValue' => $result['data']['cardValue'],
            ':points'    => $result['data']['points'],
        ]);

        // Post/Redirect/Get: return to the list so a refresh cannot re-insert.
        header('Location: card-list.php?status=added');
        exit;
    }

    // Keep what the user typed so they only need to fix the errors.
    foreach ($formValues as $field => $unused) {
        $formValues[$field] = is_string($_POST[$field] ?? null) ? $_POST[$field] : '';
    }
}

$pageTitle   = 'Add Gift Card';
$currentPage = 'card-add';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>
    <main class="page">
        <section class="panel form-panel">
            <h1>Add Gift Card</h1>
            <?php
            $submitLabel = 'Add Gift Card';
            $cancelUrl   = 'card-list.php';
            require __DIR__ . '/includes/card-form.php';
            ?>
        </section>
    </main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
