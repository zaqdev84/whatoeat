<?php
/*
    Menu Finder Class   
    
    @author ZAQDev
    @version 1.0
*/
class MenuMaker {
    
    //List of ingredients in the fridge
    private $fridgeContents = array();
    
    //List of recipes
    private $recipes = array();
    
    //Holds any error message
    private $errorStr = '';
    
    function __construct() {
    }
    
    /*
        Finds the best matching recipe with the ingredients
        
        @param $fridgeContentsCsvFile (string)
        @parm $recipesJsonFile (string)
        @return mixed
    */
    public function findRecipe($fridgeContentsCsvFile, $recipesJsonFile) {
        if(!$this->_parsefridgeContents($fridgeContentsCsvFile)) {
            return FALSE;
        }
        if(!$this->_parseRecipes($recipesJsonFile)) {
            return FALSE;
        }
        
        //For each recipe check if we have all the ingredients in the fridge
        $matchingRecipeIndexes = array();
        foreach($this->recipes as $recipeIndex=>$recipe) {
            $ingredientsInThisRecipeMatched = 0;
            
            //Keep track of the nearest use-by date in case we need it again
            $closestUseByDate = FALSE;

            foreach($recipe->ingredients as $ingredient) {
                if(isset($this->fridgeContents[strtolower($ingredient->item)])) {
                    $fridgeItem = $this->fridgeContents[strtolower($ingredient->item)];
                    
                    //Check we have the same units and enough in the fridge
                    if($ingredient->unit == $fridgeItem['unit'] AND $ingredient->amount <= $fridgeItem['amount']) {
                        $ingredientsInThisRecipeMatched++;
                        
                        if($closestUseByDate === FALSE OR $fridgeItem['use-by'] < $closestUseByDate) {
                            $closestUseByDate = $fridgeItem['use-by'];
                        }
                    }
                }
            }
            
            //Do we have all the ingredits for the recipe?
            if($ingredientsInThisRecipeMatched == count($recipe->ingredients)) {
                $matchingRecipeIndexes[] = array('recipe-index' => $recipeIndex, 'closest-use-by' => $closestUseByDate);
            }
        }
        
        
        
        if(count($matchingRecipeIndexes) == 0) {
            return 'Order Takeout';
        }
        else {
            //Sort recipes by closest use-by date if more than one
            usort($matchingRecipeIndexes, 'self::_sortByUseByDate');
            return ucwords($this->recipes[$matchingRecipeIndexes[0]['recipe-index']]->name);
        }
    }
    
    /*
        Returns any error message
    
        @return string
    */
    public function getError() {
        return $this->errorStr;
    }
    
    //Custom sort method to sort by nearest use by date
    private function _sortByUseByDate($a, $b) {
        if($a['closest-use-by'] == $b['closest-use-by']) {
            return 0;
        }
        
        return ($a['closest-use-by'] < $b['closest-use-by']) ? -1 : 1;
    }
    
    /*
        Reads and parses the fridge ingredients list
        
        @param $fridgeContentsCsvFile string
        @return boolean
    */ 
    private function _parsefridgeContents($fridgeContentsCsvFile) {
        //Fix any mac issues with line endings
        ini_set("auto_detect_line_endings", true);
        
        $fridgeContentsHandle = @fopen($fridgeContentsCsvFile, 'r');
        if($fridgeContentsHandle === FALSE) {
            $this->errorStr = 'Unable to open fridge list';
            return FALSE;
        }
        
        $currentTimestamp = time();
        while(($row = fgetcsv($fridgeContentsHandle, 1000, ',')) !== FALSE) {
            //Check its a valid item
            if(count($row) != 4) {
                continue;
            }
            
            //Replace / by . in the date so its not presumed to be in American format by php
            $useBy = strtotime(str_replace('/', '.', $row[3]));
            
            //Check its still in date - ignore out of date items
            if($useBy > $currentTimestamp)  {
                //Index by item so easier to find matches
                $this->fridgeContents[strtolower($row[0])] = array(
                    'amount' => $row[1],
                    'unit' => $row[2],
                    'use-by' => $useBy,
                );
            }
        }
        fclose($fridgeContentsHandle);
        
        //Do we have something in the fridge to work with?
        if(count($this->fridgeContents) < 1) {
            $this->errorStr = 'There is nothing in the fridge';
            return FALSE;
        }
        
        return TRUE;
    }
    
    /*
        Reads and parses the recipe json file
        
        @param $recipesJsonFile string
        @return boolean
    */ 
    private function _parseRecipes($recipesJsonFile) {
        $recipeListStr = @file_get_contents($recipesJsonFile);
        if($recipeListStr === FALSE) {
            $this->errorStr = "Cannot open recipes list";
            return FALSE;
        }
        
        $this->recipes = json_decode($recipeListStr);
        
        if(!is_array($this->recipes) OR empty($this->recipes)) {
            $this->errorStr = "Unable to parse recipes list or its empty";
            return FALSE;
        }
        
        return TRUE;
    }
    
}
?>