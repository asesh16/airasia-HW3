<?php
/**
 * Display helpers shared by the Gift Card pages.
 */

/**
 * Pick the image for a gift card. GIFTCARD has no image column, so a card
 * uses images/<card-name>-card.svg when that file exists (for example
 * images/starbucks-card.svg), and otherwise the image for its Card Type.
 */
function card_image_path(array $card): string
{
    $slug = trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($card['cardName'])), '-');
    $brandImage = 'images/' . $slug . '-card.svg';
    if ($slug !== '' && is_file(__DIR__ . '/../' . $brandImage)) {
        return $brandImage;
    }

    $typeSlug = trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($card['cardType'])), '-');
    $typeImage = 'images/type-' . $typeSlug . '.svg';

    return is_file(__DIR__ . '/../' . $typeImage) ? $typeImage : 'images/type-other.svg';
}

function format_card_value($value): string
{
    return '$' . number_format((float) $value, 2);
}

function format_points($value): string
{
    return number_format((int) $value);
}
