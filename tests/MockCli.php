<?php

namespace Neoan\Installer\Tests;

use Neoan\Installer\Cli\Cli;

class MockCli extends Cli
{
    public int $inputStep = 0;
    public array $inputArray = [];

    function addInput($input)
    {
        $this->inputArray[] = $input;
    }

    function waitForSingleInput($callback)
    {
        $result = $this->inputArray[$this->inputStep];
        echo "-> $result\n";
        $this->inputStep++;
        $callback($result);
    }

    function waitForInput($callback, $hidden = false)
    {
        $result = $this->inputArray[$this->inputStep];
        echo "-> $result\n";
        $this->inputStep++;
        $callback($result);
    }
}
