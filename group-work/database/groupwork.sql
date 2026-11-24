CREATE DATABASE IF NOT EXISTS vyda_groupwork CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci;
USE vyda_groupwork;

CREATE TABLE IF NOT EXISTS studenti (
    kod_studenta VARCHAR(10) NOT NULL PRIMARY KEY,
    jmeno VARCHAR(30) NOT NULL,
    prijmeni VARCHAR(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

ALTER TABLE studenti
    ADD COLUMN IF NOT EXISTS email VARCHAR(254) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS password VARCHAR(255) CHARACTER SET ascii COLLATE ascii_bin DEFAULT NULL,
    ADD UNIQUE INDEX IF NOT EXISTS uq_studenti_email (email);

CREATE TABLE IF NOT EXISTS brigady (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(10) NOT NULL,
    name VARCHAR(120) NOT NULL,
    date_from DATE NOT NULL,
    date_to DATE NOT NULL,
    sallary DECIMAL(10, 2) UNSIGNED NOT NULL,
    CONSTRAINT fk_brigady_student FOREIGN KEY (student_id) REFERENCES studenti (kod_studenta)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT ck_brigady_termin CHECK (date_from <= date_to)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- Ukázkové účty pro ověření přihlášení a oddělení brigád.
INSERT INTO studenti (kod_studenta, jmeno, prijmeni, email, password) VALUES
    ('GW001', 'Jana', 'Nováková', 'jana@example.test', '$argon2id$v=19$m=65536,t=4,p=1$ekJHSUxQMjA0bGRpckpScA$YOxjg9HRE2bYeMhlpuZnRLKZSzRByqzvIDSqGyJcwJE'),
    ('GW002', 'Adam', 'Dvořák', 'adam@example.test', '$argon2id$v=19$m=65536,t=4,p=1$aXlpTWtQN0hoTTVLdFhuMA$o3xDiFSyNI8WBcDyqjcKgibWQN4AbiwT9zd0NHMaqxM')
ON DUPLICATE KEY UPDATE kod_studenta = studenti.kod_studenta;

INSERT INTO brigady (id, student_id, name, date_from, date_to, sallary) VALUES
    (1, 'GW001', 'Výpomoc v knihovně', '2026-11-02', '2026-11-13', 4500.00),
    (2, 'GW001', 'Inventura v papírnictví', '2026-11-21', '2026-11-22', 1800.00),
    (3, 'GW002', 'Obsluha kavárny', '2026-11-10', '2026-11-20', 6200.00)
ON DUPLICATE KEY UPDATE id = brigady.id;
