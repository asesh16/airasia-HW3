-- AirAsia Rewards Redemption System - HW3 schema (SQLite)
-- Table and column names follow the AirAsia Requirements Document.
--
-- Rebuild the database from the project root with:
--   sqlite3 database/rewards.db < database/schema.sql
--   sqlite3 database/rewards.db < database/seed.sql

PRAGMA foreign_keys = ON;

DROP TABLE IF EXISTS GIFTCARD;
DROP TABLE IF EXISTS ACCOUNT;

-- Account table data structure
-- accountId int(11) auto, not null, primary key
-- userId    int(11) not null  (references USER.userId; the USER table and
--                              its foreign key are added in HW4 with login)
-- accountType varchar(100) not null
-- points    int(50) not null
CREATE TABLE ACCOUNT (
    accountId   INTEGER      PRIMARY KEY AUTOINCREMENT NOT NULL,
    userId      INTEGER      NOT NULL,
    accountType VARCHAR(100) NOT NULL,
    points      INTEGER      NOT NULL CHECK (points >= 0)
);

-- Gift card table data structure
-- cardId    int(11) auto, not null, primary key
-- cardName  varchar(100) not null
-- cardType  varchar(100) not null
-- cardValue float(10) not null
-- points    int(50) not null  (points required to redeem the card)
CREATE TABLE GIFTCARD (
    cardId    INTEGER      PRIMARY KEY AUTOINCREMENT NOT NULL,
    cardName  VARCHAR(100) NOT NULL CHECK (length(trim(cardName)) > 0),
    cardType  VARCHAR(100) NOT NULL,
    cardValue FLOAT        NOT NULL CHECK (cardValue > 0),
    points    INTEGER      NOT NULL CHECK (points > 0)
);
