Nette Web Project
=================

Welcome to the Nette Web Project! This is a basic skeleton application built using
[Nette](https://nette.org), ideal for kick-starting your new web projects.

Nette is a renowned PHP web development framework, celebrated for its user-friendliness,
robust security, and outstanding performance. It's among the safest choices
for PHP frameworks out there.

If Nette helps you, consider supporting it by [making a donation](https://nette.org/donate).
Thank you for your generosity!


Requirements
------------

This Web Project is compatible with Nette 3.2 and requires PHP 8.2.


Installation
------------

To install the Web Project, Composer is the recommended tool. If you're new to Composer,
follow [these instructions](https://doc.nette.org/composer). Then, run:

	composer create-project nette/web-project path/to/install
	cd path/to/install

Ensure the `temp/` and `log/` directories are writable.


Asset Building with Vite
------------------------

This project supports Vite for asset building, which is recommended but optional. To activate Vite:

1. Uncomment the `type: vite` line in the `common.neon` configuration file under the assets mapping section.
2. Then set up and build the assets:

		npm install
		npm run build


Web Server Setup
----------------

To quickly dive in, use PHP's built-in server:

	php -S localhost:8000 -t www

Then, open `http://localhost:8000` in your browser to view the welcome page.

For Apache or Nginx users, configure a virtual host pointing to your project's `www/` directory.

**Important Note:** Ensure `app/`, `config/`, `log/`, and `temp/` directories are not web-accessible.
Refer to [security warning](https://nette.org/security-warning) for more details.


Minimal Skeleton
----------------

For demonstrating issues or similar tasks, rather than starting a new project, use
[minimal skeleton](https://github.com/nette/web-project/tree/minimal).


Začlenění do repozitáře VYDA
---------------------------

Tento Nette blog je podsložka `nette-blog/` společného repozitáře VYDA.
Původní cvičení a podklady zůstávají v kořeni VYDA. V PhpStormu otevřete
celou složku VYDA; blog pak upravujte v její podsložce `nette-blog/`.

Závislosti, cache, logy a místní soukromá konfigurace se neverzují.
`composer.json` i `composer.lock` jsou součástí Gitu. Po naklonování spusťte
z kořene repozitáře:

```powershell
cd nette-blog
composer install
```

Složky `temp/` a `log/` musí být zapisovatelné. V tomto lokálním prostředí
je ověřené PHP 8.5.12 pro Apache i Composer; požadavky konkrétních závislostí
ověříte příkazem `composer check-platform-reqs`.

Na Jakubově počítači je hlavní pracovní kopie ve složce VYDA v Proton Drive.
`C:\Users\jakub\xampp\htdocs\vyda` odkazuje na celý repozitář a
`C:\Users\jakub\xampp\htdocs\nette-blog` na tuto podsložku pomocí Windows
junction. Oba odkazy musí mířit na aktuální umístění projektu. Web se otevírá na adresách:

- <http://localhost/nette-blog/www/>
- <http://localhost/vyda/nette-blog/www/>

Adresa s koncovým `index.php` přesměruje na úvodní stránku bez názvu souboru.

Junction slouží pouze místnímu XAMPP a není součástí Gitu. Na jiném počítači
lze nastavit vlastní Apache s veřejným kořenem `nette-blog/www`, případně
spustit z adresáře `nette-blog` vestavěný server:

```powershell
php -S localhost:8000 -t www
```

Potom otevřete <http://localhost:8000/>. Adresáře `app/`, `config/`, `log/`
a `temp/` nesmějí být veřejně přístupné.


Cvičení 6: úvodní stránka blogu
-----------------------------

Stav odpovídá kapitolám [Píšeme první aplikaci](https://doc.nette.org/cs/quickstart)
a [Úvodní stránka blogu](https://doc.nette.org/cs/quickstart/home-page),
probíraným na cvičení 3. 11. 2026. Aplikace načítá nejvýše pět nejnovějších
příspěvků z tabulky `posts` a vykresluje je pomocí Latte. Rozsah cvičení končí
u tohoto výpisu.

`app/Presentation/Home/HomePresenter.php` získává `Nette\Database\Explorer`
konstruktorem. Metoda `renderDefault()` předává články do šablony
`app/Presentation/Home/default.latte`, která zobrazuje datum, název a náhled
obsahu.

Místní připojení používá již vytvořenou databázi `test` s tabulkou `posts`.
V návodu se databáze jmenuje `quickstart`; v tomto projektu zůstává jméno
zvolené při cvičení. Tabulka obsahuje sloupce `id`, `title`, `content`
a `created_at`.

Sdílené nastavení je v `config/common.neon`. Osobní přihlašovací údaje
patří do ignorovaného `config/local.neon`, který Bootstrap načítá jako poslední.
Na dalším počítači vytvořte tento soubor z příkladu a vyplňte vlastní přihlášení:

```powershell
Copy-Item config/local.neon.example config/local.neon
composer install
composer check-platform-reqs
```

Databázi a tabulku připravte podle uvedené kapitoly Nette. Na tomto počítači
zůstávají zachované tři dosavadní články. Apache potřebuje zápis do `temp/`
a `log/`; ve Windows nesmějí mít tyto složky a jejich podsložky atribut
„Jen pro čtení“.

Ověřeno 3. 11. 2026 na PHP 8.5.12: načtení přes
<http://localhost/nette-blog/www/> s HTTP 200, shoda tří zobrazených článků
s databází včetně pořadí, data a obsahu, syntaxe aplikačních PHP souborů
a kontroly Composeru.


Cvičení 7: model a přihlašování
------------------------------

Implementováno podle kapitol [Model](https://doc.nette.org/cs/quickstart/model)
a [Autentifikace](https://doc.nette.org/cs/quickstart/authentication).

`app/Model/PostFacade.php` soustřeďuje výběr publikovaných článků. Metoda
`getPublicArticles()` vyřazuje články s budoucím datem a řadí je od nejnovějšího.
`HomePresenter` dostává fasádu konstruktorem a omezuje výpis na pět článků.
Služba se registruje automaticky díky pravidlu `*Facade` v `config/services.neon`.

Přihlášení je na `Sign:in`, odhlášení na `Sign:out`. Pro tento místní výukový
blog je podle návodu připraven účet **admin**, heslo **secret**, v sekci
`security.users` souboru `config/common.neon`. Místní přepsání údajů lze zadat
do ignorovaného `config/local.neon`.

`EditPresenter::startup()` povolí vytváření a úpravu článků jen přihlášenému
uživateli, včetně přímého odeslání formuláře. Nepřihlášený návštěvník je
přesměrován na přihlášení. Odkazy na vytvoření a úpravu článku se zobrazují
pouze po přihlášení. Formulář přihlášení chrání CSRF token a při chybných
údajích zobrazí obecnou chybovou zprávu. Čtení článků a komentáře zůstávají veřejné.

Součástí blogu jsou také navazující stránky `Post:show`, `Edit:create`
a `Edit:edit`. Pokud v místní databázi dosud chybí tabulka `comments`,
naimportujte `database/comments.sql`. SQL soubor nevytváří ani nemaže články.
Konfigurace databáze zůstává ve stávajících souborech `config/common.neon`
a `config/local.neon`.

Automatický test modelu používá SQLite v paměti a nemění místní MySQL databázi:

```powershell
composer run tester
```

Ověřeno 10. 11. 2026 na PHP 8.5.12: syntaxe PHP, všechny tři konfigurace
NEON, deset Latte šablon a test modelu. Přes HTTP prošlo 25 kontrol na oddělené
testovací databázi: řazení a omezení výpisu, skrytí budoucích článků, chybné
přihlášení, CSRF, úspěšné přihlášení, vytvoření a úprava článku, odhlášení
a opětovné zablokování editace. Skutečný místní XAMPP vrací HTTP 200 pro
úvodní stránku se třemi stávajícími články i pro přihlašovací formulář.

Typová kontrola `php vendor/bin/phpstan analyse app tests --level=8` hlásí
14 původních diagnostik v dosavadních presenterech a LatteExtension. Porovnání
se zdrojovým kódem před touto úpravou potvrdilo, že implementace nepřidala
žádné nové hlášení. Výsledek PHPStanu tedy zatím není bez chyb.

Ověřeno znovu 11. 11. 2026 po návratu na větev `cv7-10.11.2026`: syntaxe
PHP, test modelu, všech deset Latte šablon a tři konfigurace NEON. Kontrolu
šablon lze spustit z adresáře `nette-blog` příkazem `php latte-lint app`.
Přes místní XAMPP prošlo přihlášení, odmítnutí chybných údajů a formuláře bez
CSRF tokenu, vytvoření a úprava článku, veřejné komentování, odhlášení
a zablokování přímého odeslání editačního formuláře po odhlášení.
Zkušební článek i komentáře byly odstraněny, původní data zůstala zachovaná.

Po přepnutí z větve semestrálního projektu obnovte knihovny příkazem
`composer install` v adresáři `nette-blog` a vlastní nastavení databáze
v ignorovaném souboru `config/local.neon`. Adresy s koncovým `index.php`
jsou přesměrovány na úvodní stránku bez názvu souboru.
