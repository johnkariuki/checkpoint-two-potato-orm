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
     *
     * @var array
     */
    protected static $dbConfig = [];

    /**
     * PDO connection
     *
     * @var object
     */
    protected static $connection;

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
        self::configDsn();

        try {

            if (self::$dbConfig["driver"] === "sqlite") {
                self::$connection = new PDO(
                    self::$dbConfig["dsn"]
                );
            } else {

                self::$connection = new PDO(
                    self::$dbConfig["dsn"],
                    self::$dbConfig["username"],
                    self::$dbConfig["password"]
                );

            }

            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {

            echo $e->getMessage();
        }

        return self::$connection;
    }

    /**
     * Terminate database connection
     *
     * @return null close PDO DATABASE connection
     */
    public static function close()
    {
        self::$connection = null;
        return null;
    }

    /**
     * construct PDO dsn string
     * 
     * @codeCoverageIgnore
     * 
     * @return void
     */
    public static function configDsn()
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
            case "sqlite":
                $dsn = "sqlite:" . self::$dbConfig["database"];
                break;
            default:
                $dsn = "sqlite:" . self::$dbConfig["database"];
                break;
        }

        self::$dbConfig["dsn"] = $dsn;
    }
}
