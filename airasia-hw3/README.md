# AirAsia Rewards – Gift Card Management (HW3)

A PHP + SQLite (PDO) application that manages the Gift Card portion of the
AirAsia Rewards Redemption System. It builds on the HW2 static HTML/CSS
prototype and adds a database, shared templates, full Gift Card CRUD,
server-side validation, and a validation test harness.

Login, sessions, authorization, customer management, and redemption are
HW4 features and are not part of this assignment.

## Running the app in GitHub Codespaces

1. Open the repository in a Codespace.
2. In the terminal, start PHP's built-in web server from the project root:

   ```bash
   php -S 0.0.0.0:8000
   ```

3. Open the forwarded port 8000 (Ports tab → globe icon). The site opens on
   `card-list.php` (`index.php` redirects there).
4. Run the validation tests in the browser at `/tests/validation-tests.php`,
   or in the terminal with:

   ```bash
   php tests/validation-tests.php
   ```

### Rebuilding the database

`database/rewards.db` is included and already contains the seed data. To
reset it to the original 5 accounts and 20 gift cards:

```bash
rm -f database/rewards.db
sqlite3 database/rewards.db < database/schema.sql
sqlite3 database/rewards.db < database/seed.sql
```

## Project structure

```
├── index.php               Redirects to card-list.php
├── card-list.php           View a List of Gift Cards
├── card-details.php        View Details of One Gift Card
├── card-add.php            Add a Gift Card
├── card-update.php         Update a Gift Card
├── card-delete.php         Delete a Gift Card (POST confirmation)
├── includes/
│   ├── header.php          Document setup, stylesheet link, site title
│   ├── nav.php             Navigation (Gift Card List, Add Gift Card)
│   ├── footer.php          Common closing content
│   ├── db.php              PDO connection to SQLite + find_gift_card()
│   ├── validation.php      Reusable validation functions and h() escaping
│   ├── helpers.php         Card image lookup and number formatting
│   └── card-form.php       Gift Card form shared by add and update
├── database/
│   ├── schema.sql          Creates ACCOUNT and GIFTCARD
│   ├── seed.sql            5 ACCOUNT rows, 20 GIFTCARD rows
│   └── rewards.db          SQLite database built from the two scripts
├── tests/
│   └── validation-tests.php  Validation test harness (PASS/FAIL)
├── css/styles.css
├── images/                 Card images (brand images + one per Card Type)
├── README.md
├── test-report.md
└── ai-use.txt
```

## Database

Table and column names follow the AirAsia Requirements Document.

**GIFTCARD**

| Attribute | Type          | Notes                                  |
|-----------|---------------|----------------------------------------|
| cardId    | INTEGER       | Primary key, auto-increment            |
| cardName  | VARCHAR(100)  | Not null                               |
| cardType  | VARCHAR(100)  | Not null; one of the allowed types     |
| cardValue | FLOAT         | Not null; greater than 0               |
| points    | INTEGER       | Not null; points required, greater than 0 |

**ACCOUNT**

| Attribute   | Type         | Notes                                     |
|-------------|--------------|-------------------------------------------|
| accountId   | INTEGER      | Primary key, auto-increment               |
| userId      | INTEGER      | Not null; links to USER, which is added in HW4 |
| accountType | VARCHAR(100) | Not null                                  |
| points      | INTEGER      | Not null; account point balance           |

The ACCOUNT table is created and seeded now for the HW4 redemption work.
No ACCOUNT pages are built in HW3.

**Card images:** GIFTCARD has no image column, so the pages choose an image
from the card name (`images/<card-name>-card.svg`, such as
`starbucks-card.svg`). If no image has that name, the card uses the image for
its Card Type (`images/type-<card-type>.svg`). Newly added cards always get an
image this way.

## Validation rules (`includes/validation.php`)

| Field           | Rule |
|-----------------|------|
| Card Name       | Required, cannot be only spaces, at most 100 characters |
| Card Type       | Must exactly match one of: Food and Beverage, Shopping, Entertainment, Travel, Other |
| Card Value      | Positive number, at most 2 decimal places, at most $10,000 |
| Points Required | Positive whole number, at most 1,000,000 |
| card_id         | Positive whole number (digits only); anything else is rejected before any query runs |

All queries that use form or URL values are PDO prepared statements, and
every value displayed from a form or database record is escaped with
`htmlspecialchars()` through the `h()` helper.

## Use Case Traceability

| Use case | PHP page or pages | Database table | Test evidence |
|----------|-------------------|----------------|---------------|
| View a List of Gift Cards | `card-list.php` | GIFTCARD | Opened `card-list.php` and confirmed all 20 seeded cards appear with image, Card Name, and Points Required, each linking to `card-details.php?card_id=…` (T02). |
| View Details of One Gift Card | `card-details.php` | GIFTCARD | Opened `card-details.php?card_id=12`, confirmed every attribute matched the database row, and confirmed invalid or missing ids show a safe "not found" message (T03, T08). |
| Add a Gift Card | `card-add.php` (+ `includes/card-form.php`) | GIFTCARD | Submitted valid Gift Card data and confirmed the new record appeared on `card-list.php`, then submitted invalid data and confirmed errors were shown and no row was inserted (T04, T07). |
| Update a Gift Card | `card-update.php` (+ `includes/card-form.php`) | GIFTCARD | Confirmed the form was prepopulated, saved new values, and confirmed `card-details.php` showed the updated record (T05). |
| Delete a Gift Card | `card-delete.php` | GIFTCARD | Confirmed visiting the page only shows a confirmation, then confirmed through the POST form and checked that the card was removed from `card-list.php` (T06). |

Full test steps and results are in [test-report.md](test-report.md).
