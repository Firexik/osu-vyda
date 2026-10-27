<?php
declare(strict_types=1);
?>

<html>
<head>
    <title>První</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" type="text/css" media="screen" href="./bootstrap/css/bootstrap.css"/>
</head>
<body>

<div class="container">

    <h1>První</h1>

    <?php

    echo "Hello world";

    echo '<h2>Nadpis</h2>' . "\n";

    $text = 'prvni text';

    echo $text . '<br>';

    //inline komentář

    /*
     * komentář 1
     * komentář 2
     */

    //Bolean proměnné, hodnoty true, false

    $bool1 = true;
    echo $bool1 . '<br>';

    //Číselné proměnné
    $num1 = 2;
    echo 'Číselná proměnná: ' . $num1 . '<br>';

    //Float;
    $num2 = 5.2;

    //Základní operace +, -, *, /
    $resultNum = $num1 + $num2;

    echo 'Výsledek: ' . $resultNum . '<br>';

    echo 'Počet znaků proměnné $text: ' . strlen($text) . '<br>';

    //Dlouhý zápis, obsolete
    $numbers = array();
    //Prvni hodnota, inxdex 0
    $numbers[] = 10;
    $numbers[] = 20;
    $numbers[] = 30;

    echo 'První prvek má hodnotu: ' . $numbers[0] . '<br>';

    //Zkrácený zápis - nové pole
    $floats = [];
    $floats[0] = 10.2;
    $floats[4] = 15.7;
    $floats[8] = 16.8;
    $floats[12] = 20.9;

    //Asociativní pole
    $names = [];
    $names['first'] = 'Petr';
    $names['second'] = 'Jana';

    echo 'První jméno má hodnotu: ' . $names['first'] . '<br>';

    //Analogický zápis
    $names2 = ['first' => 'Petr', 'second' => 'Jana'];

    //Konstanty
    define("_VERSION", 2);

    //Podmínka
    $a1 = 10;

    if ($a1 == 10) {
        echo 'Pravda' . '<br>';
    } else {
        echo 'Nepravda' . '<br>';
    }

    //Cykly
    for ($i = 0; $i < 10; $i++) {
        echo $i . '<br>';
    }

    //Klasický foreach
    foreach ($numbers as $number) {
        echo $number . '<br>';
    }

    //Foreach pro asociativní pole
    foreach ($names as $key => $value) {
        echo $key . ': ' . $value . '<br>';
    }

    $numbers[0] > 15 ? $d = 'ano' : $d = 'ne';

    $b1 = 10;
    if ($b1 == 10) {
        echo '10';
    } elseif ($b1 > 10) {
        echo 'vetsi';
    } else {
        echo 'mensi';
    }

    echo '<br>';

    function sum($a, $b)
    {
        return $a + $b;
    }

    echo sum(10, 20);

    echo '<br>';

    $text1 = 'Dnes je krásný den';
    $text2 = str_replace('krásný', 'hezký', $text1);
    echo $text2.'<br>';

    $words = ['one','two','three','four','five'];
    $text3 = implode(' ',$words);
    echo $text3.'<br>';

    $array1 = explode(' ',$text3);

    foreach ($array1 as $value) {
        echo $value.'<br>';
    }

    $a = 10;
    $b = -$a;  //$b = -10
    $c = 10;
    $d = ++$c;  //$c = 11, $d = 11
    echo $c.'<br>';
    echo $d.'<br>';
    $e = 10;
    $f = $e++;  //$f = 10, $e = 11
    echo $f.'<br>';
    echo $e.'<br>';
    $g = 10;
    $h = 2 * ++$g; //$g = 11, $h = 22

    echo '-------<br>';

    $numbers2 = [10,20,30,40];

    if (in_array(15, $numbers2)) {
        echo 'hodnota tam je <br>';
    } else {
        echo 'hodnota tam není <br>';
    }

    $numbers3 = [30,10,40,20,50,70,60];
    echo 'Seřazené prvky pole: <br>';
    asort($numbers3);

    foreach ($numbers3 as $number) {
        echo $number.'<br>';
    }

    echo '<h2>Práce s řetezcem a datumem</h2>';

    //PHP8 funkce
    if (str_contains($text1, 'je')) {
        echo 'obsahuje <br>';
    }

    if (str_starts_with($text1, 'Dnes')) {
        echo 'začíná <br>';
    }

    if (str_ends_with($text1, 'den')) {
        echo 'končí <br>';
    }

    $testText = 'toto je testovaci retezec';
    echo substr($testText, 8).'<br>';
    echo substr($testText, 5, 6).'<br>';
    echo substr($testText, 8, -8).'<br>';
    echo substr($testText, -7).'<br>';

    $today = date('Y-m-d H:i:s');
    echo $today.'<br>';

    $timestampTomorrow = strtotime('+ 1 day');
    echo $tomorrow = date('d.m.Y H:i:s', $timestampTomorrow).'<br>';









?>

</div>
</body>
</html>

