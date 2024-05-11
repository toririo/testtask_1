USE php_test;
SET collation_connection = utf8mb4_unicode_ci;
SET NAMES utf8;

INSERT INTO `companies` (`id`,`name`) VALUES
(1,'A@A');

INSERT INTO `agencies` (`id`,`name`) VALUES
(1,'ООО "Рога и копыта"'),
(2,'ООО "Наследие"');

INSERT INTO `countries` (`id`,`name`) VALUES
(1,'Россия'),
(2,'Беларусь'),
(3,'Казахстан');

INSERT INTO `cities` (`id`,`name`,`country_id`) VALUES
(1,'Москва',1),
(2,'Санкт Петербург',1);

INSERT INTO `hotels` (`id`,`name`, `city_id`, `stars`) VALUES
(1,'Балчуг Кемпински Москва', 1, 5),
(2,'Измайлово Альфа', 1, 4),
(3,'Золотое кольцо', 1, 5),
(4,'Плаза Гарден Москва Центр Международной Торговли', 1, 5),
(5,'Измайлово Гамма', 1, 3),
(6,'Нептун', 2, 4),
(7,'Ладога-отель', 2, 3),
(8,'Питер Академия', 2, 3),
(9,'Марко Поло', 2, 4),
(10,'Герцен-Хаус', 2, 1);

INSERT INTO `hotel_agreements` (`id`,`hotel_id`,`discount_percent`,`comission_percent`,`is_default`,`vat_percent`,`vat1_percent`,`vat1_value`,`company_id`,`date_from`,`date_to`,`is_cash_payment`) VALUES
(1,1,10,0,1,20,1,0,1,'2023-01-01','2024-01-01',0),
(2,2,12,0,1,20,1,0,1,'2023-01-01','2024-01-01',0),
(3,3,0,15,1,20,1,0,1,'2023-01-01','2024-01-01',1),
(4,4,12,0,1,20,1,0,1,'2023-01-01','2024-01-01',1),
(5,5,0,10,1,20,1,0,1,'2023-01-01','2024-01-01',1),
(6,6,5,0,1,20,1,0,1,'2023-01-01','2024-01-01',0),
(7,7,0,12,1,20,1,0,1,'2023-01-01','2024-01-01',1),
(8,8,10,0,1,20,1,0,1,'2023-01-01','2024-01-01',1),
(9,9,0,12,1,20,1,0,1,'2023-01-01','2024-01-01',1),
(10,10,14,0,1,20,1,0,1,'2023-01-01','2024-01-01',0);

INSERT INTO `agency_hotel_options` (`id`,`hotel_id`,`agency_id`,`percent`,`is_black`,`is_recomend`,`is_white`) VALUES
(1,1,1,10,0,0,0),
(2,2,1,5,0,0,0),
(3,3,1,8,1,0,0),
(4,4,1,12,0,0,0),
(5,5,1,8,1,0,0),
(6,6,1,15,0,0,0),
(7,7,1,8,0,0,0),
(8,8,1,11,1,0,0),
(9,9,1,12,0,0,0),
(10,10,1,6,0,0,0),
(11,1,2,8,0,0,0),
(12,2,2,12,1,0,0),
(13,3,2,6,0,0,0),
(14,4,2,10,0,0,0),
(15,5,2,9,0,0,0),
(16,6,2,11,1,0,0),
(17,7,2,4,0,0,0),
(18,8,2,12,0,0,0),
(19,9,2,10,1,0,0),
(20,10,2,14,1,0,0);

-- Правило 1: Проверка страны
INSERT INTO rules (name, client_id, manager_text, is_active)
VALUES ('Правило проверки страны 1', 1, 'Текст для менеджера 1', 1);

INSERT INTO rule_conditions (rule_id, condition_type, comparison_operator, value)
VALUES (LAST_INSERT_ID(), 'country', 'equal', '1'); -- Страна: Россия

-- Правило 2: Проверка страны
INSERT INTO rules (name, client_id, manager_text, is_active)
VALUES ('Правило проверки страны 2', 1, 'Текст для менеджера 2', 1);

INSERT INTO rule_conditions (rule_id, condition_type, comparison_operator, value)
VALUES (LAST_INSERT_ID(), 'country', 'not_equal', '1'); -- Страна: не Россия

-- Правило 3: Проверка города
INSERT INTO rules (name, client_id, manager_text, is_active)
VALUES ('Правило проверки города 1', 1, 'Текст для менеджера 3', 1);

INSERT INTO rule_conditions (rule_id, condition_type, comparison_operator, value)
VALUES (LAST_INSERT_ID(), 'city', 'equal', '1'); -- Город: Москва

-- Правило 4: Проверка города
INSERT INTO rules (name, client_id, manager_text, is_active)
VALUES ('Правило проверки города 2', 1, 'Текст для менеджера 4', 1);

INSERT INTO rule_conditions (rule_id, condition_type, comparison_operator, value)
VALUES (LAST_INSERT_ID(), 'city', 'not_equal', '1'); -- Город: не Москва

-- Правило 5: Проверка звездности отеля
INSERT INTO rules (name, client_id, manager_text, is_active)
VALUES ('Правило проверки звездности отеля 1', 1, 'Текст для менеджера 5', 1);

INSERT INTO rule_conditions (rule_id, condition_type, comparison_operator, value)
VALUES (LAST_INSERT_ID(), 'stars', 'greater_than', '3'); -- Звездность больше 3

-- Правило 6: Проверка звездности отеля
INSERT INTO rules (name, client_id, manager_text, is_active)
VALUES ('Правило проверки звездности отеля 2', 1, 'Текст для менеджера 6', 1);

INSERT INTO rule_conditions (rule_id, condition_type, comparison_operator, value)
VALUES (LAST_INSERT_ID(), 'stars', 'less_than', '4');


