<?php


namespace Helper;


use Neoan3\Apps\Db;
use Neoan3\Apps\DbException;

class SqlHelper
{
    private array $usedCredentials;
    function __construct($credentials)
    {
        try{
            $credentials['dev_errors'] = true;
            Db::setEnvironment($credentials);
        } catch (DbException $e) {
            echo "Db connection not established";
            die();
        }
        $this->usedCredentials = $credentials;
    }
    function databaseTables()
    {
        try{
            $tables = [];
            $call = Db::ask(">SHOW tables");
            foreach ($call as $table){
                if(isset($table['Tables_in_'.$this->usedCredentials['name']])){
                    $tables[] = $table['Tables_in_'.$this->usedCredentials['name']];
                }
            }
            return $tables;
        } catch (DbException $e){
            $this->error($e);
        }
    }
    function describeTable($table)
    {
        try{
            return Db::ask(">DESCRIBE `$table`");
        } catch (DbException $e){
            $this->error($e);
        }

    }
    private function error($error)
    {
        echo "SQL issue:\n";
        echo $error->getMessage();
        die();
    }
}
