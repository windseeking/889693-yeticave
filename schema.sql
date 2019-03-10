CREATE DATABASE IF NOT EXISTS yeticave
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

USE yeticave;

set foreign_key_checks = 0;

CREATE TABLE IF NOT EXISTS `user`
(
  `id`           int unsigned                           NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `created_at`   timestamp    DEFAULT CURRENT_TIMESTAMP NOT NULL,
  `email`        char(255)                              NOT NULL UNIQUE KEY,
  `name`         char(255)                              NOT NULL,
  `password`     char(255)                              NOT NULL,
  `avatar_url`   char(255)    DEFAULT NULL,
  `contacts`     char(255)                              NOT NULL,
  `lots_created` int unsigned DEFAULT NULL,
  `bids_created` int unsigned DEFAULT NULL,
  KEY user_lot_id_fk (lots_created),
  KEY user_bid_id_fk (bids_created),
  CONSTRAINT user_lot_created_id FOREIGN KEY (lots_created) REFERENCES lot (id),
  CONSTRAINT user_bid_created_id FOREIGN KEY (bids_created) REFERENCES bid (id)
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
  `img_url`       char(255)                              NOT NULL,
  `opening_price` int unsigned                           NOT NULL,
  `current_price` int unsigned DEFAULT NULL,
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

CREATE TABLE IF NOT EXISTS `bid`
(
  `id`          int unsigned                        NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `created_at`  timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
  `buyer_price` int unsigned                        NOT NULL,
  `buyer_id`    int unsigned                        NOT NULL,
  `lot_id`      int unsigned                        NOT NULL,
  KEY bid_buyer_id_fk (buyer_id),
  KEY bid_lot_id_fk (lot_id),
  CONSTRAINT bid_buyer_id_fk FOREIGN KEY (buyer_id) REFERENCES user (id),
  CONSTRAINT bid_lot_id_fk FOREIGN KEY (lot_id) REFERENCES lot (id)
);

CREATE FULLTEXT INDEX lots_ft_search ON lot(title, description);
