<?php


class MockCli extends \Cli\Cli
{
    public int $inputStep = 0;
    public array $inputArray = [];
    function addInput($input)
    {
        $this->inputArray[] = $input;
    }
    function waitForSingleInput($callback)
    {
        $result =  $this->inputArray[$this->inputStep];
        echo "-> $result\n";
        $this->inputStep++;
        $callback($result);
    }
    function waitForInput($callback, $hidden = false)
    {
        $result =  $this->inputArray[$this->inputStep];
        echo "-> $result\n";
        $this->inputStep++;
        $callback($result);
    }
}
