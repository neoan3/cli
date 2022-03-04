<?php

namespace Neoan\Installer\Tests;

use Neoan\Installer\Migration\Migration;
use Neoan\Installer\Migration\Queryable;
use PHPUnit\Framework\TestCase;

class MigrationTest extends TestCase
{
    private Queryable $mockDb;
    private string $workpath;

    static function setUpBeforeClass() : void
    {
        if (!file_exists(dirname(__DIR__) . '/playground')) {
            mkdir(dirname(__DIR__) . '/playground');
        }
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
        foreach (['down', 'up', 'model'] as $direction) {
            if ($direction !== 'model') {
                $cli = new MockCli(['neoan3-cli', 'migrate', 'models', $direction], $this->workpath);
                // get known tables
                $this->mockDb->expectedOutcomes[] = [
                    [
                        'Tables_in_cli_test_db' => 'test',
                    ],
                ];
            } else {
                $cli = new MockCli(['neoan3-cli', 'migrate', 'model', 'test', 'up'], $this->workpath);
            }

            // user input
            $cli->addInput('x');
            $cli->addInput('cli-migration-test');
            $cli->addInput('');
            $cli->addInput('cli_test_db');
            $cli->addInput('');
            $cli->addInput('');
            $cli->addInput('');
            $cli->addInput('');


            $this->mockDb->expectedOutcomes[] = [
                [
                    'Field'   => 'id',
                    'Key'     => 'PRI',
                    'Type'    => 'BINARY',
                    'Null'    => 'No',
                    'Default' => false,
                    'Extra'   => '',
                ],
                [
                    'Field'   => 'user_id',
                    'Key'     => 'UNI',
                    'Type'    => 'BINARY',
                    'Null'    => 'No',
                    'Default' => false,
                    'Extra'   => '',
                ],
                [
                    'Field'   => 'name',
                    'Key'     => '',
                    'Type'    => 'varchar(255)',
                    'Null'    => 'Yes',
                    'Default' => false,
                    'Extra'   => '',
                ],
            ];

            // for insert catching
            $this->mockDb->expectedOutcomes[] = [];

            new Migration($cli, $this->mockDb);
            $this->expectOutputRegex('/done/');
        }

    }

    function testDefaultQuotes()
    {
        $mockDb = new MockDatabaseWrapper();
        $migrate = new Migration(new MockCli(['neoan3-cli', '-v'], $this->workpath), $mockDb);
        $one = $migrate->sqlRow('key', ['default' => 'sam', 'type' => 'varchar(255)', 'key' => false, 'nullable' => false]);
        $two = $migrate->sqlRow('key', ['default' => 1, 'type' => 'int(11)', 'key' => false, 'nullable' => true]);
        $this->assertMatchesRegularExpression('/`key`/', $one);
    }

    protected function setUp() : void
    {
        $this->workpath = dirname(__DIR__) . '/playground';
        $this->mockDb = new MockDatabaseWrapper([]);
    }


}
