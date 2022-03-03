<?php

namespace Neoan\Installer\Migration;

interface Queryable
{
    /**
     * @param $credentials
     */
    function connect($credentials) : void;

    /**
     * @param $table
     * @param  array  $condition
     * @param  array  $extra
     * @return array|null
     */
    function query($table, $condition = [], $extra = []) : ?array;

}