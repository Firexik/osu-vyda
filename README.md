# Semestrální projekt VYDA

Jednoduchý systém pro zápis studentů na zápočty a zkoušky. PHP 8.2+, Nette 3.2, Latte a MySQL/MariaDB. Větev `project` obsahuje pouze projekt a jeho zadání.

## Spuštění

1. Zapnout MySQL v XAMPP a spustit `composer install` v kořeni projektu.
2. V phpMyAdmin vytvořit databázi `vyda_project`. Importovat nejprve `database/schema.sql` a potom `database/data.sql`.
3. Připojení je v `config/common.neon`: server `127.0.0.1`, uživatel `root`, bez hesla. Vlastní nastavení lze dát do `config/local.neon` podle přiloženého příkladu.
4. Spustit `composer serve` nebo ve Windows `start.bat` a otevřít [http://127.0.0.1:8000](http://127.0.0.1:8000).

PHP potřebuje rozšíření `pdo_mysql` a `mbstring`. Pokud není v PATH, použijte PHP z XAMPP. Při provozu přes Apache nastavte veřejný kořen na složku `www/`.

## Účty

Heslo všech ukázkových účtů je **`Vyda2026!`**. Před přihlášením vyberte správný typ účtu.

| Typ | Přihlašovací jména |
| --- | --- |
| Student | `st001`, `st002`, `st003`, `st004`, `st005` |
| Pedagog | `p001`, `p002`, `p003`, `p004`, `p005` |

Každý účet má přiřazené předměty. Pedagog `p001` vyučuje VYDA a ALG2; student `st001` studuje VYDA, ALG2, DBAS, PRCS a REWB. U prvního VYDA je již přihlášen a u prvního ALG2 zbývá poslední místo. Data termínů se při importu počítají relativně k aktuálnímu dni.

## Funkce a kód

- Student vidí své předměty a budoucí termíny. V detailu se přihlásí nebo odhlásí.
- Pedagog vidí své předměty a jejich termíny. Budoucí termíny vytváří, upravuje a maže.
- Zápis hlídá duplicitu, jeden platný termín předmětu a maximální kapacitu. Kontroly jsou na serveru; transakce a zámky zajišťují stejná pravidla při souběžných požadavcích.
- Nový nebo upravovaný termín musí být v budoucnosti. Kapacita nepřesáhne počet míst v učebně a neklesne pod počet přihlášených.
- Číselníky se pouze čtou. Volitelné zadávání hodnocení není součástí této verze.

Presentery a formuláře jsou v `app/Presentation/`, databázové operace v `app/Model/TermFacade.php` a ověření účtů v `app/Model/AccountAuthenticator.php`. Vzhled je v jediném `www/assets/app.css`; frontend nemá vlastní JavaScript ani další knihovny. `vendor/` jsou knihovny Nette a `temp/` jeho pracovní soubory.

Původní zadání, SQL a schéma jsou v `docs/zadani/`. Oproti referenční databázi je sjednocena délka kódů předmětů na 10 znaků, použitá znaková sada `utf8mb4`, rozšířené pole hashe hesla a doplněný cizí klíč výsledku. Kapacity mají kladná omezení. Při smazání termínu se jeho zápisy odstraní přes cizí klíč.

U obhajoby lze ukázat přihlášení obou rolí, seznam předmětů, vytvoření termínu, zápis na poslední místo, odmítnutí dalšího termínu stejného předmětu a odhlášení. Platný termín je termín v budoucnosti; časy jsou v `Europe/Prague`.

Jednoduchý [wireframe ve Figmě](https://www.figma.com/design/chKP7n0xcQYFU4TDAQAmKw) odpovídá hornímu menu a tabulkovému přehledu.

Ověřeno 10. 11. 2026 na PHP 8.2.12 a MariaDB 10.4.32: syntaxe PHP a Latte, 40 databázových kontrol, 23 HTTP kontrol a dva souběžné scénáře zápisu. Funguje vestavěný PHP server i místní XAMPP; zkontrolován desktop a mobilní zobrazení.
