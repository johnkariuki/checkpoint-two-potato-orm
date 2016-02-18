#Checkpoint Two: Potato ORM

[![Build Status](https://travis-ci.org/andela-jkariuki/checkpoint-two-potato-orm.svg?branch=master)](https://travis-ci.org/andela-jkariuki/checkpoint-two-potato-orm)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/andela-jkariuki/checkpoint-two-potato-orm/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/andela-jkariuki/checkpoint-two-potato-orm/?branch=master)
[![Coverage Status](https://coveralls.io/repos/github/andela-jkariuki/checkpoint-two-potato-orm/badge.svg?branch=develop)](https://coveralls.io/github/andela-jkariuki/checkpoint-two-potato-orm?branch=develop)
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Software License][ico-license]](https://github.com/andela-jkariuki/checkpoint-two-potato-orm)

##Andela Checkpoint Two

A simple agnostic ORM that can perform the basic crud database operations.

#####TIA

##Install

Via Composer

```bash
$ composer require john-kariuki/potato-orm
```
Via Github

``` bash
$ git clone git@github.com:andela-jkariuki/checkpoint-two-potato-orm.git
```

Update packages

``` bash
$ composer install
```

Rename .env.example to .env

Update your .env to have your database credentials

```bash
DB_DRIVER = "sqlite"
DB_USERNAME = "homestead"
DB_PASSWORD = "secret"
DB_NAME = "potato.db"
DB_HOST = "localhost"
DB_PORT = 8000
```

##Usage

To use Potato ORM, extend the ```PotatoModel``` class. 

The default naming convention for a table name associated with a class is it's lowercase plural syntax.

The default naming convention for the unique table field is id.

To overwrite the default naming conventions, declare protected variables as explained below.

```php
/**
 * Default table name for Car class is cars.
 *
 * Default uniqueId field for table cars is id
 */
class Car extends PotatoModel
{
    //protected static $table = "your_table_name";
    //protected static $uniqueId = "your_unique_id";
}
```
Ensure the table exists before using the Potato ORM class to avoid exceptions.

Use the SQL statement template below

```php
CREATE TABLE `cars` (
	`id`	INTEGER PRIMARY KEY AUTOINCREMENT,
	`name`	TEXT,
	`model`	TEXT,
	`year`	INTEGER
)
```

Potato ORM gives you access to the following methods;

Reading Data
```php
//Get all rows that from a table
$cars = Car::getAll();
```

Inserting data
```php
//insert a new row to the table
$car = new Car();
$car->name = "Boss";
$car->model = "Up";
$car->year = 2013;

$car->save(); //returns a boolean value. true or false if saved or not respectively.
```

Updating data
```php
//Update an existing row in the database
$car = Car::find(1);
$car->name = "Me Hennesy";
$car->year = 2015;
```
Deleting data
```php
//delete an existing row in the table
var_dump(Car::destroy(1));
```
###Sample Code

Ensure to write your code inside a ```try catch``` block to catch any exceptions

```php
namespace DryRun;

use Potato\Manager\PotatoModel;
use PDOException;

require 'vendor/autoload.php';

class Car extends PotatoModel
{
    //protected static $table = "your_table_name";
    //protected static $uniqueId = "your_unique_id";
}
try {

    echo "Create a new Car\n";

    $car = new Car();
    $car->name = "Lambo";
    $car->model = "Hura";
    $car->year = 2013;

    echo $carId = $car->save();

    echo "\nCar has been created with an id of {$carId}\n\n";

    echo  "Find the car that has just been created and updated the name and year\n";

    $car = Car::find($carId);
   
    $car->name = "Huracan";
    $car->year = 2015;


    echo $car->save();

    echo "\nOne row (of our new Car) has been updated.\n\n";

    echo "Get all cars and print them out\n\n";
    $cars = Car::getAll();
    print_r($cars);

    echo "\nAwesome.! Now let's delete the old car. Buy A new one if you can\n\n";

    var_dump(Car::destroy($carId));
    print_r(Car::getAll());

    echo "\nYour old car is dead and gone\n";

} catch (PDOException $e) {
    echo $e->getMessage();
}
```
## Contributing

Contributions are **welcome** and will be fully **credited**.

We accept contributions via Pull Requests on [Github](https://github.com/andela-jkariuki/checkpoint-two-potato-orm).

## Pull Requests

- **[PSR-2 Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)** - The easiest way to apply the conventions is to install [PHP Code Sniffer](http://pear.php.net/package/PHP_CodeSniffer).

- **Add tests!** - Your patch won't be accepted if it doesn't have tests.

- **Document any change in behaviour** - Make sure the `README.md` and any other relevant documentation are kept up-to-date.

- **Consider our release cycle** - We try to follow [SemVer v2.0.0](http://semver.org/). Randomly breaking public APIs is not an option.

- **Create feature branches** - Don't ask us to pull from your master branch.

- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.

- **Send coherent history** - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please [squash them](http://www.git-scm.com/book/en/v2/Git-Tools-Rewriting-History#Changing-Multiple-Commit-Messages) before submitting.

## Security

If you discover any security related issues, please email [John Kariuki](john.kariuki@andela.com) or create an issue.

## Credits

[John kariuki](https://github.com/andela-jkariuki)

## License

### The MIT License (MIT)

Copyright (c) 2016 John kariuki <john.kariuki@andela.com>

> Permission is hereby granted, free of charge, to any person obtaining a copy
> of this software and associated documentation files (the "Software"), to deal
> in the Software without restriction, including without limitation the rights
> to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
> copies of the Software, and to permit persons to whom the Software is
> furnished to do so, subject to the following conditions:
>
> The above copyright notice and this permission notice shall be included in
> all copies or substantial portions of the Software.
>
> THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
> IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
> FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
> AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
> LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
> OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
> THE SOFTWARE.

[ico-version]: https://img.shields.io/packagist/v/john-kariuki/potato-orm.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/john-kariuki/potato-orm.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/john-kariuki/potato-orm
[link-downloads]: https://packagist.org/packages/john-kariuki/potato-orm
