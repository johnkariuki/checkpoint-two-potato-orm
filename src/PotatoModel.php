<?php

namespace Potato\Manager;

use Potato\Database\DatabaseConnection;
use ReflectionClass;
use PDO;
use PDOException;

/**
 * Class Potato Model: Base model that allows reading data from a
 * particular table.
 *
 * Child classes can inherit CRUD methods from Potato Model
 */
class PotatoModel extends DatabaseConnection
{
    /**
     * Table name from Child Class.
     *
     * @var string
     */
    protected static $table;

    /**
     * Unique ID Name field from Child Class.
     *
     * @var string
     */
    protected static $uniqueId;

    /**
     * Contains the unique ID value.
     *
     * @var int
     */
    protected static $uniqueIdValue = null;

    /**
     * Contains a PDO Connection Object returned by the
     * Database Connection class.
     *
     * @var object
     */
    protected static $connection = null;

    /**
     * Associative array that contains the name of each field and value
     * as a key value pair.
     *
     * @var array
     */
    protected static $data = [];

    /**
     * If set to true, the save method performs an update on existing row.
     *
     * If set to false, the save method inserts a new row
     *
     * @var bool
     */
    protected static $update = false;

    /**
     * Create database connection.
     *
     * Get all things up and running
     */
    public function __construct()
    {
        self::connect();
    }

    /**
     * Create a PDO connection if one does not exist.
     *
     * @return void
     */
    public static function connect()
    {
        if (is_null(self::$connection)) {
            self::$connection = DatabaseConnection::connect();
        }
    }

    /**
     * Add the value set in the child class a key value pair to the $data array.
     *
     * @param string         $key   Contains the name of the field e.g firstName
     * @param string/integer $value Contains the value of the field e.g John
     */
    public function __set($key, $value)
    {
        self::$data[$key] = $value;
    }

    /**
     * getAll.
     *
     * Returns all rows from a table
     *
     * @return array returns an array of rows in a table
     */
    final public static function getAll()
    {
        self::connect();

        try {
            $getAll = self::$connection->prepare('SELECT * FROM '.self::getTableName());

            if ($getAll->execute()) {
                $result = $getAll->fetchAll(PDO::FETCH_ASSOC);

                return $result;
            }
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * save.
     *
     * public function that saves a new instance of the child class data into the database
     *
     * Create PDO connection, construct SQL Statement, execute the statement
     * and return the primary key value of inserted row
     *
     * @return int return the primary key value of inserted row
     */
    public function save()
    {
        self::connect();

        if (self::$update === false) {
            $sqlQuery = 'INSERT INTO '.self::getTableName();
            $sqlQuery .= ' ('.implode(', ', array_keys(self::$data)).')';
            $sqlQuery .= ' VALUES ('.self::getDataFieldValues(self::$data).') ';
        } else {
            $sqlQuery = 'UPDATE '.self::getTableName();
            $sqlQuery .= ' SET '.self::getUpdateFieldValues(self::$data);
            $sqlQuery .= ' WHERE '.self::getUniqueId().' = '.self::$uniqueIdValue;
        }

        try {
            $query = self::$connection->exec($sqlQuery);

            if ($query) {

                self::$data = [];
                return self::$update ? $query : true;
            }

            throw new PDOException("Error Processing Request: " . self::$update ? "Update" : "Save");

        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * find.
     *
     * Find takes a unique field ID and returns true if a row is found
     * and false if a row is not found
     *
     * @param int $fieldId
     *
     * @return bool true if field returned, else false
     */
    public static function find($fieldId)
    {
        self::connect();

        $sqlQuery = 'SELECT * FROM '.self::getTableName();
        $sqlQuery .= ' WHERE '.self::getUniqueId().' = '.$fieldId;

        try {
            $preparedStatement = self::$connection->prepare($sqlQuery);
            $preparedStatement->execute();

            if ($preparedStatement->fetch(PDO::FETCH_ASSOC)) {
                self::$update = true;
                self::$uniqueIdValue = $fieldId;

                return new static();
            }

            throw new PDOException("No record found with that ID.");
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    public static function findRecord($searchField)
    {
        self::connect();
        try {
            if (is_numeric($searchField)) {

                $sqlQuery = 'SELECT * FROM '.self::getTableName();
                $sqlQuery .= ' WHERE '.self::getUniqueId().' = '.$searchField;
            } elseif (is_array($searchField)) {

                $sqlQuery = 'SELECT * FROM '.self::getTableName();
                $sqlQuery .= ' WHERE ('. self::getWhereClause($searchField) . ')';
            } else {

                throw new PDOException("Invalid search data provided");
            }

            $preparedStatement = self::$connection->prepare($sqlQuery);
            $preparedStatement->execute();

            if ($record = $preparedStatement->fetch(PDO::FETCH_ASSOC)) {
                return $record;
            }

            throw new PDOException("No record found with that ID.");
        } catch (PDOException $e) {

            throw new PDOException($e->getMessage());
        }
    }

    /**
     * destroy.
     *
     * Takes an ID parameter, deletes from the table where the unique ID
     * matches the ID parameter
     *
     * @param int $id a unique ID from the table
     *
     * @return bool should return true or false based on
     *              whether it was deleted or not
     */
    public static function destroy($fieldId)
    {
        self::connect();

        $sqlQuery = 'DELETE FROM '.self::getTableName();
        $sqlQuery .= ' WHERE '.self::getUniqueId().' = '.$fieldId;

        try {
            return self::$connection->exec($sqlQuery) ? true : false;
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    /**
     * getTableName.
     *
     * If a table name is not set in the child class,
     * return a 'snaked' table name.
     *
     * else return the set table name
     *
     * @return string table name
     */
    public static function getTableName()
    {
        if (! isset(static::$table)) {
            $className = new ReflectionClass(new static());

            return strtolower($className->getShortName().'s');
        }

        return static::$table;
    }

    /**
     * getUniqueId.
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
     * getDataFieldValues.
     *
     * return an comma separated string of field value pairs
     * in assoc array
     *
     * @param array $fieldValueArray An associative array of all the field-value pairs
     *
     * @return string $data             A string of comma separated values for SQL statement
     */
    private function getDataFieldValues($fieldValueArray)
    {
        $data = null;

        $arrayKeys = array_keys($fieldValueArray);
        $lastKey = end($arrayKeys);

        foreach (self::$data as $key => $value) {
            $data .= is_string($value) ? "'{$value}'" : $value;

            $data .= ($key !== $lastKey) ? ', ' : '';
        }

        return $data;
    }

    /**
     * getUpdateFieldValues.
     *
     * returns comma sperarated string of field values in the SQL update format
     *
     * @param array $fieldValueArray An associative array of all the field-value pairs
     *
     * @return string comma sperarated string of field values in the SQL update format
     */
    private function getUpdateFieldValues($fieldValueArray)
    {
        $data = null;

        $arrayKeys = array_keys($fieldValueArray);
        $lastKey = end($arrayKeys);

        foreach (self::$data as $key => $value) {
            $data .= is_string($value) ? "$key = '{$value}'" : "{$key} = $value";
            $data .= ($key !== $lastKey) ? ', ' : '';
        }

        return $data;
    }

    public static function getWhereClause(array $arr)
    {
        $data = null;
        $arrayKeys = array_keys($arr);
        $lastKey = end($arrayKeys);

        foreach ($arr as $key => $value) {
            $data .= is_string($value) ? "$key = '{$value}'" : "{$key} = $value";
            $data .= ($key !== $lastKey) ? ' AND ' : '';
        }

        return $data;
    }
}
