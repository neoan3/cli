<?php


use Helper\SqlHelper;
use PHPUnit\Framework\TestCase;
require_once 'MockCli.php';
require_once 'MockDatabaseWrapper.php';

class SqlHelperTest extends TestCase
{

    public static function setUpBeforeClass(): void
    {
        define('db_hard_debug', true);
    }

    public function testDatabaseTables()
    {
        $tables = [
            [
                'Tables_in_cli_test_db' => 'test'
            ]
        ];
        $c = new SqlHelper(['name'=>'any', 'password'=>'1234'], new MockDatabaseWrapper($tables));
        $a = $c->databaseTables();
        $this->assertIsArray($a);
        $this->assertEmpty($a);
    }



    public function testDescribeTable()
    {
        // user real DB, run in debug
        $c = new SqlHelper(['name'=>'any', 'password'=>'1234'], new \Migration\DatabaseWrapper());
        $a = $c->describeTable('dummy');
        $this->assertSame('DESCRIBE `dummy`', $a['sql']);
    }
}
