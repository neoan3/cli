<?php

namespace Neoan\Installer\Creation;

use Exception;
use Neoan\Installer\Cli\Cli;
use Neoan\Installer\Credentials\Credentials;
use Neoan\Installer\Migration\Queryable;

class Database
{
    private Cli $cli;

    private Queryable $db;

    private Credentials $credentials;

    public function __construct(Cli $cli, Queryable $Db)
    {
        $this->cli = $cli;

        $this->credentials = new Credentials($cli);

        $this->db = $Db;
    }

    public function init()
    {
        $this->cli->printLn('Before creating the database, please create or use the credentials required.');
        $this->cli->printLn('Press enter to continue');

        $this->cli->waitForSingleInput(function ($x) {});

        $format = [
            'host'         => 'localhost',
            'name'         => $this->cli->arguments[2],
            'user'         => 'root',
            'password'     => '',
            'port'         => 3306,
            'assumes_uuid' => 'true',
        ];

        $this->credentials->chooseCredentials($format);

        $this->createDatabase();
    }

    private function createDatabase()
    {
        $withoutName = $this->credentials->currentCredentials;
        $withoutName['dev_errors'] = true;
        unset($withoutName['name']);

        $this->db->connect($withoutName);

        try {

            $this->db->query('>CREATE DATABASE `' . $this->credentials->currentCredentials['name'] . '`');
            $this->cli->printLn('Successfully created database ' . $this->credentials->currentCredentials['name'], 'green');

        } catch (Exception $e) {

            $this->cli->printLn('Failed to run command.', 'red');
            $this->cli->printLn('Either database "' . $this->credentials->currentCredentials['name'] . '" already exists or the connection failed.', 'red');
        }
    }
}