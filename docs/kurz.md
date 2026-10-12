# Výpis kurzu KIP/7VYDA

Export z přihlášeného Moodlu dne 12. 10. 2026. Zdroj: https://moodle.osu.cz/course/view.php?id=12051

## Úvod

Úvod
Vítejte v kurzu

RNDr. Bogdan Walek, Ph.D.

Kurz byl vytvořen v rámci projektu „Inovace výuky informatických předmětů ve studijních programech Ostravské univerzity",


reg. číslo CZ.1.07/2.2.00/28.0245.

Webové a databázové aplikace v PHP (XVYDA + 2VYDA)


Oznámení
Fórum
Základní informace o kurzu
Stránka
Kompletní studijní opora
Soubor

XAMPP - Apache, MySQL, PHP a Perl v jednom:

XAMPP
URL
Přednáška Protokol HTTP, formuláře v HTML
Soubor
Přednáška Operátory, příkazy, cykly, funkce, práce s řetězcem
Soubor
Přednáška Práce s databází v PHP
Soubor
Přednáška OOP v PHP, frameworky
Soubor
Přednáška Nette framework
Soubor

## Síťový protokol HTTP

Síťový protokol HTTP
Síťový protokol HTTP

V této kapitole se dozvíte:

Jaké jsou základní vlastnosti protokolu HTTP.
Co je to URI.
Jaké jsou metody požadavku strany klienta.
Jaké odpovědi může generovat strana serveru.

Po jejím prostudování byste měli být schopni:

Znát základní vlastnosti protokolu HTTP.
Znát strukturu URI nebo URL identifikátoru.
Rozlišovat jednotlivé metody požadavku strany klienta.
Poznat, zda bylo zpracování požadavku na serveru úspěšné a jaký je jeho výsledek.

Klíčová slova této kapitoly: HTTP, HTML, klient, server, síťový protokol, URL, URI.
Doba potřebná ke studiu: 4 hodiny
Průvodce studiem
Pro studium této kapitoly je vhodné znát alespoň základní pojmy z oblasti počítačových sítí. Studium kapitoly není příliš náročné, seznamuje čtenáře se základními vlastnostmi protokolu HTTP, s URI adresami, s metodami požadavku strany klienta a s druhy odpovědí strany serveru.

Studijní text:

Síťový protokol HTTP
Soubor

## Formuláře v jazyce HTML

Formuláře v jazyce HTML
Formuláře v jazyce HTML
Formuláře v jazyce HTML
Soubor

V této kapitole se dozvíte:

K čemu slouží formuláře v jazyce HTML.
Jaké jsou nejdůležitější formulářové prvky.
Jaké vlastnosti je možné nastavit těmto prvkům.

Po jejím prostudování byste měli být schopni:

Vytvořit formulář v jazyce HTML.
Umět používat jednotlivé formulářové prvky a umět jim nastavovat parametry.

Klíčová slova této kapitoly: HTML, formulář, parametry.
Doba potřebná ke studiu: 4 hodiny
Průvodce studiem
Studium této kapitoly je velmi důležité pro tvorbu webových stránek nebo aplikací v jazyce PHP. Dynamické webové stránky nebo aplikace se neobejdou bez formulářů, protože pomocí nich můžeme získávat vstupy od uživatelů. Studium kapitoly není náročné, čtenáře seznamuje s jednotlivými formulářovými prvky a s jejich vlastnostmi.

Studijní text:

## Jazyk PHP, jeho historie a hlavní rysy

Jazyk PHP, jeho historie a hlavní rysy
Jazyk PHP, jeho historie a hlavní rysy
Jazyk PHP, jeho historie a hlavní rysy
Soubor

Shrnutí obsahu kapitoly
V této krátké kapitole jste seznámili s historií jazyka PHP a s jeho hlavními rysy. Studium kapitoly není náročné, jedná se pouze o doplňující kapitolu.

## Prvni aplikace v PHP, proměnné, konstanty, operátory

Prvni aplikace v PHP, proměnné, konstanty, operátory
Prvni aplikace v PHP, proměnné, konstanty, operátory
První aplikace v PHP, práce s proměnnými a konstantami, operátory
Soubor

Kontrolní otázky:

K čemu slouží příkaz echo?
Jaké jednoduché datové typy můžeme použít v PHP?
Jaká pravidla musí splňovat identifikátor proměnné?
Jaké složitější datové typy můžeme použít v PHP?
Jaké operátory můžeme použít v PHP?

Shrnutí obsahu kapitoly
V této kapitole jste se seznámili s příkazem „echo“, který umožňuje vypsat text do okna prohlížeče. Dále jste se naučili práci s proměnnými, která je velmi důležitá pro tvorbu aplikací v jakémkoliv jazyce. Jazyk PHP umožňuje používat čtyři jednoduché datové typy – logickou hodnotu, celé číslo, desetinné číslo a řetězec znaků. Kromě toho umožňuje použití složitějších datových typů – polí a objektů. Pro tvorbu aplikací je možné někdy využít i tvorbu konstant. Jazyk PHP umožňuje používat unární a binární operátory a jeden ternární operátor.

## Příkazy

Příkazy
Příkazy
Příkazy
Soubor

Kontrolní otázky:

K čemu slouží v PHP příkaz if?
Jaké příkazy cyklu můžeme použít v PHP?
K čemu slouží příkazy break a continue?
Jaký je rozdíl mezi příkazy require a include?
K čemu slouží příkaz exit?

Shrnutí obsahu kapitoly
V této kapitole jste se v úvodu seznámili s příkazem pro vytvoření větvení v PHP. Dále jste se seznámili s jednotlivými typy příkazů cyklu v PHP a s rozdíly mezi nimi. Jazyk PHP umožňuje použít cyklus s podmínkou na
začátku, cyklus s podmínkou na konci i cyklus s řídící proměnnou. Příkazy require a include umožňují vložit do souboru obsah jiného souboru. Příkaz exit umožňuje opustit právě prováděný PHP skript.

## Zpracování vstupu od uživatele

Zpracování vstupu od uživatele
Zpracování vstupu od uživatele
Zpracování vstupu od uživatele
Soubor

Kontrolní otázky:

Jakým způsobem získáme hodnotu parametru odeslaného metodou GET?
Jakým způsobem získáme hodnotu parametru odeslaného metodou POST?
Co je to superglobální proměnná?

Shrnutí obsahu kapitoly
V této kapitole jste se seznámili se dvěma způsoby odeslání parametrů na webový server. První způsob představuje odeslání požadavku metodou GET. Parametry a jejich hodnoty jsou v tomto případě součástí URL adresy. K získání hodnot parametrů slouží superglobální pole $_GET. Druhým způsobem je odeslání požadavku metodou POST, při kterém jsou parametry a jejich hodnoty umístěny v těle požadavku. K získání hodnot parametrů slouží superglobální pole $_POST. Kromě těchto dvou polí obsahuje PHP i další superglobální pole.

Korespondenční úkol č. 1

## Funkce

Funkce
Funkce
Funkce
Soubor

Kontrolní otázky:

K čemu slouží funkce?
Jak vytvoříte funkci v jazyce PHP?
Jakým způsobem je možné předávat parametry funkcím v PHP?
Co je to vnořená funkce?
Co je to rekurzivní funkce?

Shrnutí obsahu kapitoly
V této kapitole jste s předdefinovanými funkcemi v PHP i s uživatelsky definovanými funkcemi. Uživatelsky definované funkce umožňují vytvářet modulární aplikace a snižují chybovost kódu. Definice funkce začíná klíčovým slovem function. Předávání parametrů v jazyce PHP je možné hodnotou a nebo odkazem. Jazyk PHP umožňuje také definici vnořených nebo rekurzivních funkcí. Pro usnadnění tvorby aplikací je v jazyce PHP k dispozici velké množství předdefinovaných funkcí. Nejdůležitějšími skupinami jsou funkce pro práci s polem, se soubory, s řetězci, s databází nebo matematické funkce.

## Práce se soubory

Práce se soubory
Práce se soubory
Práce se soubory

Kontrolní otázky:

Jaké parametry má funkce fopen() a co je jejím návratovým typem?
Jaký je rozdíl mezi funkcemi fgetc(), fgets() a fread()?
Jaký je rozdíl mezi funkcemi fgets() a fgetss()?
Jakými způsoby je možné vypsat obsah konkrétního adresáře?

Shrnutí obsahu kapitoly
V této kapitole jste s předdefinovanými funkcemi v PHP, které slouží pro práci se soubory. K otevření souboru slouží funkce fopen(), k jeho zavření pak fclose(). Čtení ze souboru je možné provést pomocí funkcí fgetc(), fgets(), fgetss() nebo fread(). Zápis do souboru je možný pomocí funkce fwrite(). Funkce copy(), rename() a unlink() slouží ke zkopírování, přejmenování a odstranění souboru. Výpis obsahu adresáře je možný dvěma způsoby – pomocí funkcí nebo pomocí třídy Directory.

## Práce s řetězci

Práce s řetězci
Práce s řetězci
Práce s řetězci
Soubor

Kontrolní otázky:

Jakým způsobem je možné vytisknout řetězec?
Jakou funkcí je možné vytvořit formátovaný řetězec?
Jaký je rozdíl mezi funkcemi implode() a join()?
K čemu slouží funkce substr_count()?

Shrnutí obsahu kapitoly
V této kapitole jste s předdefinovanými funkcemi v PHP, které slouží pro práci s textovými řetězci. Tisk řetězce je možné provést příkazy echo a print. Pro vytvoření formátovaného výstupu slouží funkce printf. K řetězci lze přistupovat jako k poli, pro práci s jednotlivými znaky se používají index uvedené v hranatých nebo složených závorkách. Pomocí funkcí lze v PHP zjistit délku řetězce a najít první nebo poslední výskyt řetězce v jiném řetězci. Úpravu řetězce je možné provést nahrazením části řetězce jiným řetězcem, přidáním obráceného lomítka před speciální znaky nebo nahrazením speciálních znaků jazyka HTML odpovídajícími HTML entitami. Řetězce lze také rozdělit podle zadaného oddělovače na pole dílčích řetězců a nebo je možné prvky pole spojit do jednoho řetězce. Jazyk PHP obsahuje mnoho funkcí, jejich popis v této kapitole není úplný, jsou zmíněny jen některé z funkcí.

## Práce s polem, práce s datem a časem

Práce s polem, práce s datem a časem
Práce s polem, práce s datem a časem
Práce s polem, práce s datem a časem
Soubor

Kontrolní otázky:

Jakým způsobem je možné indexovat prvky pole?
Jaké funkce je možné použít pro vyhledávání v poli?
K čemu slouží funkce mktime()?
K čemu slouží funkce getdate()?

Shrnutí obsahu kapitoly
V této kapitole jste se seznámili s předdefinovanými funkcemi v PHP, které slouží pro práci s polem a kalendářním datem a časem. Jazyk PHP umožňuje vytvářet spojitě nebo nespojitě indexovaná pole nebo asociativní pole. Při práci s polem můžeme použít funkce pro procházení polem, vyhledávání v poli nebo řazení pole. Práce s kalendářním datem a časem je možné procedurálním způsobem (pomocí funkcí) nebo objektově (pomocí třídy DateTime). Zadané datum je možné naformátovat různými způsoby pomocí speciálních formátovacích znaků. Jazyk PHP obsahuje mnoho funkcí, jejich popis v této kapitole není úplný, jsou zmíněny jen některé z funkcí.

## Objektově orientované programování v PHP

Objektově orientované programování v PHP
Objektově orientované programování v PHP


V této kapitole se dozvíte:

Jak vytvářet aplikace v PHP pomocí objektového přístupu.
Jak vytvořit odvozenou třídu.
Jak přetěžovat proměnné a metody.

Po jejím prostudování byste měli být schopni:

Umět vytvořit třídu v PHP.
Umět použít dědičnost při tvorbě tříd.
Umět přetěžovat proměnné a metody.

Klíčová slova této kapitoly: class, konstruktor, destruktor, dědičnost, public, protected, private.
Průvodce studiem
Studium této kapitoly předpokládá alespoň základní znalosti objektově orientovaného programování. Kapitola se nezabývá vysvětlováním principů objektově orientovaného programování, ale vysvětluje tvorbu tříd v jazyce PHP.

Objektově orientované programování v PHP
Soubor

Kontrolní otázky:

Jakým vytvoříme třídu v PHP?
Jaký je rozdíl mezi public, protected a private?
Jak je možné v PHP vytvořit konstruktor a jak destruktor?
Co je to přetěžování proměnných?
Co je to přetěžování metod?

Shrnutí obsahu kapitoly
V této kapitole jste se seznámili s tvorbou tříd v jazyce PHP. Třídy mohou obsahovat členské proměnné a členské metody. Speciálními metodami jsou konstruktor a destruktor, které slouží k vytvoření nebo zrušení instance třídy. Přístup ke členským proměnným nebo metodám může být nastaven jako public, protected nebo private. Jazyk PHP umožňuje dynamicky vytvářet členské proměnné nebo metody. Tato činnost bývá označována jako přetěžování.

## Databáze MySQL

Databáze MySQL
Databáze MySQL


V této kapitole se dozvíte:

Základní informace o databázích MySQL.
Jak se připojit k databázovému serveru.
Jak spouštět SQL příkazy na databázovém serveru.
Jak uvolnit výsledky a uzavřít připojení databázi.

Po jejím prostudování byste měli být schopni:

Umět používat rozhraní mysqli pro práci s databází MySQL.
Umět spouštět libovolné příkazy SQL.
Umět zpracovat výsledky, které vrátil příkaz SELECT.

Klíčová slova této kapitoly: MySQL, mysqli, mysqli_real_connect, mysqli_select_db mysqli_query, mysqli_close mysqli_fetch_row, mysqli_fetch_array, mysqli_fetch_assoc, mysqli_fetch_object, mysqli_free_result.
Průvodce studiem
Studium této kapitoly není náročné. V úvodu kapitoly se krátce seznámíte s databázovým serverem MySQL. Dále se již naučíte pracovat s tímto serverem pomocí jazyka PHP. Práce s databází spočívá ve čtyřech krocích – připojení k databázovému serveru, výběr databáze, spouštění příkazů a uzavření připojení k databázovému serveru.

Databáze MySQL
Soubor

Kontrolní otázky:

Pomocí jakých rozhraní je možné pracovat v PHP s databází MySQL?
Jak provedete připojení k databázi a výběr databáze?
Jaké funkce je možné použít pro zpracování výsledků dotazu SELECT?

Shrnutí obsahu kapitoly

V této kapitole jste se seznámili se základními principy práce s databází MySQL pomocí jazyka PHP. K práci s databází je možné použít dvě rozhraní mysql a mysqli. Rozhraní mysqli je novější, proto je vhodnější. Základní
funkcí pro spouštění příkazů SQL je mysqli_query(). Pomocí této funkce můžeme spouštět libovolné SQL příkazy. Ke zpracování výsledků vrácených po spuštění příkazu SELECT slouží objekt mysqli_result. Procházení záznamů ve výsledku je možné pomocí čtyř různých funkcí. Po ukončení práce s databází bychom měli uvolnit načtené výsledky a uzavřít připojení k databázi.

Korespondenční úkol č. 2

## Semestrální projekt

Semestrální projekt
Semestrální projekt


Vaším úkolem je vytvořit webovou aplikaci v PHP a MySQL. Zadání semestrálního projektu naleznete v popisu Semestrální projekt.

Zdrojové kódy projektu vložte do Moodlu, funkční verzi budete prezentovat u zápočtu.

Semestrální projekt
Úkol
SQL skript
Soubor

## Cvičení soubory a úkoly - pouze pro prezenční studenty

Cvičení soubory a úkoly - pouze pro prezenční studenty
Není dostupné

## Samostatné úkoly, úkoly po cvičení

Samostatné úkoly, úkoly po cvičení
Úkol 1
Soubor
Úkol 2
Soubor

## Vzorové příklady XVYDA a 2VYDA

Vzorové příklady XVYDA a 2VYDA
Není dostupné


