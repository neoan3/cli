<?php


namespace Migration;


interface DataBase
{

    /**
     * @param $table
     * @param array $condition
     * @param array $extra
     * @return array|null
     */
    function query($table, $condition = [], $extra = []): ?array;

    /**
     * @param $credentials
     */
    function connect($credentials): void;

}