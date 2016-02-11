<?php

namespace Potato\Tests;

use PHPUnit_Framework_TestCase;
use Potato\Database\DatabaseConnection;

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
     * Seed 3 rows of data into the cars table
     *
     * @return [type] [description]
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

    public static function tearDownAfterClass()
    {
        self::$connection->exec("DROP TABLE IF EXISTS cars");
    }
}
