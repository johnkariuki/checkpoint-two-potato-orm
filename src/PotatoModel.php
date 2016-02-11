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
     * Table name from Child Class
     *
     * @var string
     */
    protected static $table;

    /**
     * Unique ID value from Child Class
     *
     * @var [type]
     */
    protected static $uniqueId;

     /**
     * Contains unique ID value
     *
     * @var null
     */
    protected static $uniqueIdValue = null;

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
     * If set to true, the save method performs an update on existing row
     *
     * If set to false, the save method inserts a new row
     *
     * @var boolean
     */
    protected static $update = false;

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
     * and return the primary key value of inserted row
     *
     * @return integer return the primary key value of inserted row
     */
    public function save()
    {
        self::$connection = DatabaseConnection::connect();

        if (self::$update === false) {

            $sqlQuery = "INSERT INTO " . self::getTableName();
            $sqlQuery .= " (" . implode(", ", array_keys(self::$data)). ")";
            $sqlQuery .= " VALUES (" . self::getDataFieldValues(self::$data) . ") ";
        } else {
            $sqlQuery = "UPDATE " . self::getTableName();
            $sqlQuery .= " SET " . self::getUpdateFieldValues(self::$data);
            $sqlQuery .= " WHERE " . self::getUniqueId() . " = " . self::$uniqueIdValue;
        }

        try {

            $query = self::$connection->exec($sqlQuery);
            self::$data = [];

            return self::$update ? $query : self::$connection->lastInsertId();

        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }


    public static function find($id)
    {
        self::$connection = DatabaseConnection::connect();

        $sqlQuery = "SELECT * FROM " . self::getTableName();
        $sqlQuery .= " WHERE " . self::getUniqueId(). " = ". $id;

        try {
            $preparedStatement = self::$connection->prepare($sqlQuery);
            $preparedStatement->execute();

            if ($row = $preparedStatement->fetch(PDO::FETCH_ASSOC)) {

                self::$update = true;
                self::$uniqueIdValue = $id;

                return new static;
            }

            return false;

        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    /**
     * destroy
     *
     * Takes an ID parameter, deletes from the table where the unique ID
     * matches the ID parameter
     *
     * @param  integer $id a unique ID from the table
     *
     * @return boolean     should return true or false based on
     *                     whether it was deleted or not
     */
    public static function destroy($id)
    {
        self::$connection = DatabaseConnection::connect();

        $sqlQuery = "DELETE FROM " . self::getTableName();
        $sqlQuery .= " WHERE " . self::getUniqueId() . " = " . $id;

        try {

            return self::$connection->exec($sqlQuery) ? true : false;

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
     * getUniqueId
     *
     * If the unique ID is set in the child class, return it
     *
     * else return a default unique ID of 'id'
     *
     * @return string Unique ID table ID
     */
    public static function getUniqueId()
    {
        if (! isset(static::$uniqueId)) {
            return 'id';
        }

        return static::$uniqueId;
    }

    /**
     * getDataFieldValues
     *
     * return an comma separated string of field value pairs
     * in assoc array
     *
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

                $data .= $value;
            }


            $data .= ($key !== $lastKey) ? ", " : "";
        }

        return $data;
    }

    private function getUpdateFieldValues($fieldValueArray)
    {
        $data = null;

        $arrayKeys = array_keys($fieldValueArray);
        $lastKey = end($arrayKeys);

        foreach (self::$data as $key => $value) {

            if (is_string($value)) {

                $data .= "$key = '{$value}'";

            } else {

                $data .= "{$key} = $value";
            }


            $data .= ($key !== $lastKey) ? ", " : "";
        }

        return $data;
    }
}
