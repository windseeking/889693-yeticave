USE yeticave;

INSERT INTO user (email, name, password, contacts)
VALUES ('test@test.com', 'Павел', 'thebestpasswordisqwerty', '+79055555555'),
       ('mail@mail.com', 'Инна', 'thebestpasswordisqwerty', '+71234567890'),
       ('meow@meow.com', 'Василий', 'thebestpasswordisqwerty', '+79110001100');

INSERT INTO cat (name)
VALUES ('Доски и лыжи'),
       ('Крепления'),
       ('Ботинки'),
       ('Одежда'),
       ('Инструменты'),
       ('Разное');

INSERT INTO lot (title, description, img_url, cat_id, opening_price, ends_at, bid_step, author_id)
VALUES ('2014 Rossignol District Snowboard', 'description', 'img/lot-1.jpg', '1', '10999', '2019-02-22', '100', '1'),
       ('DC Ply Mens 2016/2017 Snowboard', 'description', 'img/lot-2.jpg', '1', '159999', '2018-03-03', '200', '2'),
       ('Крепления Union Contact Pro 2015 года размер L/XL', 'description', 'img/lot-3.jpg', '2', '8000', '2019-04-05', '300',
        '3'),
       ('Ботинки для сноуборда DC Mutiny Charocal', 'description', 'img/lot-4.jpg', '3', '10999', '2019-11-14', '400', '1'),
       ('Куртка для сноуборда DC Mutiny Charocal', 'description', 'img/lot-5.jpg', '4', '7500', '2019-02-28', '500', '2'),
       ('Маска Oakley Canopy', 'description', 'img/lot-6.jpg', '5', '5400', '2018-03-08', '600', '3');

INSERT INTO stake (buyer_price, buyer_id, lot_id)
VALUES ('11000', '2', '1'),
       ('8300', '3', '3');
