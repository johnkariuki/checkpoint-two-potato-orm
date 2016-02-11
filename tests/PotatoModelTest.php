<?php

namespace Potato\Tests;

use PHPUnit_Framework_TestCase;
use Potato\Database\DatabaseConnection;
use Potato\Manager\PotatoModel;
use Potato\Tests\Car;

class PotatoModelTest extends PHPUnit_Framework_TestCase
{
    protected static $connection;

    public static function setUpBeforeClass()
    {
        self::$connection = DatabaseConnection::connect();

        $sqlQuery = 'CREATE TABLE IF NOT EXISTS cars
			(
				`id`	INTEGER PRIMARY KEY AUTOINCREMENT,
				`name`	TEXT,
				`model`	TEXT,
				`year`	INTEGER
			)';

        self::$connection->exec($sqlQuery);
        self::seedDatabase();
    }

    /**
     * seedDatabase
     *
     * Seed 4 rows of data into the cars table
     *
     * @return
     */
    public static function seedDatabase()
    {
        $statement = self::$connection->prepare('INSERT INTO cars (name, model, year) VALUES(:name, :model, :year)');

        $statement->bindParam(':name', $name);
        $statement->bindParam(':model', $model);
        $statement->bindParam(':year', $year);

        $name = 'Ford';
        $model = 'Mustang';
        $year = 1967;
        $statement->execute();

        $name = 'Nissan';
        $model = 'Versa Sedan';
        $year = 2016;
        $statement->execute();

        $name = 'Lamborghini';
        $model = 'Huracan';
        $year = 2015;
        $statement->execute();

        $name = 'Ferrari';
        $model = 'GTC4 Lusso';
        $year = 2016;
        $statement->execute();
    }

    /**
     * testAddNewRecord
     *
     * Assert that the instantiated class is an instance of the Potato Model
     *
     * Assert that the save method adds a new [5th] record into the cars table
     *
     * For a new insert, the save method returns the ID of the inserted record
     *
     * @return void
     */
    public function testAddNewRecord()
    {
        $car = new Car();

        $car->name = "Bentley";
        $car->model = "Mulsanne Range";
        $car->year = 2015;

        $carId = $car->save();

        $this->assertTrue($car instanceof PotatoModel);
        $this->assertEquals(5, $carId);
    }

    /**
     * testFindRecord
     *
     * Assert that $car is an instance of the PotatoModel
     *
     * Assert that the returned affected rows is equal to one
     *
     * @return void
     */
    public function testFindRecord()
    {
        $car = Car::find(5);
        $car->name = "Beetle";
        $affectedRows = $car->save();

        $this->assertTrue($car instanceof PotatoModel);
        $this->assertEquals(1, $affectedRows);
    }

    /**
     * testgetAllRecords
     *
     * Assert that getAll returns all rows in table
     *
     * Assert that returned row contains 5 rows
     *
     * Assert that each returned row contains all the table colummns
     *
     * Assert that the last updated name field is Beetle
     *
     * @return void
     */
    public function testgetAllRecords()
    {
        $cars = Car::getAll();

        $this->assertTrue(is_array($cars));
        $this->assertEquals(5, count($cars));

        $this->assertArrayHasKey("name", $cars[4]);
        $this->assertArrayHasKey("model", $cars[4]);
        $this->assertArrayHasKey("year", $cars[4]);

        $this->assertEquals("Beetle", $cars[4]["name"]);
        $this->assertEquals("Mulsanne Range", $cars[4]["model"]);
        $this->assertEquals(2015, $cars[4]["year"]);
    }

    public static function tearDownAfterClass()
    {
        self::$connection->exec("DROP TABLE IF EXISTS cars");
    }
}
