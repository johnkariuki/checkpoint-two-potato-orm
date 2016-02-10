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
     * Associative array that contains the name of each field and value
     * as a key value pair
     *
     * @var array
     */
    protected static $data = [];

    /**
     * Add the value set in the child class a key value pair to the $data array
     *
     * @param [type] $key   Contains the name of the field e.g firstName
     * @param [type] $value Contains the value of the field e.g John
     */
    public function __set($key, $value)
    {
        self::$data[$key] = $value;
    }

    /**
     * getAll
     *
     * Returns all rows from a table
     *
     * @return array returns an array of rows in a table
     */
    final public static function getAll()
    {

        try {

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
     * save
     *
     * public function that saves a new instance of the child class data into the database
     *
     * Create PDO connection, construct SQL Statement, execute the statement
     * and return the number of inserted/saved/affected rows
     *
     * @return integer return the number of inserted/saved/affected rows
     */
    public function save()
    {
        self::$connection = DatabaseConnection::connect();

        $sql = "INSERT INTO " . self::getTableName();
        $sql .= " (" . implode(", ", array_keys(self::$data)). ")";
        $sql .= " VALUES (" . self::getDataFieldValues(self::$data) . ") ";

        try {

            return self::$connection->exec($sql);

        } catch (PDOException $e) {
            return $e->getMessage();
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

    /**
     * [getDataFieldValues description]
     * @param  array    $fieldValueArray  An associative array of all the field-value pairs
     * @return string   $data             A string of comma separated values for SQL statement
     */
    private function getDataFieldValues($fieldValueArray)
    {
        $data = null;

        $arrayKeys = array_keys($fieldValueArray);
        $lastKey = end($arrayKeys);

        foreach (self::$data as $key => $value) {

            if (is_string($value)) {

                $data .= "'{$value}'";

            } else {

                $data .= $value . "";
            }


            $data .= ($key !== $lastKey) ? ", " : "";
        }

        return $data;
    }
}
