<?php 

include(dirname(__FILE__).'/classes/MenuMaker.php');

//Check we have the correct inputs
if(count($argv) != 3) {
    echo "Incorrect arguments. Correct usage: whatoeat.php fridge-list recipe-list\n";
    exit;
}

$menuMaker = new MenuMaker();
$recipe = $menuMaker->findRecipe($argv[1], $argv[2]);

if($recipe !== FALSE) {
    echo $recipe . "\n";
}
else {
    echo $menuMaker->getError() . "\n";
}

