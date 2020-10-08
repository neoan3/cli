<?php


namespace Helper;

use Cli\Cli;


class VersionHelper
{
    private Cli $cli;
    public string $appMainVersion;
    public function __construct(Cli $cli)
    {
        $this->cli = $cli;
        $this->retrieveNeoanVersion();
    }
    public function printCliVersion()
    {
        exec('composer global show neoan3/neoan3 -f json', $output, $return);
        $package = json_decode(implode('',$output), true);
        $this->cli->printLn("Version: " . $package['versions'][0], 'magenta');
        $this->cli->printLn("Docs: " . $package['homepage'], 'magenta');
    }
    private function retrieveNeoanVersion()
    {
        if(file_exists($this->cli->workPath . '/version.json')){
            $version = json_decode(\file_get_contents($this->cli->workPath . '/version.json'),true);
            if(isset($version['version'])){
                preg_match('/\d+/', $version['version'], $matches);
                $this->appMainVersion = (int) $matches[0];
            }
        }
    }
}