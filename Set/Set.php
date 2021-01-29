<?php
namespace Set;

use Cli\Cli;
class Set
{
    private Cli $cli;
    function __construct(Cli $cli)
    {
        $this->cli = $cli;
        $this->parseCommand();
    }
    function parseCommand()
    {
        if(!isset($this->cli->arguments[1]) || !isset($this->cli->arguments[2])){
            $this->cli->printLn('Malformed command', 'red');
            $this->cli->printLn('neoan3 set <variable> <value>', 'yellow');
            $this->cli->printLn('Example:', 'yellow');
            $this->cli->printLn('"neoan3 set default_ctrl home"', 'yellow');
            return true;
        }
        switch($this->cli->arguments[1]){
            case 'default_ctrl':
            case 'default_404':
                $this->writeDefault($this->cli->arguments[1],$this->cli->arguments[2]);
                break;
            case 'credential-path':
                $this->writeGlobal($this->cli->arguments[1],$this->cli->arguments[2]);
                break;
            default:
                $this->cli->printLn('Unknown or unsupported variable', 'red');
        }
    }
    function writeGlobal($var, $value)
    {
        try{
            $location = dirname(__DIR__) . '/bin/_vars.json';
            $env = json_decode(file_get_contents($location), true);
            $env[$var] = $value;
            file_put_contents($location, json_encode($env));
        } catch (\Exception $e) {
            $this->cli->printLn('Unable to write global config', 'red');
        }

    }

    function writeDefault($defaultKey, $newDefault)
    {
        $pattern = "/(define\('$defaultKey',\s*')([a-z0-9]+)('\);)/i";
        if($current = $this->handleDefaultFile()){
            $output = preg_replace($pattern, "$1$newDefault$3", $current);
            $this->handleDefaultFile($output);
        }

    }
    function handleDefaultFile($newContent = false)
    {
        $path = $this->cli->workPath . '/default.php';
        if(!is_writable($path)){
            $this->cli->printLn('Cannot locate default.php. Wrong directory?', 'red');
            return false;
        }
        if($newContent){
            file_put_contents($path, $newContent);
            return true;
        }
        return file_get_contents($path);
    }
}