<?php

namespace Neoan\Installer\Tests;

use Exception;
use Neoan\Installer\Helper\SqlHelper;
use Neoan\Installer\Migration\DatabaseWrapper;
use Neoan3\Apps\Db;
use PHPUnit\Framework\TestCase;

class SqlHelperTest extends TestCase
{

    public static function setUpBeforeClass() : void
    {
        define('db_hard_debug', true);
    }

    public function testDatabaseTables()
    {
        $tables = [
            [
                'Tables_in_cli_test_db' => 'test',
            ],
        ];
        $c = new SqlHelper(['name' => 'cli_test_db', 'password' => '1234'], new MockDatabaseWrapper([$tables]));
        $a = $c->databaseTables();
        $this->assertIsArray($a);
    }


    public function testDescribeTable()
    {
        // user real DB, run in debug
        Db::setEnvironment('dev_errors', true);
        Db::debug();
        $c = new SqlHelper(['name' => 'anythigthatdoesnotexist', 'password' => '1234'], new DatabaseWrapper());
        $a = $c->describeTable('dummy');
        $this->assertSame('DESCRIBE `dummy`', $a['sql']);
    }

    public function testFails()
    {
        $c = new SqlHelper(['name' => 'any', 'password' => '1234'], new MockDatabaseWrapper([new Exception('nono1'), new Exception('nono2')]));
        $this->expectOutputRegex('/nono1/');
        $c->databaseTables();
        $this->expectOutputRegex('/nono2/');
        $c->describeTable('some');
    }
}
