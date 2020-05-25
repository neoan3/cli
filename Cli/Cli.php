<?php


namespace Cli;


use Creation\Creation;

class Cli
{
    public $arguments = [];
    public $flags = [];
    public $workPath;

    function __construct()
    {
        global $argv;
        $intermittent = $argv;
        array_shift($intermittent);
        $this->workPath = getcwd();
        $this->argumentConstructor($intermittent);
    }
    private function argumentConstructor($arguments)
    {
        foreach ($arguments as $arg){
            preg_match('/^-{1,2}[a-z0-9]+/i', $arg, $matches);

            if(!empty($matches)){
                $this->flags[] = preg_replace('/-/', '', $arg);
            } else {
                $this->arguments[] = $arg;
            }
        }
    }
    function printLn($msg)
    {
        echo $msg . "\n";
    }
    function run()
    {
        switch ($this->arguments[0]){
            case 'new':
                new Creation();
        }
    }

}