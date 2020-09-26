<?php


namespace Migration;


use Exception;
use Neoan3\Apps\Db;
use Neoan3\Apps\DbException;

class DatabaseWrapper implements DataBase
{
    /**
     * @param $table
     * @param array $condition
     * @param array $extra
     * @return array|null
     * @throws Exception
     */
    function query($table, $condition = [], $extra = []) : ?array
    {
        try{
            return Db::ask($table, $condition, $extra);
        } catch (DbException $e){
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param $credentials
     * @throws Exception
     */
    function connect($credentials) :void
    {
        try{
            Db::setEnvironment($credentials);
        } catch (DbException $e){
            throw new Exception($e->getMessage());
        }
    }
}