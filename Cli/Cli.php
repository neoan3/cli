<?php


namespace Cli;


use Creation\Creation;
use Credentials\Credentials;
use Helper\ServerHelper;
use Helper\VersionHelper;
use Migration\DatabaseWrapper;
use Migration\Migration;
use Set\Set;

class Cli
{
    public array $arguments = [];
    public array $flags = [];
    public string $workPath;
    public VersionHelper $versionHelper;
    public array $globalVars;
    public string $ascii = "\e[32m\n" .
    "::::    ::: :::::::::: ::::::::      :::     ::::    :::  ::::::::  \n" .
    ":+:+:   :+: :+:       :+:    :+:   :+: :+:   :+:+:   :+: :+:    :+: \n" .
    ":+:+:+  +:+ +:+       +:+    +:+  +:+   +:+  :+:+:+  +:+        +:+ \n" .
    "+#+ +:+ +#+ +#++:++#  +#+    +:+ +#++:++#++: +#+ +:+ +#+     +#++:  \n" .
    "+#+  +#+#+# +#+       +#+    +#+ +#+     +#+ +#+  +#+#+#        +#+ \n" .
    "#+#   #+#+# #+#       #+#    #+# #+#     #+# #+#   #+#+# #+#    #+# \n" .
    "###    #### ########## ########  ###     ### ###    ####  ########  \n\n\e[39m";
    private array $output = [];

    function __construct($arguments, $workPath)
    {
        $this->workPath = $workPath;
        array_shift($arguments);
        $this->argumentConstructor($arguments);
        $this->versionHelper = new VersionHelper($this);
        $this->globalVars = json_decode(file_get_contents(dirname(__DIR__) . '/bin/_vars.json'), true);
    }
    private function argumentConstructor($arguments)
    {
        foreach ($arguments as $arg){
            preg_match('/^-{1,2}([a-z0-9]+):*([a-z0-9.]*)/i', $arg, $matches);
            if(!empty($matches[1])){
                $this->flags[$matches[1]] = !empty($matches[2]) ? $matches[2] : true;
            } else {
                $this->arguments[] = $arg;
            }
        }
    }
    function printLn($msg, $color='')
    {
        $colors = [
            'green' => "\e[32m",
            'yellow' => "\e[33m",
            'red' => "\e[31m",
            'magenta' => "\e[95m",
            '' => "\e[39m"
        ];
        echo ( isset($colors[$color]) ? $colors[$color] : '' ) . $msg . "\n\e[39m";
    }

    function displayAscii()
    {
        echo $this->ascii;
    }
    function io($execString, $warning = "Command did not return anything\n")
    {
        exec($execString, $this->output, $return);
        if (empty($this->output)) {
            $this->clearOutput();
            echo $warning;
            return false;
        }
        $this->printOutput();
        return true;
    }
    function clearOutput()
    {
        $this->output = [];
    }

    function printOutput()
    {
        foreach ($this->output as $line) {
            echo $line . "\n";
        }
        $this->clearOutput();
    }

    function waitForSingleInput($closure)
    {
        if(!function_exists('readline_callback_handler_install')){
            $this->waitForInput($closure);
            return;
        }
        readline_callback_handler_install('', function() { });
        $wait = true;
        $inputBytes = '';
        while ($wait) {
            $r = array(STDIN);
            $w = NULL;
            $e = NULL;
            $n = stream_select($r, $w, $e, null);

            if ($n) {
                $inputBytes .= ord(stream_get_contents(STDIN, 1));
                // possible inputs
                switch ($inputBytes){
                    case '279165':
                        $wait = $this->closeStream();
                        $closure('up');
                        break;
                    case '279133':
                        $wait = $this->closeStream();
                        $closure('down');
                        break;
                    case '27': // wait
                    case '2791': // wait
                        break;
                    case '48':
                        $wait = $this->closeStream();
                        $closure(0);
                        break;
                    case '49':
                        $wait = $this->closeStream();
                        $closure(1);
                        break;
                    case '50':
                        $wait = $this->closeStream();
                        $closure(2);
                        break;
                    case '51':
                        $wait = $this->closeStream();
                        $closure(3);
                        break;
                    case '52':
                        $wait = $this->closeStream();
                        $closure(4);
                        break;
                    case '53':
                        $wait = $this->closeStream();
                        $closure(5);
                        break;
                    case '54':
                        $wait = $this->closeStream();
                        $closure(6);
                        break;
                    case '55':
                        $wait = $this->closeStream();
                        $closure(7);
                        break;
                    case '56':
                        $wait = $this->closeStream();
                        $closure(8);
                        break;
                    case '57':
                        $wait = $this->closeStream();
                        $closure(9);
                        break;
                    case '120':
                        $wait = $this->closeStream();
                        $closure('x');
                        break;
                    case '101':
                        $closure($e);
                        break;
                    case '89':
                    case '121':
                    case '10':
                        $wait = $this->closeStream();
                        $closure('default');
                        break;
                    case '78':
                    case '110':
                        $wait = $this->closeStream();
                        $closure(false);
                        break;

                }
            }
        }

    }
    private function closeStream()
    {
        if(function_exists('readline_callback_handler_remove')){
            readline_callback_handler_remove();
        }
        
        return false;
    }
    function waitForInput($closure, $hidden = false) {
        if($hidden){
            echo "\033[30;40m";
        }
        $handle = fopen("php://stdin", "r");
        $line = trim(fgets($handle));
        $closure($line);
        fclose($handle);
        if($hidden){
            echo "\033[0m\n";
        }
    }
    function run()
    {


        if(array_search('v', $this->flags )){
            $this->versionHelper->printCliVersion();
            exit();
        }
        if(!isset($this->arguments[0])){
            $this->displayAscii();
            return;
        }
        switch ($this->arguments[0]){
            case 'new':
                new Creation($this, new DatabaseWrapper());
                break;
            case 'test':
                $this->io('php ' . $this->workPath . '/vendor/phpunit/phpunit/phpunit --configuration ' . $this->workPath . '/phpunit.xml');
                if($this->versionHelper->appMainVersion > 2){
                    $s = new ServerHelper($this);
                    $s->startCoverageServer();
                }
                break;
            case 'set':
                new Set($this);
                break;
            case 'migrate':
                new Migration($this, new DatabaseWrapper());
                break;
            case 'credentials':
                $c = new Credentials($this);
                $c->chooseCredentials();
                $c->displayCredentials();
                break;
            case 'develop':
                $s = new ServerHelper($this);
                $s->startDevServer();
                break;
            default:
                $this->printLn('Unknown command' , 'red');
                $this->printLn('See https://github.com/neoan3/cli' , 'red');
        }
    }

}