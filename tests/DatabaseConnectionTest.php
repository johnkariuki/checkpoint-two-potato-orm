<?php

namespace Potato\Tests;

use Potato\Database\DatabaseConnection;
use PHPUnit_Framework_Testcase;
use PDO;

class DatabaseConnectionTest extends PHPUnit_Framework_Testcase
{
    /**
     * testDatabaseConnection
     *
     * Test connect method creates a PDO database connection
     *
     * Test close method closes a database connection
     *
     * @return void
     */
    public function testDatabaseConnection()
    {
        $connection = DatabaseConnection::connect();

        $this->assertTrue(is_object($connection));
        $this->assertTrue($connection instanceof PDO);

        $connection = DatabaseConnection::close();

        $this->assertNull($connection);
    }
}
