<?php

use Creation\Creation;
use Cli\Cli;
use PHPUnit\Framework\TestCase;

class CreationTest extends TestCase
{
    private Creation $cli;

    public function setUp(): void
    {
        $this->cli = new Creation();
    }

    public function testNeeded()
    {
        $this->cli->arguments = ['neoan3-cli','new'];
        $this->assertFalse($this->cli->run());
    }

    public function test__construct()
    {

    }
}
