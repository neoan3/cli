<?php


namespace Cli;


use Creation\Creation;

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
                    case '279165': $closure('up'); $wait = false;
                        break;
                    case '279133': $closure('down'); $wait = false;
                        break;
                    case '27': // wait
                    case '2791': // wait
                        break;
                    case '48': $closure(0); $wait = false;
                        break;
                    case '49': $closure(1); $wait = false;
                        break;
                    case '50': $closure(2); $wait = false;
                        break;
                    case '51': $closure(3); $wait = false;
                        break;
                    case '52': $closure(4); $wait = false;
                        break;
                    case '53': $closure(5); $wait = false;
                        break;
                    case '89':
                    case '121':
                    case '10': $closure('default'); $wait = false;
                        break;
                    case '78':
                    case '110': $closure(false); $wait = false;
                        break;

                }


            }
        }
    }
    function waitForInput($closure) {
        $handle = fopen("php://stdin", "r");
        $line = trim(fgets($handle));
        $closure($line);
        fclose($handle);
    }
    function run()
    {
        switch ($this->arguments[0]){
            case 'new':
                new Creation($this);
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