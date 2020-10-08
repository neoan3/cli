<?php

use Creation\Creation;
use Cli\Cli;
use PHPUnit\Framework\TestCase;

require_once 'MockCli.php';

class CreationTest extends TestCase
{
    private Cli $cli;
    private Creation $instance;
    private string $workpath;
    static function setUpBeforeClass(): void
    {
        if(!file_exists(dirname(__DIR__).'/playground')){
            mkdir(dirname(__DIR__).'/playground');
        }
    }
    protected function setUp(): void
    {
        $this->workpath = dirname(__DIR__).'/playground';
    }

    static function tearDownAfterClass(): void
    {
       /* $h = new Helper\FileHelper(new Cli([],dirname(__DIR__)));
        if(file_exists(dirname(__DIR__).'/playground')){
            unlink(dirname(__DIR__).'/playground/.htaccess');
            unlink(dirname(__DIR__).'/playground/.travis.yml');
            $h->deleteRecursively(dirname(__DIR__).'/playground');
        }*/

    }

    public function testNeeded()
    {
        $missingArg = new Creation(new Cli(['neoan3-cli','new'],$this->workpath));
        $this->assertFalse(
            $missingArg->workable
        );
    }

    public function test__construct()
    {
        $ok = new Creation(new Cli(['neoan3-cli','new','app'],$this->workpath));
        sleep(3);
        $this->assertTrue($ok->workable);
    }
    public function testTestCreationFailure()
    {
        // test warning
        new Creation(new Cli(['neoan3-cli','new','test','component','endpoint'],$this->workpath));
        $this->expectOutputRegex('/already exists/');
    }
    public function testTestCreationMalFormed()
    {
        // test malformed
        new Creation(new Cli(['neoan3-cli','new','test','component'],$this->workpath));
        $this->expectOutputRegex('/Malformed command/');
    }
    public function testTestCreation()
    {
        // create component
        $create = new Creation(new Cli(['neoan3-cli','new','component','newComponent', '-f:demo', '-t:api'],$this->workpath));
        // test test creation
        new Creation(new Cli(['neoan3-cli','new','test','component','newComponent'], $this->workpath));
        $this->assertFileExists($this->workpath . '/component/newComponent/NewComponentTest.php');

    }
    public function testComponentExists()
    {
        // test warning
        new Creation(new Cli(['neoan3-cli','new','component','endpoint'],$this->workpath));
        $this->expectOutputRegex('/already exists/');
    }
    public function testComponentMalformed()
    {
        // test malformed
        new Creation(new Cli(['neoan3-cli','new','component'],$this->workpath));
        $this->expectOutputRegex('/Malformed command/');
    }
    public function testComponent()
    {

        // test ask for type
        $cli = new MockCli(['neoan3-cli','new','component','askTypes'],$this->workpath);
        // choose route
        $cli->addInput(0);
        // choose if view
        $cli->addInput('y');
        // choose if frame
        $cli->addInput('y');
        // choose frame demo
        $cli->addInput(0);
        new Creation($cli);
        $this->expectOutputRegex('/What type of component/');
    }
    public function testFrame()
    {
        // test warning
        new Creation(new Cli(['neoan3-cli','new','frame','demo'],$this->workpath));
        $this->expectOutputRegex('/already exists/');
        // test malformed
        new Creation(new Cli(['neoan3-cli','new','frame'],$this->workpath));
        $this->expectOutputRegex('/Malformed command/');
        // ok
        new Creation(new Cli(['neoan3-cli','new','frame','testFrame'],$this->workpath));
        $this->assertFileExists($this->workpath . '/frame/testFrame/TestFrame.php');
    }
    public function testModel()
    {
        // ok
        new Creation(new Cli(['neoan3-cli','new','model','test'],$this->workpath));
        $this->assertFileExists($this->workpath . '/model/test/Test.model.php');
        // test warning
        new Creation(new Cli(['neoan3-cli','new','model','test'],$this->workpath));
        $this->expectOutputRegex('/already exists/');
        // test malformed
        new Creation(new Cli(['neoan3-cli','new','model'],$this->workpath));
        $this->expectOutputRegex('/Malformed command/');

    }
}
