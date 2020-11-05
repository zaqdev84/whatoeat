<?php

use PHPUnit\Framework\TestCase;
/*
    PHPUnit test cases for MenuMaker class
*/

class MenuMakerTest  extends TestCase {
    
    private $MenuMaker;
    private $testInputDir;
    
    protected function setUp():void {
        include_once(dirname(__FILE__).'/../classes/MenuMaker.php');
        $this->MenuMaker = new MenuMaker();
        
        $this->testInputDir = dirname(__FILE__) . '/../input/';
    }
    
    public function testMissingInput() {
        //Both missing
        $result = $this->MenuMaker->findRecipe('whatever.csv', 'something.json');
        $this->assertEquals(FALSE, $result);
        
        //Ingredients missing
        $result = $this->MenuMaker->findRecipe('whatever.csv', $this->testInputDir.'sample/recipes.json');
        $this->assertEquals(FALSE, $result);
        
        //Recipe list missing
        $result = $this->MenuMaker->findRecipe($this->testInputDir.'sample/fridge-list.csv', 'something.json');
        $this->assertEquals(FALSE, $result);
    }
    
    public function testEmptyInput() {
        //Both empty
        $result = $this->MenuMaker->findRecipe($this->testInputDir.'empty/fridge-list.csv', $this->testInputDir.'empty/recipes.json');
        $this->assertEquals(FALSE, $result);
        
        //Ingredients empty
        $result = $this->MenuMaker->findRecipe($this->testInputDir.'empty/fridge-list.csv', $this->testInputDir.'sample/recipes.json');
        $this->assertEquals(FALSE, $result);
        
        //Recipe list empty
        $result = $this->MenuMaker->findRecipe($this->testInputDir.'sample/fridge-list.csv', $this->testInputDir.'empty/recipes.json');
        $this->assertEquals(FALSE, $result);
    }
    
    public function testNoMatchingIngredients() {
        //Fridge list has no matching ingredients
        $result = $this->MenuMaker->findRecipe($this->testInputDir.'no-match/fridge-list.csv', $this->testInputDir.'sample/recipes.json');        
        $this->assertEquals('Order Takeout', $result);
    }
    
    public function testExpiredIngredient() {
        //Mixed salad has expired, so instead of salad sandwich we should get cheese on toast
        $result = $this->MenuMaker->findRecipe($this->testInputDir.'expired/fridge-list.csv', $this->testInputDir.'expired/recipes.json');
        var_dump($result);
        $this->assertEquals('Grilled Cheese On Toast', $result);
    }
}
?>