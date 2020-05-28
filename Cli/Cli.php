<?php


namespace Cli;


use Creation\Creation;

class Cli
{
    public array $arguments = [];
    public array $flags = [];
    public string $workPath;

    function __construct($arguments, $workPath)
    {

        $this->workPath = $workPath;
        array_shift($arguments);
        $this->argumentConstructor($arguments);
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
                new Creation($this);
        }
    }

}