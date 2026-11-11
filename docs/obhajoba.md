# Soulad se zadáním a podklady k obhajobě

Ověřeno 11. 11. 2026 podle [aktuálního zadání](zadani/semestralni-projekt.txt), které nyní výslovně vyžaduje Nette či jiný PHP framework. Projekt od počátku používá Nette Application, Forms, Database a Security a šablony Latte; čisté PHP bez frameworku nepoužívá. Podmínka frameworku tedy nevyžadovala přepis implementace.

## Povinné funkce

| Požadavek | Implementace a ověření |
| --- | --- |
| PHP framework a MySQL | Nette v `composer.json`, start aplikace v `app/Bootstrap.php`, databáze v `config/common.neon`. |
| Student a pedagog; nepřihlášený uživatel nemůže pracovat se systémem | `AccountAuthenticator` ověřuje heslo a přiřazuje roli; `BasePresenter` chrání všechny aplikační stránky. HTTP kontroly ověřují přesměrování hostů a zákaz správy termínů studentem. |
| Nejméně 5 funkčních účtů každé role s přiřazenými předměty | `database/data.sql` obsahuje 5 studentů a 5 pedagogů. Ověřeno heslo, přiřazené předměty i dostupné termíny všech deseti účtů. |
| Moje předměty pro obě role | `CoursePresenter` a `TermFacade::getCourses()` filtrují podle vazeb účtu na předměty. |
| Moje termíny pro pedagoga; vypsané termíny předmětů studenta | `TermPresenter` a `TermFacade::getTerms()` filtrují podle předmětů. Pedagog může spravovat termíny předmětů, které učí, včetně těch s druhým pedagogem stejného předmětu. Student vidí budoucí termíny. |
| Vytvoření, úprava a smazání termínu | Nette formuláře a transakce v `TermFacade::save()` a `delete()`. Nové i upravené termíny musí být v budoucnosti; předmět je ověřován na serveru. |
| Přihlášení a odhlášení studenta | Formulář v detailu a `TermFacade::enroll()` / `cancel()`. Ověřeno také přes skutečné HTTP POST. |
| Bez duplicitních zápisů a nejvýše jeden platný termín stejného předmětu | Serverové kontroly a primární klíč zápisu. Platný znamená budoucí termín; staré záznamy zůstávají historií. Zámek studenta chrání souběžný zápis dvou různých termínů téhož předmětu. |
| Nepřekročit maximální kapacitu | Počet zápisů se kontroluje uvnitř transakce se zámkem termínu. Dva souběžné požadavky na poslední místo mají právě jednoho vítěze; druhý dostane hlášení o obsazenosti. |
| Číselníky pouze pro čtení | Web neobsahuje správu pedagogů, studentů, předmětů, výsledků, místností ani přiřazovacích tabulek. Naplňují se importem demonstračních dat. |
| Prezentace u zápočtu | Lokální spuštění a ukázkové účty jsou v `README.md`. Termín odevzdání dle zadání: 7. 12. 2026 ve 23:55. |

Zadávání hodnocení je podle zadání nepovinné a implementováno není. Nemění to splnění povinných požadavků. Provozní nasazení a obhajobu musí provést student.

## Změny proti referenční databázi

Referencí jsou [původní SQL](zadani/vydap_projekt_final.sql.txt) a [obrázek schématu](zadani/schema-semestralni-projekt.png). Obrázek byl 11. 11. 2026 porovnán s nově staženým zdrojem: SHA-256 `B61651C1F842722F0E9418C280BE2776F4AA27423CB5B8731E761ADE3AE4068C` se shoduje.

- Kódy předmětů mají ve všech vazbách jednotně 10 znaků, aby byly typy klíčů a cizích klíčů konzistentní.
- Tabulky používají InnoDB pro transakce a řádkové zámky, `utf8mb4` a české řazení pro texty s diakritikou.
- Hesla mají délku 255 znaků a ukládají se jako hash `password_hash()`, nikoli jako čitelný text.
- Výsledek v zápisu je nepovinný (`NULL` před hodnocením) a má cizí klíč do číselníku výsledků.
- Kapacity místností a termínů jsou kladné; index předmětu a data pomáhá hledání platných termínů.
- Smazání termínu odstraní jeho zápisy přes `ON DELETE CASCADE`, aby nezůstaly osiřelé záznamy.
- Poznámka termínu má 255 znaků namísto 200. Formulář omezuje délku podle schématu.

## Ověření

Dne 11. 11. 2026 prošlo všech **83 kontrol** na PHP **8.2.12** i **8.5.12** s MariaDB **10.4.32**. Kontrola syntaxe všech PHP souborů aplikace a testu i validace Composeru rovněž prošly.

Příkaz `php tests/requirements.php` vytváří databázi s náhodným názvem `vyda_test_*`, importuje schéma a demonstrační data a spouští také oddělenou kopii webu v dočasné složce. Nikdy nenačítá místní `config/local.neon` a nepoužívá aplikační databázi `vyda_project`. Po skončení zastaví svůj webový server a smaže pouze vlastní testovací databázi. Diagnostické webové logy zůstávají v dočasné složce OS.

Ověřeny jsou účty a role, přiřazené předměty, oprávnění, budoucí termíny, kapacity, duplicity, odhlášení, mazání a dva souběžné scénáře zápisu. HTTP testy ověřují oba typy přihlášení, chráněné stránky i vytváření, úpravu, smazání a studentský zápis přes formuláře. Testovací server databáze běží na `127.0.0.1:33077`; port a přihlašovací údaje lze nastavit proměnnými `VYDA_TEST_PORT`, `VYDA_TEST_USER` a `VYDA_TEST_PASSWORD`. Testovací účet potřebuje oprávnění vytvořit a odstranit vlastní databázi. Pro HTTP testy jsou navíc potřeba PHP rozšíření `curl` a `dom` a dostupné `proc_open`.

Testy neslouží jako obecný bezpečnostní audit ani jako zátěžový test. Zahrnují důležité scénáře stanovené zadáním; neprokazují všechny možné souběhy a provozní situace.
