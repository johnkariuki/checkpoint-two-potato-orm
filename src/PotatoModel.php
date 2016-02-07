<?php

namespace Potato\Manager;

use Potato\Database\DatabaseConnection;
use ReflectionClass;
use PDO;
use PDOException;

/**
 * Class Potato Model: Base model that allows reading data from a
 * particular table
 *
 * Child classes can inherit CRUD methods from Potato Model
 */
class PotatoModel extends DatabaseConnection
{
    /**
     * Table name
     * @var string
     */
    protected static $table;

    /**
     * Contains a PDO Connection Object returned by the
     * Database Connection class
     *
     * @var Object
     */
    protected static $connection;

    /**
     * getAll: Returns all rows from a table
     *
     * @return array returns an array of rows in a table
     */
    final public static function getAll()
    {

        try {
        //create connection
            self::$connection = DatabaseConnection::connect();

            $getAll = self::$connection->prepare("SELECT * FROM " . self::getTableName());

            if ($getAll->execute()) {

                $result = $getAll->fetchAll(PDO::FETCH_ASSOC);

                self::$connection  = DatabaseConnection::close();

                return $result;
            }
        } catch (PDOException $e) {
            return  $e->getMessage();
        }
    }

    /**
     * getTableName
     *
     * If a table name is not set in the child class, return a 'snaked' table name.
     *
     * else return the set table name
     *
     * @return string table name
     */
    public static function getTableName()
    {
        if (! isset(static::$table)) {

            $className = new ReflectionClass(new static);
            return strtolower($className->getShortName() . 's');
        }

        return static::$table;
    }
}
