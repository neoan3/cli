<?php

use Creation\Creation;
use Cli\Cli;
use PHPUnit\Framework\TestCase;

class CreationTest extends TestCase
{
    private Cli $cli;
    private Creation $instance;
    static function setUpBeforeClass(): void
    {
        if(!file_exists(dirname(__DIR__).'/playground')){
            mkdir(dirname(__DIR__).'/playground');
        }
    }


    static function tearDownAfterClass(): void
    {
        $h = new Helper\FileHelper(new Cli([],dirname(__DIR__)));
        if(file_exists(dirname(__DIR__).'/playground')){
            unlink(dirname(__DIR__).'/playground/.htaccess');
            $h->deleteRecursively(dirname(__DIR__).'/playground');
        }

    }

    public function testNeeded()
    {
        $missingArg = new Creation(new Cli(['neoan3-cli','new'],dirname(__DIR__) .'/playground'));
        $this->assertFalse(
            $missingArg->workable
        );
    }

    public function test__construct()
    {
        $ok = new Creation(new Cli(['neoan3-cli','new','app'],dirname(__DIR__) .'/playground'));
        sleep(3);
        $this->assertTrue($ok->workable);
    }
}
