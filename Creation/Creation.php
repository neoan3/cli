<?php


namespace Creation;


use Cli\Cli;

class Creation
{
    private Cli $cli;
    public bool $workable = true;
    private \Migration\DataBase $database;

    function __construct(Cli $cli, \Migration\DataBase $dataBase)
    {
        $this->cli = $cli;
        $this->database = $dataBase;
        if($this->needed()){
            if(!in_array($this->cli->arguments[1],['component','frame', 'model', 'app', 'test', 'database'])){
                $this->cli->printLn('Unknown command', 'red');
                exit();
            }
            $this->run();
        } else{
            $this->workable = false;
        }
    }
    function run()
    {
        $class = '\\Creation\\' . ucfirst($this->cli->arguments[1]);
        $runner = new $class($this->cli, $this->database);
        return $runner->init();
    }

    function needed()
    {
        // new [what] [name]
        if(!isset($this->cli->arguments[1])){
            $this->cli->printLn("missing argument <Type>");
            return false;
        }
        return true;
    }
}