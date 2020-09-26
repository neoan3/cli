<?php


use Migration\Migration;
use PHPUnit\Framework\TestCase;

require_once 'MockCli.php';
require_once 'MockDatabaseWrapper.php';

class MigrationTest extends TestCase
{
    private \Migration\DataBase $mockDb;
    private string $workpath;

    static function setUpBeforeClass(): void
    {
        if (!file_exists(dirname(__DIR__) . '/playground')) {
            mkdir(dirname(__DIR__) . '/playground');
        }
    }

    protected function setUp(): void
    {
        $this->workpath = dirname(__DIR__) . '/playground';
        $this->mockDb = new MockDatabaseWrapper([]);
    }
    function testMalformed()
    {
        $cli = new MockCli(['neoan3-cli', 'migrate', 'models'], $this->workpath);
        new Migration($cli, $this->mockDb);
        $this->expectOutputRegex('/Malformed command/');
        $cli = new MockCli(['neoan3-cli', 'migrate', 'model', 'test', 'right'], $this->workpath);
        new Migration($cli, $this->mockDb);
        $this->expectOutputRegex('/Malformed command/');


    }

    function testProcess()
    {
        foreach (['up','down'] as $direction){
            $cli = new MockCli(['neoan3-cli', 'migrate', 'models', $direction], $this->workpath);
            // user input
            $cli->addInput('x');
            $cli->addInput('cli-migration-test');
            $cli->addInput('');
            $cli->addInput('cli_test_db');
            $cli->addInput('');
            $cli->addInput('');
            $cli->addInput('');
            $cli->addInput('');

            // get known tables
            $this->mockDb->expectedOutcomes[] = [
                [
                    'Tables_in_cli_test_db' => 'test'
                ]
            ];
            $this->mockDb->expectedOutcomes[] = [
                [
                    'Field' => 'id',
                    'Key' => 'PRI',
                    'Type' => 'BINARY',
                    'Null' => 'No',
                    'Default' => false,
                    'Extra' => ''
                ],
                [
                    'Field' => 'name',
                    'Key' => '',
                    'Type' => 'varchar(255)',
                    'Null' => 'Yes',
                    'Default' => false,
                    'Extra' => ''
                ],
            ];

            new Migration($cli, $this->mockDb);
            $this->expectOutputRegex('/done/');
        }

    }
}
