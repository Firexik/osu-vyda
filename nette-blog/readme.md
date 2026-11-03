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

Na Jakubově počítači je hlavní pracovní kopie ve složce VYDA na OneDrivu.
`C:\Users\jakub\xampp\htdocs\nette-blog` na tuto podsložku odkazuje pomocí
Windows junction, takže se web otevírá na původní adrese:

- <http://localhost/nette-blog/www/>

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
