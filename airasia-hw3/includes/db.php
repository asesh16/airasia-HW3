<?php
/**
 * Shared PDO connection to the SQLite rewards database.
 * Pages use it with: require_once __DIR__ . '/includes/db.php';
 * The connection is available as $pdo.
 */

$databasePath = __DIR__ . '/../database/rewards.db';

// PDO's SQLite driver would silently create an empty database file if the
// path were wrong, so check that the real database exists first.
if (!is_file($databasePath)) {
    http_response_code(500);
    exit('The rewards database was not found. Build it with: '
        . 'sqlite3 database/rewards.db < database/schema.sql && '
        . 'sqlite3 database/rewards.db < database/seed.sql');
}

try {
    $pdo = new PDO('sqlite:' . $databasePath, null, null, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    $pdo->exec('PRAGMA foreign_keys = ON');
} catch (PDOException $e) {
    http_response_code(500);
    error_log('Database connection failed: ' . $e->getMessage());
    exit('Sorry, the rewards database is unavailable right now.');
}

/**
 * Look up one gift card by its id with a prepared statement.
 * Returns the row as an array, or null when no card has that id.
 */
function find_gift_card(PDO $pdo, int $cardId): ?array
{
    $statement = $pdo->prepare(
        'SELECT cardId, cardName, cardType, cardValue, points
         FROM GIFTCARD
         WHERE cardId = :cardId'
    );
    $statement->execute([':cardId' => $cardId]);
    $card = $statement->fetch();

    return $card === false ? null : $card;
}
