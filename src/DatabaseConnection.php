<?php

namespace Potato\Database;

use PDO;
use PDOException;

/**
 * Class configures connection to the database
 *
 * Loads database details from .env, configures DSN details and returns a PDO connection
 *
 */
class DatabaseConnection
{
    /**
     * Associative array of Database configuratioon settings
     * @var array
     */
    protected static $dbConfig = [];

    /**
     * Constructor
     *
     * Load .env package by https://github.com/vlucas/phpdotenv
     *
     * Populate $dbConfig class variable with database connection settings
     */
    public function __construct()
    {
        $dotenv = new \Dotenv\Dotenv(__DIR__ . "/..");
        $dotenv->load();

        self::$dbConfig = [
            "driver" => getenv('DB_DRIVER'),
            "username" => getenv('DB_USERNAME'),
            "password" => getenv('DB_PASSWORD'),
            "database" => getenv('DB_NAME'),
            "host" => getenv('DB_HOST'),
            "port" => getenv('DB_PORT')
        ];

    }

    /**
     * Fetch database connection details
     * and return a PDO connection or throw error
     *
     * @return object PDO Object
     */
    public static function connect()
    {
        new static;
        self::configConnection();

        try {

            if (self::$dbConfig["driver"] === "sqlite") {

                $connection = new PDO(
                    "sqlite:" . self::$dbConfig["database"]
                );
            } else {

                $connection = new PDO(
                    self::$dbConfig["dsn"],
                    self::$dbConfig["username"],
                    self::$dbConfig["password"]
                );

            }

            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {

            echo $e->getMessage();
        }

        return $connection;
    }

    /**
     * construct PDO dsn string
     *
     * @return void
     */
    public static function configConnection()
    {
        switch (self::$dbConfig["driver"]) {

            case "mysql":
                $dsn = "mysql:host=" . self::$dbConfig['host'];
                $dsn .= ";dbname=" . self::$dbConfig['database'];
                break;

            case "pgsql":
                $dsn = "pgsql:host=" . self::$dbConfig['host'];
                $dsn .= ";port=". self::$dbConfig['port'] .";dbname=" . self::$dbConfig['database'];
                break;
            default:
                $dsn = "pgsql:host=" . self::$dbConfig['host'];
                $dsn .= ";port=". self::$dbConfig['port'] .";dbname=" . self::$dbConfig['database'];
                break;
        }

        self::$dbConfig["dsn"] = $dsn;
    }
}