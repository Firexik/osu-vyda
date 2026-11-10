-- Referenční schéma VYDA; všechny tabulky používají transakční InnoDB.
-- Změny oproti podkladu jsou odůvodněny v docs/obhajoba.md.
CREATE TABLE mistnosti (
    zkratka_mistnosti VARCHAR(5) NOT NULL PRIMARY KEY,
    popis VARCHAR(30) NOT NULL,
    kapacita SMALLINT UNSIGNED NOT NULL,
    CONSTRAINT mistnost_kapacita CHECK (kapacita > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

CREATE TABLE pedagogove (
    kod_pedagoga VARCHAR(20) NOT NULL PRIMARY KEY,
    jmeno VARCHAR(30) NOT NULL,
    prijmeni VARCHAR(30) NOT NULL,
    tituly_pred_jmenem VARCHAR(20) NOT NULL DEFAULT '',
    tituly_za_jmenem VARCHAR(20) NOT NULL DEFAULT '',
    heslo VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

CREATE TABLE studenti (
    kod_studenta VARCHAR(10) NOT NULL PRIMARY KEY,
    jmeno VARCHAR(30) NOT NULL,
    prijmeni VARCHAR(30) NOT NULL,
    heslo VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

CREATE TABLE predmety (
    zkratka_predmetu VARCHAR(10) NOT NULL PRIMARY KEY,
    nazev VARCHAR(50) NOT NULL,
    pocet_kreditu SMALLINT UNSIGNED NOT NULL,
    pocet_hodin_prednasek SMALLINT UNSIGNED NOT NULL,
    pocet_hodin_cviceni SMALLINT UNSIGNED NOT NULL,
    ukonceni SET('Zp','Zk') NOT NULL DEFAULT 'Zp',
    anotace TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

CREATE TABLE pedagogove_predmety (
    kod_pedagoga VARCHAR(20) NOT NULL,
    zkratka_predmetu VARCHAR(10) NOT NULL,
    PRIMARY KEY (kod_pedagoga, zkratka_predmetu),
    FOREIGN KEY (kod_pedagoga) REFERENCES pedagogove (kod_pedagoga) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (zkratka_predmetu) REFERENCES predmety (zkratka_predmetu) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

CREATE TABLE studenti_predmety (
    kod_studenta VARCHAR(10) NOT NULL,
    zkratka_predmetu VARCHAR(10) NOT NULL,
    PRIMARY KEY (kod_studenta, zkratka_predmetu),
    FOREIGN KEY (kod_studenta) REFERENCES studenti (kod_studenta) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (zkratka_predmetu) REFERENCES predmety (zkratka_predmetu) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

CREATE TABLE vysledky (
    id_vysledku SMALLINT NOT NULL PRIMARY KEY,
    popis VARCHAR(20) NOT NULL,
    typ SET('Zp','Zk') NOT NULL DEFAULT 'Zp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

CREATE TABLE vypsane_terminy (
    id_terminu INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    zkratka_mistnosti VARCHAR(5) NOT NULL,
    kod_pedagoga VARCHAR(20) NOT NULL,
    zkratka_predmetu VARCHAR(10) NOT NULL,
    datum_cas DATETIME NOT NULL,
    max_pocet_prihlasenych SMALLINT UNSIGNED NOT NULL,
    poznamka VARCHAR(255) NOT NULL DEFAULT '',
    INDEX predmet_datum (zkratka_predmetu, datum_cas),
    CONSTRAINT termin_kapacita CHECK (max_pocet_prihlasenych > 0),
    FOREIGN KEY (zkratka_mistnosti) REFERENCES mistnosti (zkratka_mistnosti) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (kod_pedagoga) REFERENCES pedagogove (kod_pedagoga) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (zkratka_predmetu) REFERENCES predmety (zkratka_predmetu) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

CREATE TABLE zapsane_terminy (
    id_terminu INT NOT NULL,
    kod_studenta VARCHAR(10) NOT NULL,
    id_vysledku SMALLINT DEFAULT NULL,
    PRIMARY KEY (id_terminu, kod_studenta),
    FOREIGN KEY (id_terminu) REFERENCES vypsane_terminy (id_terminu) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (kod_studenta) REFERENCES studenti (kod_studenta) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (id_vysledku) REFERENCES vysledky (id_vysledku) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;
