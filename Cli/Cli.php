<?php


namespace Cli;


use Creation\Creation;
use Migration\Migration;

class Cli
{
    public array $arguments = [];
    public array $flags = [];
    public string $workPath;
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
    function io($execString, $warning = "Warning: Command did not return\n")
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
        switch ($this->arguments[0]){
            case 'new':
                new Creation($this);
                break;
            case 'test':
                $this->io('php ' . $this->workPath . '/vendor/phpunit/phpunit/phpunit --configuration ' . $this->workPath . '/phpunit.xml');
                break;
            case 'migrate':
                new Migration($this);
                break;
            case 'develop':

                $this->displayAscii();
                $this->printLn('Build something amazing today!', 'green');
                $this->printLn('##############################', 'green');
                $this->printLn('Starting development server. Press Ctrl+C / Command+C to quit.');
                $this->io('php -S localhost:8080 _neoan/server.php');
                break;
        }
    }

}