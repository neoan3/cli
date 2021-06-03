<?php


use Add\Package;
use Cli\Cli;
use Helper\FileHelper;
use PHPUnit\Framework\TestCase;

class PackageTest extends TestCase
{
    private FileHelper $instance;
    private Cli $cli;

    static function setUpBeforeClass(): void
    {
        if(!file_exists(__DIR__. '/addPackage')){
            mkdir(__DIR__. '/addPackage');
            mkdir(__DIR__. '/addPackage/component');
            copy(__DIR__ . '/mockComposer.json',__DIR__. '/addPackage/composer.json');
        }
    }
    static function tearDownAfterClass(): void
    {
        $c = new FileHelper(new Cli([],__DIR__. '/addPackage'));
     //   $c->deleteRecursively(__DIR__ . '/addPackage/');
    }
    public function testNeeded()
    {
        $missingArg = new Package(new Cli(['neoan3-cli','add', 'model'],__DIR__. '/addPackage'));
        $this->assertFalse(
            $missingArg->workable
        );
        $missingArg = new Package(new Cli(['neoan3-cli','add'],__DIR__. '/addPackage'));
        $this->assertFalse(
            $missingArg->workable
        );
    }
    public function testComponent()
    {
        new Package(new Cli(['neoan3-cli','add', 'component', 'sroehrl/neoan3-pwa', 'https://github.com/sroehrl/neoan3Pwa.git'],__DIR__. '/addPackage'));
        $this->assertFileExists(__DIR__. '/addPackage/component/Neoan3Pwa/Neoan3PwaController.php');
    }


}
