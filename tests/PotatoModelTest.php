<?php

namespace Potato\Tests;

use PHPUnit_Framework_TestCase;
use Potato\Database\DatabaseConnection;
use Potato\Manager\PotatoModel;
use Potato\Tests\Car;

class PotatoModelTest extends PHPUnit_Framework_TestCase
{
    /**
     * Contains a PDO Connection Object returned by the
     * Database Connection class
     *
     * @var object
     */
    protected static $connection;

    /**
     * setUpBeforeClass
     *
     * Run Fixture to prepare the test cars table
     */
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
     * Assert that the last updated name field is Beetle
     *
     * Aseert that the last updated model is Mulsanne Range
     *
     * Assert that the last updated year is 2015
     *
     * @return void
     */
    public function testFindAndUpdateRecord()
    {
        $car = Car::find(5);
        $car->name = "Beetle";
        $affectedRows = $car->save();

        $cars = Car::getAll();

        $this->assertTrue($car instanceof PotatoModel);
        $this->assertEquals(1, $affectedRows);

        $this->assertEquals("Beetle", $cars[4]["name"]);
        $this->assertEquals("Mulsanne Range", $cars[4]["model"]);
        $this->assertEquals(2015, $cars[4]["year"]);
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
    }

    /**
     * testDestroyRecord
     *
     * Delete last car inserted.
     *
     * Assert that delete method returns true
     *
     * assert that returned getAll array has 4 cars
     *
     * Assert that car with id of 5 is not in cars table
     *
     * @return [type] [description]
     */
    public function testDestroyRecord()
    {
        $deleteCar = Car::destroy(5);
        $cars = Car::getAll();

        $this->assertTrue($deleteCar);
        $this->assertEquals(4, count($cars));

        $this->AssertNotContains(5, $cars);
        $this->AssertNotContains("Beetle", $cars);
    }

    /**
     * tearDownAfterClass
     *
     * DROP teat table cars
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        self::$connection->exec("DROP TABLE IF EXISTS cars");
    }
}
