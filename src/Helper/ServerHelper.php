<?php

namespace Neoan\Installer\Helper;

use Neoan\Installer\Cli\Cli;

class ServerHelper
{
    private Cli $cli;

    public function __construct(Cli $cli)
    {
        $this->cli = $cli;
    }

    public function startCoverageServer()
    {
        $this->cli->printLn('Processed. See ' . $this->cli->workPath . '/tests/report/index.html', 'green');
        if (!$this->pingPort(8999)) {
            $this->cli->printLn('##############################', 'green');
            $this->cli->printLn('Coverage server booting up. Press Ctrl+C / Command+C to quit.', 'green');
            $this->cli->io('php -S localhost:8999 tests/coverage');
        } else {
            $this->cli->printLn('The coverage server is either already running at http://localhost:8999 or the port is blocked by another service.', 'magenta');
        }

    }

    public function startDevServer()
    {
        if (!$this->pingPort()) {
            $this->cli->displayAscii();
            $this->cli->printLn('Build something amazing today!', 'green');
            $this->cli->printLn('##############################', 'green');
            $this->cli->printLn('Starting development server. Press Ctrl+C / Command+C to quit.');
            $this->cli->io('php -S localhost:8080 _neoan/server.php');
        } else {
            $this->cli->printLn('Port 8080 seems to be busy. Is something already running?', 'red');
        }

    }

    public function pingPort(int $port = 8080) : bool
    {
        $ping = @fsockopen('localhost', $port);
        if ($ping) {
            fclose($ping);

            return true;
        }

        return false;
    }
}