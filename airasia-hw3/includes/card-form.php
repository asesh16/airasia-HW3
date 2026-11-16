<?php
/**
 * Gift Card form shared by card-add.php and card-update.php.
 *
 * Expects:
 *   $formValues  - ['cardName', 'cardType', 'cardValue', 'points'] to show
 *   $errors      - [field name => message] from validate_gift_card()
 *   $submitLabel - text for the submit button
 *   $cancelUrl   - where the Cancel link goes
 *   $cardId      - (update only) the id of the card being edited
 *
 * The HTML attributes (required, min, step, select list) help the user,
 * but every value is validated again on the server.
 */

// Builds the error id/aria attributes for one field, if it has an error.
$fieldError = function (string $field) use ($errors): string {
    return isset($errors[$field]) ? ' aria-invalid="true" aria-describedby="' . $field . '-error"' : '';
};
?>
<?php if ($errors !== []): ?>
    <div class="alert alert-error" role="alert">
        <p><strong>Please fix the following and submit again:</strong></p>
        <ul>
            <?php foreach ($errors as $message): ?>
                <li><?= h($message) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post">
    <?php if (isset($cardId)): ?>
        <input type="hidden" name="card_id" value="<?= h($cardId) ?>">
    <?php endif; ?>

    <div class="form-field">
        <label for="cardName">Card Name</label>
        <input id="cardName" name="cardName" type="text" maxlength="<?= CARD_NAME_MAX_LENGTH ?>"
               value="<?= h($formValues['cardName']) ?>" required<?= $fieldError('cardName') ?>>
        <?php if (isset($errors['cardName'])): ?>
            <p id="cardName-error" class="field-error"><?= h($errors['cardName']) ?></p>
        <?php endif; ?>
    </div>

    <div class="form-field">
        <label for="cardType">Card Type</label>
        <select id="cardType" name="cardType" required<?= $fieldError('cardType') ?>>
            <option value=""<?= $formValues['cardType'] === '' ? ' selected' : '' ?> disabled>Select a card type</option>
            <?php foreach (CARD_TYPES as $type): ?>
                <option value="<?= h($type) ?>"<?= $formValues['cardType'] === $type ? ' selected' : '' ?>><?= h($type) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['cardType'])): ?>
            <p id="cardType-error" class="field-error"><?= h($errors['cardType']) ?></p>
        <?php endif; ?>
    </div>

    <div class="form-field">
        <label for="cardValue">Card Value</label>
        <div class="input-with-prefix">
            <span aria-hidden="true">$</span>
            <input id="cardValue" name="cardValue" type="number" min="0.01" max="<?= CARD_VALUE_MAX ?>" step="0.01"
                   placeholder="10.00" value="<?= h($formValues['cardValue']) ?>" required<?= $fieldError('cardValue') ?>>
        </div>
        <?php if (isset($errors['cardValue'])): ?>
            <p id="cardValue-error" class="field-error"><?= h($errors['cardValue']) ?></p>
        <?php endif; ?>
    </div>

    <div class="form-field">
        <label for="points">Points Required</label>
        <input id="points" name="points" type="number" min="1" max="<?= CARD_POINTS_MAX ?>" step="1"
               value="<?= h($formValues['points']) ?>" required<?= $fieldError('points') ?>>
        <?php if (isset($errors['points'])): ?>
            <p id="points-error" class="field-error"><?= h($errors['points']) ?></p>
        <?php endif; ?>
    </div>

    <div class="button-row">
        <button type="submit"><?= h($submitLabel) ?></button>
        <a href="<?= h($cancelUrl) ?>">Cancel</a>
    </div>
</form>
