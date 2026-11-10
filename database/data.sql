-- Ukázkové účty: studenti st001–st005 a pedagogové p001–p005.
-- Všechny mají heslo Vyda2026! uložené jako hash password_hash().
SET NAMES utf8mb4;
SET @heslo = '$2y$10$yXGUs9RhGuELxnAZYPzsoePqNALE1KaYxARPYldVL/9vjHUADo8py';
START TRANSACTION;

INSERT INTO pedagogove (kod_pedagoga, jmeno, prijmeni, tituly_pred_jmenem, tituly_za_jmenem, heslo) VALUES
('p001', 'Martin', 'Novák', 'Ing.', 'Ph.D.', @heslo),
('p002', 'Eva', 'Svobodová', 'RNDr.', 'Ph.D.', @heslo),
('p003', 'Petr', 'Dvořák', 'Ing.', '', @heslo),
('p004', 'Lucie', 'Černá', 'Mgr.', '', @heslo),
('p005', 'Tomáš', 'Procházka', 'doc. Ing.', 'Ph.D.', @heslo);

INSERT INTO studenti (kod_studenta, jmeno, prijmeni, heslo) VALUES
('st001', 'Jakub', 'Kovalčík', @heslo),
('st002', 'Anna', 'Veselá', @heslo),
('st003', 'Adam', 'Horák', @heslo),
('st004', 'Tereza', 'Malá', @heslo),
('st005', 'David', 'Kučera', @heslo);

INSERT INTO predmety (zkratka_predmetu, nazev, pocet_kreditu, pocet_hodin_prednasek, pocet_hodin_cviceni, ukonceni, anotace) VALUES
('VYDA', 'Webové a databázové aplikace', 5, 2, 2, 'Zp', 'Tvorba aplikací v PHP a práce s databází MySQL.'),
('ALG2', 'Algoritmy 2', 6, 2, 2, 'Zk', 'Algoritmy a datové struktury.'),
('DBAS', 'Databázové systémy', 5, 2, 2, 'Zk', 'Relační model, SQL a transakce.'),
('PRCS', 'Programování v C#', 5, 2, 2, 'Zp', 'Objektové programování v C#.'),
('REWB', 'Responzivní web', 4, 2, 2, 'Zp', 'Tvorba webu v HTML a CSS.'),
('PRCC', 'Programování v C/C++', 5, 2, 2, 'Zk', 'Programování v jazyce C a C++.');

INSERT INTO pedagogove_predmety (kod_pedagoga, zkratka_predmetu) VALUES
('p001', 'VYDA'), ('p001', 'ALG2'), ('p002', 'VYDA'), ('p002', 'DBAS'),
('p003', 'PRCS'), ('p004', 'REWB'), ('p005', 'PRCC');

INSERT INTO studenti_predmety (kod_studenta, zkratka_predmetu) VALUES
('st001', 'VYDA'), ('st001', 'ALG2'), ('st001', 'DBAS'), ('st001', 'PRCS'), ('st001', 'REWB'),
('st002', 'VYDA'), ('st002', 'ALG2'), ('st002', 'DBAS'),
('st003', 'VYDA'), ('st003', 'PRCS'), ('st003', 'PRCC'),
('st004', 'VYDA'), ('st004', 'REWB'), ('st004', 'PRCC'),
('st005', 'VYDA'), ('st005', 'DBAS'), ('st005', 'PRCC');

INSERT INTO mistnosti (zkratka_mistnosti, popis, kapacita) VALUES
('A101', 'Počítačová učebna', 24), ('B203', 'Seminární místnost', 16),
('C105', 'Posluchárna', 60), ('D202', 'Laboratoř', 12);

INSERT INTO vysledky (id_vysledku, popis, typ) VALUES
(1, 'Započteno', 'Zp'), (2, 'Nezapočteno', 'Zp'),
(3, 'A', 'Zk'), (4, 'B', 'Zk'), (5, 'C', 'Zk'), (6, 'D', 'Zk'), (7, 'E', 'Zk'), (8, 'F', 'Zk');

INSERT INTO vypsane_terminy (id_terminu, zkratka_predmetu, kod_pedagoga, zkratka_mistnosti, datum_cas, max_pocet_prihlasenych, poznamka) VALUES
(1, 'VYDA', 'p001', 'A101', TIMESTAMP(CURDATE() + INTERVAL 2 DAY, '09:00:00'), 24, 'Prezentace semestrálního projektu.'),
(2, 'VYDA', 'p002', 'B203', TIMESTAMP(CURDATE() + INTERVAL 5 DAY, '13:00:00'), 16, 'Druhý termín obhajob.'),
(3, 'ALG2', 'p001', 'C105', TIMESTAMP(CURDATE() + INTERVAL 3 DAY, '10:00:00'), 2, 'Písemná část zkoušky.'),
(4, 'DBAS', 'p002', 'A101', TIMESTAMP(CURDATE() + INTERVAL 7 DAY, '08:30:00'), 20, 'Praktický test z SQL.'),
(5, 'PRCS', 'p003', 'D202', TIMESTAMP(CURDATE() + INTERVAL 9 DAY, '11:00:00'), 12, 'Ukázka řešení projektu.'),
(6, 'REWB', 'p004', 'B203', TIMESTAMP(CURDATE() + INTERVAL 11 DAY, '09:30:00'), 16, 'Prezentace webu.'),
(7, 'PRCC', 'p005', 'A101', TIMESTAMP(CURDATE() + INTERVAL 12 DAY, '12:00:00'), 24, 'Písemná a praktická část.'),
(8, 'ALG2', 'p001', 'C105', TIMESTAMP(CURDATE() + INTERVAL 14 DAY, '10:00:00'), 30, 'Náhradní termín.'),
(9, 'VYDA', 'p001', 'A101', TIMESTAMP(CURDATE() - INTERVAL 5 DAY, '09:00:00'), 24, 'Proběhlý termín.'),
(10, 'DBAS', 'p002', 'B203', TIMESTAMP(CURDATE() - INTERVAL 3 DAY, '10:00:00'), 16, 'Proběhlá zkouška.');

INSERT INTO zapsane_terminy (id_terminu, kod_studenta, id_vysledku) VALUES
(1, 'st001', NULL), (1, 'st002', NULL), (1, 'st003', NULL),
(3, 'st002', NULL), (4, 'st005', NULL), (5, 'st003', NULL), (7, 'st004', NULL),
(9, 'st001', 1), (9, 'st002', NULL), (10, 'st001', 4);

COMMIT;
