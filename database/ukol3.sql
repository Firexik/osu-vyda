-- Ukol 3: v phpMyAdminu vyberte databazi test a tento soubor jednou importujte.
-- Zadani ucitele dodane 29. 10. 2026. Pole podle form.php z vetve ukol1, bez hesel.
-- Zachovava puvodni studenty s ID 1 a 2 a doplnuje celkem deset studentu.
-- Funguje nad puvodni tabulkou students (id, name, surname) i v prazdne databazi.
-- Ukazkove udaje jsou smyslene. Formular se nemeni; data se doplnuji v phpMyAdminu.

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
    ADD COLUMN street VARCHAR(150) NULL,
    ADD COLUMN city VARCHAR(100) NULL,
    ADD COLUMN zip CHAR(5) NULL,
    ADD COLUMN region_id TINYINT UNSIGNED NULL,
    ADD COLUMN gender ENUM('muz', 'zena') NULL;

UPDATE students SET
    email = 'petr.novak@outlook.com',
    street = '17. listopadu 15', city = 'Ostrava', zip = '70800',
    region_id = 14, gender = 'muz'
WHERE id = 1;

UPDATE students SET
    email = 'pavel.novak@gmail.com',
    street = 'Školní 12', city = 'Praha', zip = '11000',
    region_id = 1, gender = 'muz'
WHERE id = 2;

INSERT INTO students (id, name, surname, email, street, city, zip, region_id, gender) VALUES
    (3, 'Jana', 'Svobodová', 'jana.svobodova@outlook.com',
     'Kounicova 20', 'Brno', '60200', 11, 'zena'),
    (4, 'Tomáš', 'Dvořák', 'tomas.dvorak@gmail.com',
     'Studentská 5', 'Olomouc', '77900', 12, 'muz'),
    (5, 'Eva', 'Černá', 'eva.cerna@outlook.com',
     'Dlouhá 8', 'Zlín', '76001', 13, 'zena'),
    (6, 'Martin', 'Veselý', 'martin.vesely@gmail.com',
     'Jabloňová 14', 'Liberec', '46001', 7, 'muz'),
    (7, 'Lucie', 'Procházková', 'lucie.prochazkova@outlook.com',
     'Lipová 6', 'České Budějovice', '37001', 3, 'zena'),
    (8, 'Adam', 'Kučera', 'adam.kucera@gmail.com',
     'Dukelská 22', 'Plzeň', '30100', 4, 'muz'),
    (9, 'Tereza', 'Novotná', 'tereza.novotna@outlook.com',
     'Husova 18', 'Hradec Králové', '50002', 8, 'zena'),
    (10, 'Ondřej', 'Doležal', 'ondrej.dolezal@gmail.com',
     'Smetanova 4', 'Pardubice', '53002', 9, 'muz');

ALTER TABLE students
    MODIFY COLUMN email VARCHAR(254) NOT NULL,
    MODIFY COLUMN street VARCHAR(150) NOT NULL,
    MODIFY COLUMN city VARCHAR(100) NOT NULL,
    MODIFY COLUMN zip CHAR(5) NOT NULL,
    MODIFY COLUMN region_id TINYINT UNSIGNED NOT NULL,
    MODIFY COLUMN gender ENUM('muz', 'zena') NOT NULL,
    ADD UNIQUE KEY uq_students_email (email),
    ADD CONSTRAINT fk_students_region FOREIGN KEY (region_id) REFERENCES regions (id)
        ON UPDATE CASCADE ON DELETE RESTRICT;
