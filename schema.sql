CREATE DATABASE IF NOT EXISTS yeticave
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

USE yeticave;

set foreign_key_checks = 0;

CREATE TABLE IF NOT EXISTS `user`
(
  `id`             int unsigned                           NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `created_at`     timestamp    DEFAULT CURRENT_TIMESTAMP NOT NULL,
  `email`          char(255)                              NOT NULL UNIQUE KEY,
  `name`           char(255)                              NOT NULL,
  `password`       char(255)                              NOT NULL,
  `avatar_url`     char(255)    DEFAULT NULL,
  `contacts`       char(255)                              NOT NULL,
  `lots_created`   int unsigned DEFAULT NULL,
  `stakes_created` int unsigned DEFAULT NULL,
  KEY user_lot_id_fk (lots_created),
  KEY user_stake_id_fk (stakes_created),
  CONSTRAINT user_lot_created_id FOREIGN KEY (lots_created) REFERENCES lot (id),
  CONSTRAINT user_stake_created_id FOREIGN KEY (stakes_created) REFERENCES stake (id)
);

CREATE TABLE IF NOT EXISTS `cat`
(
  `id`   int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` char(255)    NOT NULL UNIQUE KEY
);

CREATE TABLE IF NOT EXISTS `lot`
(
  `id`            int unsigned                           NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `created_at`    timestamp    DEFAULT CURRENT_TIMESTAMP NOT NULL,
  `title`         char(255)                              NOT NULL,
  `description`   varchar(1000)                          NOT NULL,
  `image_url`     char(255)                              NOT NULL,
  `opening_price` int unsigned                           NOT NULL,
  `ends_at`       timestamp                              NOT NULL,
  `bid_step`      int unsigned                           NOT NULL,
  `winner_id`     int unsigned DEFAULT NULL,
  `author_id`     int unsigned                           NOT NULL,
  `cat_id`        int unsigned                           NOT NULL,
  KEY lot_title (title),
  KEY lot_description (description),
  KEY lot_cat_id_fk (cat_id),
  KEY lot_author_id_fk (author_id),
  KEY lot_winner_id_fk (winner_id),
  CONSTRAINT lot_cat_id_fk FOREIGN KEY (cat_id) REFERENCES cat (id),
  CONSTRAINT lot_author_id_fk FOREIGN KEY (author_id) REFERENCES user (id),
  CONSTRAINT lot_winner_id_fk FOREIGN KEY (winner_id) REFERENCES user (id)
);

CREATE TABLE IF NOT EXISTS `stake`
(
  `id`          int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `created_at`  timestamp    DEFAULT CURRENT_TIMESTAMP NOT NULL,
  `buyer_price` int unsigned NOT NULL,
  `buyer_id`    int unsigned NOT NULL,
  `lot_id`      int unsigned NOT NULL,
  KEY stake_buyer_id_fk (buyer_id),
  KEY stake_lot_id_fk (lot_id),
  CONSTRAINT stake_buyer_id_fk FOREIGN KEY (buyer_id) REFERENCES user (id),
  CONSTRAINT stake_lot_id_fk FOREIGN KEY (lot_id) REFERENCES lot (id)
);