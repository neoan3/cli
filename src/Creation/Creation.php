<?php

namespace Neoan\Installer\Creation;

use Neoan\Installer\Cli\Cli;
use Neoan\Installer\Migration\Queryable;

class Creation
{
    public bool $workable = true;

    private Cli $cli;

    private Queryable $database;

    function __construct(Cli $cli, Queryable $dataBase)
    {
        $this->cli = $cli;

        $this->database = $dataBase;

        if (!$this->needed()) {
            $this->workable = false;

            return;
        }

        if (!in_array($this->cli->arguments[1], ['component', 'frame', 'model', 'app', 'test', 'database'])) {
            $this->cli->printLn('Unknown command', 'red');
            exit();
        }

        $this->run();
    }

    function run()
    {
        $class = '\\Neoan\\Installer\\Creation\\' . ucfirst($this->cli->arguments[1]);
        $runner = new $class($this->cli, $this->database);

        return $runner->init();
    }

    function needed() : bool
    {
        // new [what] [name]
        if (!isset($this->cli->arguments[1])) {
            $this->cli->printLn("missing argument <Type>");

            return false;
        }

        return true;
    }
}