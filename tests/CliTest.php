<?php


use Cli\Cli;
use PHPUnit\Framework\TestCase;

class CliTest extends TestCase
{

    public function testDisplayAscii()
    {
        $cli = new Cli([], __DIR__);
        $this->expectOutputString($cli->ascii);
        $cli->displayAscii();

    }
    public function testArgumentConstructor()
    {
        $cli = new Cli(['','-flag'], __DIR__);
        $this->assertArrayHasKey('flag', $cli->flags);
    }
    public function testColors()
    {
        $cli = new Cli([], __DIR__);
        $this->expectOutputRegex("/\e\[32ma/");
        $cli->printLn('a', 'green');
    }
}
