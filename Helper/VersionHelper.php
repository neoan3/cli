<?php


namespace Helper;

use Cli\Cli;


class VersionHelper
{
    private Cli $cli;
    public int $appMainVersion = 2;
    public string $casing;
    public function __construct(Cli $cli)
    {
        $this->cli = $cli;
        $this->retrieveNeoanVersion();
        $this->casing = $this->appMainVersion && $this->appMainVersion > 2 ? 'toPascalCase' : 'toCamelCase';
    }
    public function printCliVersion()
    {
        exec('composer global show neoan3/neoan3 -f json', $output, $return);
        $package = json_decode(implode('',$output), true);
        $homepage = 'https://neoan3.rocks';
        $version = 'not recognized';
        if($package){
            $homepage = $package['homepage'];
            $version = $package['versions'][0];
        }
        $this->cli->printLn("Version: $version", 'magenta');
        $this->cli->printLn("Docs: $homepage", 'magenta');
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