<?php

namespace Neoan\Installer\Helper;

use Exception;
use Neoan\Installer\Migration\Queryable;

class SqlHelper
{
    private array $usedCredentials;

    private Queryable $db;

    function __construct($credentials, Queryable $db)
    {
        $this->db = $db;
        $credentials['dev_errors'] = true;
        $this->db->connect($credentials);
        $this->usedCredentials = $credentials;
    }

    function databaseTables()
    {
        try {
            $tables = [];
            $call = $this->db->query(">SHOW tables");
            foreach ($call as $table) {
                if (isset($table['Tables_in_' . $this->usedCredentials['name']])) {
                    $tables[] = $table['Tables_in_' . $this->usedCredentials['name']];
                }
            }

            return $tables;
        } catch (Exception $e) {
            $this->error($e);
        }
    }

    function describeTable($table)
    {
        try {
            return $this->db->query(">DESCRIBE `$table`");
        } catch (Exception $e) {
            $this->error($e);
        }

    }

    private function error($error)
    {
        echo "SQL issue:\n";
        echo $error->getMessage();
    }
}
