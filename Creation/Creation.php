<?php


namespace Creation;


use Cli\Cli;

class Creation extends Cli
{
    function __construct()
    {
        parent::__construct();
        if($this->needed()){
            $class = '\\Creation\\' . ucfirst($this->arguments[1]);
            $runner = new $class();
            $runner->init();
        }
    }

    function needed()
    {
        // new [what] [name]
        if(!isset($this->arguments[1])){
            $this->printLn("missing argument <Type>");
            return false;
        }
        return true;
    }
}