-- Ukol 3: v phpMyAdminu vyberte databazi test a tento soubor jednou importujte.
-- Podklad: form.php z vetve ukol1. Zachovava puvodni studenty s ID 1 a 2.
-- Funguje nad puvodni tabulkou students (id, name, surname) i v prazdne databazi.
-- Ukazkove udaje jsou smyslene. Heslo vsech peti studentu: Student123!

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS students (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    surname VARCHAR(50) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

CREATE TABLE regions (
    id TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_regions_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

INSERT INTO regions (id, name) VALUES
    (1, 'Hlavní město Praha'),
    (2, 'Středočeský kraj'),
    (3, 'Jihočeský kraj'),
    (4, 'Plzeňský kraj'),
    (5, 'Karlovarský kraj'),
    (6, 'Ústecký kraj'),
    (7, 'Liberecký kraj'),
    (8, 'Královéhradecký kraj'),
    (9, 'Pardubický kraj'),
    (10, 'Kraj Vysočina'),
    (11, 'Jihomoravský kraj'),
    (12, 'Olomoucký kraj'),
    (13, 'Zlínský kraj'),
    (14, 'Moravskoslezský kraj');

-- Pri importu do prazdne databaze doplni i puvodni dva studenty.
INSERT INTO students (id, name, surname) VALUES
    (1, 'Petr', 'Novák'),
    (2, 'Pavel', 'Novák')
ON DUPLICATE KEY UPDATE id = VALUES(id);

-- Docasne povolene NULL umozni nejprve doplnit existujici zaznamy.
ALTER TABLE students
    CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci,
    ADD COLUMN email VARCHAR(254) NULL,
    ADD COLUMN password VARCHAR(255) NULL,
    ADD COLUMN street VARCHAR(150) NULL,
    ADD COLUMN city VARCHAR(100) NULL,
    ADD COLUMN zip CHAR(5) NULL,
    ADD COLUMN region_id TINYINT UNSIGNED NULL,
    ADD COLUMN gender ENUM('muz', 'zena') NULL,
    ADD COLUMN note TEXT NULL;

UPDATE students SET
    email = 'petr.novak@example.com',
    password = '$2y$10$Tm2SrNEa7e5XCeJvy5S7QecoVwaYUYI5ZPl.C7l8lec9F35STnqwK',
    street = '17. listopadu 15', city = 'Ostrava', zip = '70800',
    region_id = 14, gender = 'muz', note = 'Student informatiky, 3. semestr.'
WHERE id = 1;

UPDATE students SET
    email = 'pavel.novak@example.com',
    password = '$2y$10$nUOZwGPZIZjLo/9llegpl.wAB68Sxpm1iKFYcKql7YzBIp9SCPlhW',
    street = 'Školní 12', city = 'Praha', zip = '11000',
    region_id = 1, gender = 'muz', note = 'Zajímá se o webové aplikace.'
WHERE id = 2;

INSERT INTO students (id, name, surname, email, password, street, city, zip, region_id, gender, note) VALUES
    (3, 'Jana', 'Svobodová', 'jana.svobodova@example.com',
     '$2y$10$ldC0pSJ4JQZc/WmGwXAjs.4mlSBhZipQXpOLktU4hjAHBj1zYvS92',
     'Kounicova 20', 'Brno', '60200', 11, 'zena', 'Věnuje se návrhu databází.'),
    (4, 'Tomáš', 'Dvořák', 'tomas.dvorak@example.com',
     '$2y$10$8phSx.YjStQ4MnJkuf0iM.F/hVsMb06fkmLOALYZ/n7QNuiv0D3Hq',
     'Studentská 5', 'Olomouc', '77900', 12, 'muz', 'Člen studentského programátorského týmu.'),
    (5, 'Eva', 'Černá', 'eva.cerna@example.com',
     '$2y$10$HEZm.L4qL4fDYxKtK6nxtO95.6J3kvFwGzhcu0Yc6vyDMex3PnEDK',
     'Dlouhá 8', 'Zlín', '76001', 13, 'zena', 'Připravuje projekt v PHP.');

ALTER TABLE students
    MODIFY COLUMN email VARCHAR(254) NOT NULL,
    MODIFY COLUMN password VARCHAR(255) NOT NULL,
    MODIFY COLUMN street VARCHAR(150) NOT NULL,
    MODIFY COLUMN city VARCHAR(100) NOT NULL,
    MODIFY COLUMN zip CHAR(5) NOT NULL,
    MODIFY COLUMN region_id TINYINT UNSIGNED NOT NULL,
    MODIFY COLUMN gender ENUM('muz', 'zena') NOT NULL,
    MODIFY COLUMN note TEXT NOT NULL,
    ADD UNIQUE KEY uq_students_email (email),
    ADD CONSTRAINT fk_students_region FOREIGN KEY (region_id) REFERENCES regions (id)
        ON UPDATE CASCADE ON DELETE RESTRICT;
