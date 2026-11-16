-- AirAsia Rewards Redemption System - HW3 seed data
-- Run after schema.sql:
--   sqlite3 database/rewards.db < database/seed.sql

-- 5 ACCOUNT records (userId values match the USER records added in HW4)
INSERT INTO ACCOUNT (userId, accountType, points) VALUES
    (1, 'Admin',    0),
    (2, 'Gold',     12500),
    (3, 'Silver',   4800),
    (4, 'Platinum', 30250),
    (5, 'Standard', 900);

-- 20 GIFTCARD records
-- cardType values must match the allowed list in includes/validation.php
INSERT INTO GIFTCARD (cardName, cardType, cardValue, points) VALUES
    ('Starbucks',              'Food and Beverage', 10.00,  1500),
    ('Amazon',                 'Shopping',          25.00,  2500),
    ('Target',                 'Shopping',          20.00,  2000),
    ('Grab Food',              'Food and Beverage', 15.00,  1800),
    ('Tealive',                'Food and Beverage',  5.00,   600),
    ('McDonald''s',            'Food and Beverage', 10.00,  1200),
    ('Shopee',                 'Shopping',          50.00,  5000),
    ('Lazada',                 'Shopping',          30.00,  3200),
    ('Uniqlo',                 'Shopping',          40.00,  4200),
    ('IKEA',                   'Shopping',         100.00,  9500),
    ('Netflix',                'Entertainment',     15.00,  1600),
    ('Spotify',                'Entertainment',     10.00,  1100),
    ('GSC Cinemas',            'Entertainment',     20.00,  2100),
    ('Steam',                  'Entertainment',     25.00,  2600),
    ('Agoda',                  'Travel',            50.00,  4800),
    ('Klook',                  'Travel',            30.00,  3000),
    ('AirAsia Flight Voucher', 'Travel',            75.00,  6500),
    ('Booking.com',            'Travel',           100.00,  9800),
    ('Sephora',                'Other',             25.00,  2700),
    ('Barnes & Noble',         'Other',             20.00,  2200);
