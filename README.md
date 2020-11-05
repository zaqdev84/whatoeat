Given a list of items in the fridge (presented as a csv list), and a\n
collection of recipes (a collection of JSON formatted recipes),
produce a recommendation for what to cook tonight.
Program should be written to take two inputs; fridge csv list, and
the json recipe data. How you choose to implement this is up to you;
you can write a console application which takes input file names as
command line args, or as a web page which takes input through a form.
The only rule is that it must run and return a valid result using the
provided input data.

## Usage
```
    $ php ./whatoeat.php <fridge csv file> <recipes json file>
```

### Example run

##### No Input
```
    $ php ./whatoeat.php
    Incorrect arguments. Correct usage: php ./whatoeat.php fridge-list recipe-list
```


##### Normal Run
```
    $ php ./whatoeat.php input/sample/fridge-list.csv input/sample/recipes.json
    Grilled Cheese On Toast
```

## Tests
PHPUnit can be installed via Composer before executing tests. For more details on how to install PHPUnit click [here](http://phpunit.de/manual/current/en/installation.html)

You can run a PHPUnit test with the following command
```
    $ composer update
    $ ./vendor/bin/phpunit
```
