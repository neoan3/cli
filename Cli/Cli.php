<?php


namespace Cli;


use Creation\Creation;

class Cli
{
    public array $arguments = [];
    public array $flags = [];
    public string $workPath;
    public string $ascii = "\n" .
    "::::    ::: :::::::::: ::::::::      :::     ::::    :::  ::::::::  \n" .
    ":+:+:   :+: :+:       :+:    :+:   :+: :+:   :+:+:   :+: :+:    :+: \n" .
    ":+:+:+  +:+ +:+       +:+    +:+  +:+   +:+  :+:+:+  +:+        +:+ \n" .
    "+#+ +:+ +#+ +#++:++#  +#+    +:+ +#++:++#++: +#+ +:+ +#+     +#++:  \n" .
    "+#+  +#+#+# +#+       +#+    +#+ +#+     +#+ +#+  +#+#+#        +#+ \n" .
    "#+#   #+#+# #+#       #+#    #+# #+#     #+# #+#   #+#+# #+#    #+# \n" .
    "###    #### ########## ########  ###     ### ###    ####  ########  \n\n";
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
    function printLn($msg)
    {
        echo $msg . "\n";
    }

    function displayAcii()
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
    function run()
    {
        switch ($this->arguments[0]){
            case 'new':
                new Creation($this);
                break;
            case 'develop':
                $this->displayAcii();
                $this->io('php -S localhost:8080 _neoan/server.php');
                break;
        }
    }

}