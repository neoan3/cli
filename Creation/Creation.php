<?php


namespace Creation;


class Creation
{
    private \Cli\Cli $cli;
    public bool $workable = true;
    function __construct(\Cli\Cli $cli)
    {
        $this->cli = $cli;
        if($this->needed()){
            $this->run();

        } else{
            $this->workable = false;
        }
    }
    function run()
    {
        $class = '\\Creation\\' . ucfirst($this->cli->arguments[1]);
        $runner = new $class($this->cli);
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