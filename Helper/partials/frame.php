<?php
/**
 * Created by neoan3-cli.
 */

namespace Neoan3\Frame;

use Neoan3\Core\Serve;
use Neoan3\Provider\MySql\Database;
use Neoan3\Provider\MySql\DatabaseWrapper;

/**
 * Class {{name}}
 * @package Neoan3\Frame
 */
class {{name}} extends Serve
{

    /**
     * @var Database|DatabaseWrapper
     */
    protected Database $db;

    /**
     * Demo constructor.
     * @param Database|null $db
     */
    function __construct(Database $db = null)
    {
        parent::__construct();
        if($db){
            $this->db = $db;
        } else {
            $credentials = getCredentials('{{name.lower}}_db');
            $this->db = new DatabaseWrapper($credentials);
        }
    }

    /**
     * @param $model
     * @return mixed
     */
    function model($model)
    {
        $model::init($this->db);
        return $model;
    }

    /**
     * Overwriting Serve's constants()
     * @return array
     */
    function constants()
    {
        return [
            'base' => [base],
            'link' => [
                [
                    'sizes' => '32x32',
                    'type' => 'image/png',
                    'rel' => 'icon',
                    'href' => 'asset/neoan-favicon.png'
                ]
            ],
            'stylesheet' => [
                'https://unpkg.com/tailwindcss@^1.0/dist/tailwind.min.css',
            ]
        ];
    }
}
