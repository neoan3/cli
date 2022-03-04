<?php

namespace Neoan\Installer\Tests;

use Neoan\Installer\Cli\Cli;
use PHPUnit\Framework\TestCase;

class CliTest extends TestCase
{

    public function testDisplayAscii()
    {
        $cli = new Cli([], __DIR__);
        $this->expectOutputString($cli->ascii);
        $cli->displayAscii();
        $cli2 = new Cli(['neoan3-cli'], __DIR__);
        $this->expectOutputString($cli->ascii);

    }

    public function testArgumentConstructor()
    {
        $cli = new Cli(['', '-flag'], __DIR__);
        $this->assertArrayHasKey('flag', $cli->flags);
    }

    public function testColors()
    {
        $cli = new Cli([], __DIR__);
        $this->expectOutputRegex("/\e\[32ma/");
        $cli->printLn('a', 'green');
    }

    public function testIo()
    {
        $cli = new Cli([], __DIR__);
        $this->expectOutputRegex("/hi/");
        $cli->io('php ' . __DIR__ . "/silent.php");
        $this->expectOutputRegex("/Command did/");
    }

}
