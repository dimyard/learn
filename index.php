<?
$countOfStableBottles = 99;

function plural_form($number, $after) {
    $cases = array (2, 0, 1, 1, 1, 2);
    echo $number.' '.$after[ ($number%100>4 && $number%100<20)? 2: $cases[min($number%10, 5)] ];
}

while ($countOfStableBottles > 0) {
    echo(plural_form($countOfStableBottles--, ["бутылка стояла", "бутылки стояло", "бутылок стояло"]) . " на столе. Одна упала. </br>"); //.PHP_EOL
}