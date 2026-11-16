# HW3 Test Report – AirAsia Gift Card Management

**Test date:** 2026-11-15
**Environment:** PHP 8.5 built-in web server (`php -S`), SQLite 3.43, PDO SQLite
**Starting data:** `database/rewards.db` freshly built from `schema.sql` + `seed.sql`
(5 ACCOUNT rows, 20 GIFTCARD rows)

Database results were confirmed with the `sqlite3` command-line tool, for example
`sqlite3 database/rewards.db "SELECT * FROM GIFTCARD WHERE cardId = 12;"`.
Altered-form tests (T07, T08) sent requests the normal form cannot produce,
such as a Card Type outside the dropdown or a changed hidden `card_id`. These
were sent with `curl` or by editing the form in the browser's developer tools.

## Summary

| Test ID | Test | Status |
|---------|------|--------|
| T01 | Validation test harness | PASS |
| T02 | View Gift Card List | PASS |
| T03 | View Gift Card Details | PASS |
| T04 | Add Gift Card | PASS |
| T05 | Update Gift Card | PASS |
| T06 | Delete Gift Card | PASS |
| T07 | Invalid or altered input handling | PASS |
| T08 | Invalid Gift Card identifier handling | PASS |

---

## T01 – Validation test harness

**Steps and input**
1. Open `tests/validation-tests.php` in the browser. You can also run `php tests/validation-tests.php` in the terminal.
2. Review the PASS/FAIL result for each test.
3. Count GIFTCARD rows before and after (`SELECT COUNT(*) FROM GIFTCARD;`).

The harness runs the five required tests plus 22 more edge-case tests:
- Valid Gift Card data
- Blank Card Name
- Altered or invalid Card Type (`Groceries`)
- Zero Card Value (`0`)
- Invalid Points Required (`12.5`)
- Edge cases, including:
  - Card Name that is only spaces, or longer than 100 characters
  - Card Type in the wrong letter case (`shopping`)
  - Negative, non-numeric, or 3-decimal Card Values
  - Zero, negative, or text Points Required
  - An array sent instead of text
  - Invalid `card_id` values: `abc`, `0`, `-3`, `1.5`, `1 OR 1=1`, missing, and too large

**Expected result**
- Valid data passes, and each invalid input fails on the field it changed.
- Every row shows PASS.
- The row count is unchanged, because the harness never opens the database.

**Actual result**
- The page showed "27 of 27 tests passed", with a green PASS label on every row.
- The terminal run printed `27 of 27 tests passed.`
- GIFTCARD had 20 rows before and 20 after.

**Status:** PASS

---

## T02 – View Gift Card List

**Steps and input**
1. Open `card-list.php`. You can also open the site root, which redirects to `card-list.php`.
2. Check each card entry and click a card name and a card image.
3. Click the "Add Gift Card" button and the navigation links.

**Expected result**
- All 20 cards from GIFTCARD are listed, each with an image, Card Name, and Points Required.
- The name and image link to `card-details.php?card_id=<id>`.
- "Add Gift Card" links to `card-add.php`.
- Special characters display correctly.

**Actual result**
- The page heading showed "Available Cards (20)", followed by 20 entries sorted by name.
- Starbucks, Amazon, and Target showed their brand images. The other cards showed their Card Type image.
- "Spotify" linked to `card-details.php?card_id=12`.
- "McDonald's" and "Barnes & Noble" displayed correctly; the HTML source showed them escaped as `&#039;` and `&amp;`.
- The add button and the navigation links worked.

**Status:** PASS

---

## T03 – View Gift Card Details

**Steps and input**
1. From the list, click **Spotify**, which opens `card-details.php?card_id=12`.
2. Compare the values on the page with `SELECT * FROM GIFTCARD WHERE cardId = 12;`.
3. Check the three links.

**Expected result**
- A read-only view shows the card image, Card ID, Card Name, Card Type, Card Value, and Points Required, all matching the database.
- The page has links to Update Gift Card, Delete Gift Card, and Return to Gift Card List.

**Actual result**
- The page showed:
  - Card ID 12
  - Card Name Spotify
  - Card Type Entertainment
  - Card Value $10.00
  - Points Required 1,100
- These matched the database row `12|Spotify|Entertainment|10.0|1100`.
- The links went to `card-update.php?card_id=12`, `card-delete.php?card_id=12`, and `card-list.php`.

**Status:** PASS

---

## T04 – Add Gift Card

**Steps and input**
1. Click **Add Gift Card** in the navigation.
2. Enter the following and click **Add Gift Card**:
   - Card Name: `Grab Ride`
   - Card Type: `Travel`
   - Card Value: `12.50`
   - Points Required: `1300`
3. Check the list and the database.

**Expected result**
- The form has no Card ID input.
- After submitting, the user is redirected to `card-list.php` with a success message.
- The new card appears in the list.
- A new row exists in GIFTCARD.

**Actual result**
- The form showed only the four editable fields.
- Submitting redirected (HTTP 302) to `card-list.php?status=added`, which showed "The gift card was added."
- "Grab Ride" appeared in the list with the Travel image, linked to `card_id=21`.
- The database contained the row `21|Grab Ride|Travel|12.5|1300`.

**Status:** PASS

---

## T05 – Update Gift Card

**Steps and input**
1. Open `card-details.php?card_id=12` and click **Update Gift Card**.
2. Confirm the form is prepopulated.
3. Change the values to the following and click **Save Changes**:
   - Card Name: `Spotify Premium`
   - Card Value: `15.00`
   - Points Required: `1650`

**Expected result**
- The form shows the current values, with the correct Card Type selected.
- After saving, the user goes to `card-details.php?card_id=12` with a success message showing the new values.
- The database row is updated.

**Actual result**
- The form was prepopulated with:
  - Card Name `Spotify`
  - Card Type `Entertainment` (selected)
  - Card Value `10.00`
  - Points Required `1100`
- Saving redirected to `card-details.php?card_id=12&status=updated`, which showed "The gift card was updated." and the values Spotify Premium, $15.00, and 1,650.
- The database row became `12|Spotify Premium|Entertainment|15.0|1650`.

**Status:** PASS

---

## T06 – Delete Gift Card

**Steps and input**
1. Open the details page for "Grab Ride" (`card_id=21`) and click **Delete Gift Card**.
2. Check that visiting the page did not delete anything.
3. Try deleting through GET instead, using `card-delete.php?card_id=21&confirm=yes`.
4. Click **Yes, Delete This Gift Card**.

**Expected result**
- The confirmation page clearly identifies the card.
- Opening the page through GET never deletes the card, even with `confirm=yes` in the URL.
- After a POST confirmation, the user returns to `card-list.php` and the card is gone.

**Actual result**
- The page showed "Are you sure you want to permanently delete the **Grab Ride** gift card?" along with the card image and details.
- After the GET requests, the row still existed (`COUNT(*) = 1`).
- A POST without the confirmation field also left the row in place.
- Confirming through the POST form redirected to `card-list.php?status=deleted`, which showed "The gift card was deleted."
- Grab Ride was no longer listed, and `COUNT(*)` for `cardId = 21` was 0.

**Status:** PASS

---

## T07 – Invalid or altered input handling

**Steps and input**
1. On `card-add.php`, submit the following:
   - Card Name: empty
   - Card Type: `Groceries` (not in the dropdown; sent with an altered form)
   - Card Value: `-5`
   - Points Required: `12.5`
2. On `card-update.php?card_id=12`, submit Card Value `0` and Points Required `abc`.
3. On `card-add.php`, submit Card Name `<script>alert(1)</script>` with otherwise valid values, then view its details page.
4. Submit an altered form that sends `cardName[]` and `cardType[]` as arrays.

**Expected result**
- Invalid submissions are rejected on the server, with a helpful message next to each field.
- The values the user typed are kept in the form.
- No database row is added or changed.
- Script text is stored as plain text and displayed escaped, never run.
- Array input is rejected without PHP errors.

**Actual result**
- Step 1 showed four messages:
  - "Card Name is required."
  - "Card Type must be one of: Food and Beverage, Shopping, Entertainment, Travel, Other."
  - "Card Value must be greater than zero."
  - "Points Required must be a whole number (no decimals or letters)."
- After step 1, the GIFTCARD count stayed at 21.
- Step 2 showed the Card Value and Points Required errors, and row 12 was unchanged.
- In step 3, the details page HTML contained `&lt;script&gt;alert(1)&lt;/script&gt;` and no `<script>` tag.
- Step 4 showed "Card Name is required." and "Please choose a Card Type." with HTTP 200, and the server log had no warnings.

**Status:** PASS

---

## T08 – Invalid Gift Card identifier handling

**Steps and input**
1. Open `card-details.php` with each of these `card_id` values:
   - `abc`
   - `0`
   - `-1`
   - `1 OR 1=1`
   - empty
   - `999` (well-formed, but no such card)
2. Repeat with `card-update.php` and `card-delete.php` using `abc` and `999`.
3. Submit the update form with a changed hidden `card_id` (`abc`, `999`).
4. Try deleting a card that was already deleted.

**Expected result**
- Malformed ids are rejected before any query runs and show a "not valid" message (HTTP 400).
- Well-formed ids with no matching card show a "not found" message (HTTP 404).
- Every error page links back to the list.
- Nothing is changed.

**Actual result**
- `abc`, `0`, `-1`, `1 OR 1=1`, and the empty value returned 400 with "That gift card link is not valid. Please choose a card from the list."
- `999` returned 404 with "No gift card was found with ID 999. It may have been deleted."
- `card-update.php` and `card-delete.php` returned 400 for `abc` and 404 for `999`.
- The changed hidden `card_id` values returned 400 and 404, and no rows were updated.
- Deleting the already-deleted card returned 404.

**Status:** PASS

---

## Defects found and fixed

| # | Problem | Fix | Retest |
|---|---------|-----|--------|
| 1 | In the first draft of the shared Gift Card form, the `<form>` tag had `novalidate`, which turned off the browser's own checks (`required`, `min`, `step`). Server-side validation still worked, but users lost instant feedback. | Removed `novalidate` so the browser checks run first, and kept full server-side validation for altered requests. | The rendered `card-add.php` HTML has no `novalidate`, and the inputs carry `required`, `min`, and `step` attributes. The altered requests in T07 still received the server error messages. PASS |
| 2 | If `database/rewards.db` is missing, PDO's SQLite driver silently creates an empty database file, and pages then fail with "no such table". | `includes/db.php` checks that the database file exists before connecting and shows a message explaining how to rebuild it. | Renamed the database file and loaded `card-list.php`. It returned HTTP 500 with the rebuild instructions, and no empty `rewards.db` was created. Restored the file, and the page returned 200. PASS |
