<?php
  declare(strict_types=1);
?>

<html>
<head>
<title>První</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" media="screen" href="./bootstrap/css/bootstrap.css" />
</head>
<body>

<div class="container">

<h1>První</h1>

<?php
echo "Hello world!";

echo '<h2>Nadpis</h2>' . "\n";

$text = 'prvni text';

echo $text . "<br>";

//inline komentář

/*
 * komentář 1
 * komentář 2
 */

//Boolean proměnná. Hodnoty true/false.

$bool1 = true;
echo $bool1 . "<br>";

//Číselné proměnné

$num1 = 3;
echo "Číselná proměnná: " . $num1 . "<br>";

//float
$num2 = 4.4;
echo $num2 . "<br>";

//Základní operace
$resultNum = $num1 + $num2;
echo "Výsledek: " . $resultNum . "<br>";

//Funkce
echo "Počet znaků proměnné text: " . strlen($text) . "<br>";

//Array
$numbers = array();
//První hodnota, index 0
$numbers[] = 10;
$numbers[] = 20;
$numbers[] = 30;

echo "První prvek má hodnotu: " . $numbers[0] . "<br>";

//Zkrácený zápis - nové pole
$floats = [];
$floats[0] = 10.2;
$floats[4] = 15.7;
$floats[8] = 16.8;
$floars[12] = 20.9;

//Asociativní pole
$names = [];
$names['first'] = 'Petr';
$names['second'] = 'Jana';

echo 'První jméno má hodnotu: ' . $names['first'] . "<br>";

//Analogický zápis
$names2 = ['first' => 'Petr', 'second' => 'Jana'];

//Konstanty
define("VERSION", 2);

//Podmínka
$a1 = 10;

if ($a1 == 10){
    echo "Pravda" . "<br>";
}
else{
    echo "Nepravda" . "<br>";
}

//Cykly
for ($i = 0; $i < 10; $i++){
    echo $i . "<br>";
}

//Klasický foreach
foreach ($numbers as $number){
    echo $number . "<br>";
}

foreach ($floats as $number){
    echo $number . "<br>";
}

//foreach pro asociativní pole
foreach ($names as $key => $value){
    echo $key . ": " . $value . "<br>";
}

$numbers[0] > 15 ? $d = "ano" : $d = "ne";

$b1 = 10;
if ($b1 == 10){
    echo "10";
}
elseif ($b1 > 10){
    echo "větší";
}
else{
    echo "menší";
}

echo '<br>';

//new *
function sum($a, $b){
    return $a + $b;
}

echo sum(10, 20);

?>

</div>
</body>
</html>

