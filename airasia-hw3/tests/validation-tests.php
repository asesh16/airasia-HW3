<?php
/**
 * Validation test harness for includes/validation.php.
 *
 * Only the validation functions are loaded - there is no database
 * connection - so these tests cannot add, change, or delete any records.
 *
 * Open in a browser:   /tests/validation-tests.php
 * Or run in a terminal: php tests/validation-tests.php
 */
require_once __DIR__ . '/../includes/validation.php';

// A known-good Gift Card used as the starting point for each test.
$validCard = [
    'cardName'  => 'Starbucks',
    'cardType'  => 'Food and Beverage',
    'cardValue' => '10.00',
    'points'    => '1500',
];

/**
 * Each Gift Card test changes one field of $validCard.
 * 'expect' is 'pass' or 'fail'; 'field' is the field that should be
 * reported in the errors when the test expects a failure.
 */
$cardTests = [
    // The five required tests
    ['name' => 'Valid Gift Card data',            'input' => [],                                  'expect' => 'pass'],
    ['name' => 'Blank Card Name',                 'input' => ['cardName' => ''],                  'expect' => 'fail', 'field' => 'cardName'],
    ['name' => 'Altered or invalid Card Type',    'input' => ['cardType' => 'Groceries'],         'expect' => 'fail', 'field' => 'cardType'],
    ['name' => 'Zero or negative Card Value',     'input' => ['cardValue' => '0'],                'expect' => 'fail', 'field' => 'cardValue'],
    ['name' => 'Invalid Points Required',         'input' => ['points' => '12.5'],                'expect' => 'fail', 'field' => 'points'],

    // Additional edge cases
    ['name' => 'Card Name of only spaces',        'input' => ['cardName' => '    '],              'expect' => 'fail', 'field' => 'cardName'],
    ['name' => 'Card Name over 100 characters',   'input' => ['cardName' => str_repeat('A', 101)], 'expect' => 'fail', 'field' => 'cardName'],
    ['name' => 'Card Name exactly 100 characters', 'input' => ['cardName' => str_repeat('A', 100)], 'expect' => 'pass'],
    ['name' => 'Card Name with HTML characters',  'input' => ['cardName' => 'Barnes & Noble <b>'], 'expect' => 'pass'],
    ['name' => 'Card Type with wrong letter case', 'input' => ['cardType' => 'shopping'],         'expect' => 'fail', 'field' => 'cardType'],
    ['name' => 'Missing Card Type',               'input' => ['cardType' => null],                'expect' => 'fail', 'field' => 'cardType'],
    ['name' => 'Negative Card Value',             'input' => ['cardValue' => '-5.00'],            'expect' => 'fail', 'field' => 'cardValue'],
    ['name' => 'Non-numeric Card Value',          'input' => ['cardValue' => 'ten'],              'expect' => 'fail', 'field' => 'cardValue'],
    ['name' => 'Card Value with 3 decimals',      'input' => ['cardValue' => '10.999'],           'expect' => 'fail', 'field' => 'cardValue'],
    ['name' => 'Whole-number Card Value',         'input' => ['cardValue' => '25'],               'expect' => 'pass'],
    ['name' => 'Zero Points Required',            'input' => ['points' => '0'],                   'expect' => 'fail', 'field' => 'points'],
    ['name' => 'Negative Points Required',        'input' => ['points' => '-100'],                'expect' => 'fail', 'field' => 'points'],
    ['name' => 'Text Points Required',            'input' => ['points' => 'abc'],                 'expect' => 'fail', 'field' => 'points'],
    ['name' => 'Array sent instead of text (altered form)', 'input' => ['cardName' => ['Starbucks']], 'expect' => 'fail', 'field' => 'cardName'],
];

/** Card ID tests: input => expected return value of validate_card_id(). */
$idTests = [
    ['name' => 'Valid card_id "12"',         'input' => '12',         'expect' => 12],
    ['name' => 'Non-numeric card_id "abc"',  'input' => 'abc',        'expect' => null],
    ['name' => 'Zero card_id "0"',           'input' => '0',          'expect' => null],
    ['name' => 'Negative card_id "-3"',      'input' => '-3',         'expect' => null],
    ['name' => 'Decimal card_id "1.5"',      'input' => '1.5',        'expect' => null],
    ['name' => 'SQL injection "1 OR 1=1"',   'input' => '1 OR 1=1',   'expect' => null],
    ['name' => 'Missing card_id',            'input' => null,         'expect' => null],
    ['name' => 'Too large card_id',          'input' => '99999999999', 'expect' => null],
];

$results = [];

foreach ($cardTests as $test) {
    $input = array_merge($validCard, $test['input']);
    $outcome = validate_gift_card($input);
    $actual = $outcome['errors'] === [] ? 'pass' : 'fail';

    $passed = $actual === $test['expect'];
    // A failure must be reported on the field the test changed.
    if ($passed && $test['expect'] === 'fail') {
        $passed = isset($outcome['errors'][$test['field']]);
    }

    $results[] = [
        'name'     => $test['name'],
        'input'    => json_encode($test['input'] === [] ? $validCard : $test['input'], JSON_UNESCAPED_SLASHES),
        'expected' => $test['expect'] === 'pass' ? 'Passes validation' : 'Fails validation (' . $test['field'] . ')',
        'actual'   => $actual === 'pass' ? 'Passes validation' : 'Fails: ' . implode(' ', $outcome['errors']),
        'passed'   => $passed,
    ];
}

foreach ($idTests as $test) {
    $actual = validate_card_id($test['input']);
    $results[] = [
        'name'     => $test['name'],
        'input'    => json_encode(['card_id' => $test['input']]),
        'expected' => $test['expect'] === null ? 'Rejected (null)' : 'Accepted as ' . $test['expect'],
        'actual'   => $actual === null ? 'Rejected (null)' : 'Accepted as ' . $actual,
        'passed'   => $actual === $test['expect'],
    ];
}

$passCount = count(array_filter($results, fn ($r) => $r['passed']));
$total = count($results);

// Plain-text output when run from the terminal.
if (PHP_SAPI === 'cli') {
    foreach ($results as $i => $r) {
        printf("%-4s %2d. %s\n", $r['passed'] ? 'PASS' : 'FAIL', $i + 1, $r['name']);
    }
    echo "\n{$passCount} of {$total} tests passed.\n";
    exit($passCount === $total ? 0 : 1);
}

$pageTitle = 'Validation Tests';
$basePath  = '../';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav.php';
?>
    <main class="page page-wide">
        <section class="panel">
            <h1>Validation Test Harness</h1>
            <p>
                Tests the functions in <code>includes/validation.php</code>.
                No database connection is opened, so no records are added, changed, or deleted.
            </p>
            <p class="alert <?= $passCount === $total ? 'alert-success' : 'alert-error' ?>">
                <strong><?= $passCount ?> of <?= $total ?> tests passed.</strong>
            </p>

            <div class="table-scroll">
                <table class="test-table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Test</th>
                            <th scope="col">Input</th>
                            <th scope="col">Expected</th>
                            <th scope="col">Actual</th>
                            <th scope="col">Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $i => $r): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= h($r['name']) ?></td>
                                <td><code><?= h($r['input']) ?></code></td>
                                <td><?= h($r['expected']) ?></td>
                                <td><?= h($r['actual']) ?></td>
                                <td>
                                    <span class="result <?= $r['passed'] ? 'result-pass' : 'result-fail' ?>">
                                        <?= $r['passed'] ? 'PASS' : 'FAIL' ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
