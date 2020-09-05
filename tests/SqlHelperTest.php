<?php


use Helper\SqlHelper;
use PHPUnit\Framework\TestCase;
require_once 'MockCli.php';

class SqlHelperTest extends TestCase
{

    public static function setUpBeforeClass(): void
    {
        define('db_hard_debug', true);
    }

    public function testDatabaseTables()
    {
        $c = new SqlHelper(['name'=>'any', 'password'=>'1234']);
        $a = $c->databaseTables();
        $this->assertIsArray($a);
        $this->assertEmpty($a);
    }



    public function testDescribeTable()
    {
        // run DB in debug mode
        $c = new SqlHelper(['name'=>'any', 'password'=>'1234']);
        $a = $c->describeTable('dummy');
        $this->assertSame('DESCRIBE `dummy`', $a['sql']);
    }
}
