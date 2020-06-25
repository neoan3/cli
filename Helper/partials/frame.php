<?php
/**
 * Created by neoan3-cli.
 */

namespace Neoan3\Frame;

use Neoan3\Core\Serve;

/**
 * Class {{name}}
 * @package Neoan3\Frame
 */
class {{name}} extends Serve
{

    /**
     * Demo constructor.
     */
    function __construct()
    {
        parent::__construct();
        /*
         * General SETUP
         *
         * */

        /*
        // example DB implementation
        try{
            \Neoan3\Apps\Db::setEnvironment(getCredentials()['your_db_credentials']);
        } catch (\Neoan3\Apps\DbException $e){
            echo "Failed to initiate database connection";
            die();
        }*/

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
