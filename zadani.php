<?php
declare(strict_types=1);
?>
<html>
<head>
    <title>Skupinový úkol — minikonference</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" type="text/css" media="screen" href="../bootstrap/css/bootstrap.css"/>
</head>
<body>
<div class="container py-4">

    <h1>Skupinový úkol: přihláška týmu na minikonferenci</h1>
    <p class="lead">Skupina 2–3 studenti. Jeden soubor <code>index.php</code>. Bootstrap ze cvičení.</p>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h4">Co stránka dělá</h2>
            <p class="mb-0">Skupina se přihlásí s projektem na studentskou minikonferenci. Formulář se odešle na stejnou stránku. Když údaje nesedí, stránka vypíše chyby a formulář zůstane. Když sedí, místo formuláře se ukáže karta přihlášky.</p>
        </div>
    </div>

    <h2 class="h4">Formulář</h2>
    <p>Nadpis stránky: <strong>Přihláška týmu na minikonferenci</strong>. Formulář má <code>method="post"</code>. Pole:</p>
    <ol>
        <li><strong>Název týmu</strong> — text, povinné, nejvýše 40 znaků.</li>
        <li><strong>Člen 1, Člen 2, Člen 3</strong> — tři textová pole, všechna povinná.</li>
        <li><strong>Název projektu</strong> — text, povinné.</li>
        <li><strong>Téma</strong> — <code>select</code>. První položka je prázdná („— vyberte —“). Další jsou přesně tyto, v tomto pořadí:
            <ul>
                <li><code>web</code> — Webová aplikace</li>
                <li><code>mobile</code> — Mobilní aplikace</li>
                <li><code>data</code> — Práce s daty</li>
                <li><code>other</code> — Jiné</li>
            </ul>
        </li>
        <li><strong>Popis</strong> — <code>textarea</code>, povinný, alespoň 20 znaků.</li>
    </ol>
    <p>Tlačítko: <strong>Odeslat přihlášku</strong>, třída <code>btn btn-success</code>.</p>

    <h2 class="h4">Kontrola</h2>
    <p>Po odeslání sestavte pole chyb a vypište ho přes <code>foreach</code>. Povolená témata mějte v poli a kontrolujte je přes <code>in_array</code>. Délku počítejte přes <code>strlen</code>.</p>
    <ul>
        <li>Prázdný název týmu: „Vyplňte název týmu.“</li>
        <li>Název delší než 40 znaků: „Název týmu může mít nejvýše 40 znaků.“</li>
        <li>Prázdný člen: „Vyplňte jméno člena 1.“ a stejně pro člena 2 a 3.</li>
        <li>Prázdný projekt: „Vyplňte název projektu.“</li>
        <li>Téma mimo seznam: „Vyberte téma projektu ze seznamu.“</li>
        <li>Popis kratší než 20 znaků: „Popis musí mít alespoň 20 znaků.“</li>
    </ul>
    <p>Při chybě červený <code>alert alert-danger</code> s větou „Přihlášku teď nejde přijmout:“ a seznamem chyb. Formulář zůstane a už vyplněné hodnoty v něm zůstanou.</p>

    <h2 class="h4">Výstup po přijetí</h2>
    <p>Formulář se schová. Zelený <code>alert alert-success</code> s textem „Přihláška je přijatá.“ Pod ním karta:</p>
    <ul>
        <li>V hlavičce karty název týmu.</li>
        <li><strong>Projekt:</strong> název projektu.</li>
        <li><strong>Téma:</strong> český název z asociativního pole, tedy „Webová aplikace“, ne klíč <code>web</code>.</li>
        <li><strong>Členové:</strong> tři jména spojená přes <code>implode</code> řetězcem „, “.</li>
        <li>Celý popis.</li>
        <li>Řádek „Délka popisu: N znaků, M slov.“ Počet slov vezměte z <code>explode</code> podle mezery. Prázdné kusy se nepočítají, použijte <code>foreach</code>.</li>
        <li>Řádek „Odesláno: “ a čas z <code>date('d.m.Y H:i')</code>.</li>
        <li>Když popis obsahuje <code>PHP</code> nebo <code>php</code> (<code>str_contains</code>), přidejte větu: „V popisu je PHP. Na konferenci bude stát za zmínku, kterou část PHP použijete.“</li>
    </ul>
    <p>Pod kartou odkaz <strong>Nová přihláška</strong> na <code>index.php</code>. Vše, co přišlo z formuláře, vypisujte přes <code>htmlspecialchars</code>. Čtení pole z <code>$_POST</code> a escapování dejte do vlastních funkcí.</p>

    <h2 class="h4">Vzhled</h2>
    <p>Bootstrap ze cvičení: <code>container</code>, <code>form-group</code>, <code>form-control</code>, <code>form-row</code> u tří členů, <code>btn</code>, <code>alert</code>, <code>card</code>.</p>

    <h2 class="h4">Odevzdání</h2>
    <ul>
        <li>Soubor <code>index.php</code>. Bootstrap nechte odkazem na složku ze cvičení.</li>
        <li>Na začátku souboru komentář se jmény členů skupiny.</li>
    </ul>

</div>
</body>
</html>
