<?php
/**
 * Reusable server-side validation and safe-output helpers for Gift Cards.
 * This file never touches the database, so tests/validation-tests.php can
 * exercise it without changing any records.
 */

// Allowed GIFTCARD.cardType values (also used to build the form dropdown).
const CARD_TYPES = [
    'Food and Beverage',
    'Shopping',
    'Entertainment',
    'Travel',
    'Other',
];

const CARD_NAME_MAX_LENGTH = 100;   // GIFTCARD.cardName is varchar(100)
const CARD_VALUE_MAX       = 10000; // upper limit on the dollar value
const CARD_POINTS_MAX      = 1000000;

/**
 * Escape a value for safe output in HTML text or attribute values.
 */
function h($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Validate a Gift Card identifier (from a URL parameter or hidden field).
 * Returns the id as a positive int, or null if it is not a valid id.
 */
function validate_card_id($value): ?int
{
    if (is_int($value)) {
        return $value > 0 ? $value : null;
    }
    // Digits only, no leading zero, at most 10 digits (fits int(11)).
    if (!is_string($value) || !preg_match('/^[1-9][0-9]{0,9}$/', $value)) {
        return null;
    }
    $id = (int) $value;

    return $id <= 2147483647 ? $id : null;
}

/**
 * Validate the Card Name: required, 1-100 characters after trimming.
 * Returns an error message, or null when the value is valid.
 */
function validate_card_name($value): ?string
{
    if (!is_string($value) || trim($value) === '') {
        return 'Card Name is required.';
    }
    if (mb_strlen(trim($value), 'UTF-8') > CARD_NAME_MAX_LENGTH) {
        return 'Card Name must be ' . CARD_NAME_MAX_LENGTH . ' characters or fewer.';
    }

    return null;
}

/**
 * Validate the Card Type: must exactly match one of CARD_TYPES.
 */
function validate_card_type($value): ?string
{
    if (!is_string($value) || $value === '') {
        return 'Please choose a Card Type.';
    }
    if (!in_array($value, CARD_TYPES, true)) {
        return 'Card Type must be one of: ' . implode(', ', CARD_TYPES) . '.';
    }

    return null;
}

/**
 * Validate the Card Value: a positive dollar amount with at most 2 decimals.
 */
function validate_card_value($value): ?string
{
    if (!is_string($value) && !is_int($value) && !is_float($value)) {
        return 'Card Value must be a number.';
    }
    $value = trim((string) $value);
    if ($value === '') {
        return 'Card Value is required.';
    }
    if (!preg_match('/^-?[0-9]+(\.[0-9]+)?$/', $value)) {
        return 'Card Value must be a number, such as 25 or 25.00.';
    }
    if ((float) $value <= 0) {
        return 'Card Value must be greater than zero.';
    }
    if (!preg_match('/^[0-9]+(\.[0-9]{1,2})?$/', $value)) {
        return 'Card Value can have at most two decimal places.';
    }
    if ((float) $value > CARD_VALUE_MAX) {
        return 'Card Value cannot be more than $' . number_format(CARD_VALUE_MAX) . '.';
    }

    return null;
}

/**
 * Validate Points Required: a positive whole number.
 */
function validate_points($value): ?string
{
    if (!is_string($value) && !is_int($value)) {
        return 'Points Required must be a whole number.';
    }
    $value = trim((string) $value);
    if ($value === '') {
        return 'Points Required is required.';
    }
    if (!preg_match('/^-?[0-9]+$/', $value)) {
        return 'Points Required must be a whole number (no decimals or letters).';
    }
    if ((int) $value <= 0) {
        return 'Points Required must be greater than zero.';
    }
    if (strlen(ltrim($value, '0')) > 7 || (int) $value > CARD_POINTS_MAX) {
        return 'Points Required cannot be more than ' . number_format(CARD_POINTS_MAX) . '.';
    }

    return null;
}

/**
 * Validate a full Gift Card submission (e.g. $_POST).
 *
 * Returns ['data' => cleaned values ready for the database,
 *          'errors' => [field name => message]].
 * The submission is valid when 'errors' is empty.
 */
function validate_gift_card(array $input): array
{
    $checks = [
        'cardName'  => validate_card_name($input['cardName'] ?? null),
        'cardType'  => validate_card_type($input['cardType'] ?? null),
        'cardValue' => validate_card_value($input['cardValue'] ?? null),
        'points'    => validate_points($input['points'] ?? null),
    ];
    $errors = array_filter($checks, fn ($message) => $message !== null);

    $data = [];
    if ($errors === []) {
        $data = [
            'cardName'  => trim($input['cardName']),
            'cardType'  => $input['cardType'],
            'cardValue' => round((float) trim((string) $input['cardValue']), 2),
            'points'    => (int) trim((string) $input['points']),
        ];
    }

    return ['data' => $data, 'errors' => $errors];
}
