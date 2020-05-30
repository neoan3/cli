<?php


use Cli\Cli;
use PHPUnit\Framework\TestCase;

class CliTest extends TestCase
{

    public function testDisplayAcii()
    {
        $cli = new Cli([], __DIR__);
        $this->expectOutputString($cli->ascii);
        $cli->displayAcii();

    }

}
