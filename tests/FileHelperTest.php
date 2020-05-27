<?php


use Helper\FileHelper;
use PHPUnit\Framework\TestCase;

class FileHelperTest extends TestCase
{
    private FileHelper $instance;

    protected function setUp(): void
    {
        $cli = new Cli\Cli([],__DIR__. '/mockDrop');
        $this->instance = new FileHelper($cli);
        if(!file_exists($cli->workPath)){
            mkdir($cli->workPath);
        }
    }
    static function tearDownAfterClass(): void
    {
        $c = new FileHelper(new \Cli\Cli([],__DIR__. '/mockDrop'));
        $c->deleteRecursively(__DIR__ . '/mockDrop/');
    }

    public function testDownload()
    {
        $this->createMockFile();
        $this->instance->download(__DIR__ . '/mock.txt', 'download.txt');
        $this->assertFileExists(__DIR__ . '/download.txt');
    }

    public function testDeleteRecursively()
    {
        mkdir(__DIR__ . '/deletable');
        file_put_contents(__DIR__. '/deletable/mock.txt', 'mock2');
        $this->instance->deleteRecursively(__DIR__ . '/deletable/');
        $this->assertDirectoryDoesNotExist(__DIR__ . '/deletable');
    }

    public function testUnZip()
    {
        $this->createMockFile();
        $z = new ZipArchive();
        $z->open(__DIR__ . '/zip.zip', ZipArchive::CREATE);
        $z->addEmptyDir('folder');
        $z->addFile(__DIR__ . '/mock.txt');
        $z->close();
        $this->instance->unZip(__DIR__ . '/zip.zip','_zip');
        $this->assertDirectoryExists(__DIR__ . '/_zip');

    }
    public function testFailUnZip()
    {
        $this->expectException(\Exception::class);
        $this->instance->unZip(__DIR__. '/noSuchZip.zip','_zipme');
    }

    public function testCopyDir()
    {
//        $this->createMockFile();
        $p = __DIR__;
        if(!file_exists($p . '/_source')){
            mkdir($p . '/_source');
        }
        if(!file_exists($p . '/_destination')){
            mkdir($p . '/_destination');
        }
        file_put_contents($p . '/_source/mock.txt','another mock');
        $this->instance->copyDir('_source', '_destination');
        $this->assertDirectoryExists($p .'/_destination');
        $this->assertFileExists($p . '/_destination/mock.txt');
    }
    private function createMockFile()
    {
        $mockFile = __DIR__ . '/mock.txt';
        file_put_contents($mockFile,'mock file');
        return $mockFile;
    }
}
