# Studentské brigády

Jednoduchá aplikace v Nette pro přihlášení studenta, zobrazení jeho brigád a změnu hesla.

## Spuštění v XAMPP

1. V XAMPP spusťte Apache a MySQL. V [phpMyAdmin](http://localhost/phpmyadmin/) otevřete **Import** a vyberte `group-work/database/groupwork.sql`. Import založí databázi `vyda_groupwork`, doplní přihlašovací údaje do tabulky `studenti` a přidá tabulku `brigady` s ukázkovými daty.
2. Ve složce `group-work` spusťte `composer install`. Aplikace vyžaduje PHP 8.2 nebo novější s `pdo_mysql` a podporou Argon2id.
3. Ve složce `htdocs` musí být projekt dostupný jako `vyda`. Na tomto počítači už odkaz existuje. Otevřete [Studentské brigády](http://localhost/vyda/group-work/www/).

Nastavení ve `config/common.neon` používá výchozí účet místního XAMPP. Vlastní přístupové údaje lze zadat pod klíčem `database` v `config/local.neon`.

## Ukázkové účty

| Přihlašovací e-mail | Výchozí heslo |
| --- | --- |
| `jana@example.test` | `JanaBrigady2026` |
| `adam@example.test` | `AdamBrigady2026` |

Zkuste nejprve zadat špatné údaje. Potom se přihlaste postupně oběma účty a porovnejte jejich seznamy. U jednoho účtu změňte heslo, odhlaste se a ověřte přihlášení se starým i novým heslem.
