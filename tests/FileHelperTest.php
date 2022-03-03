<?php

namespace Neoan\Installer\Tests;

use Exception;
use Neoan\Installer\Cli\Cli;
use Neoan\Installer\Helper\FileHelper;
use PHPUnit\Framework\TestCase;
use ZipArchive;

class FileHelperTest extends TestCase
{
    private FileHelper $instance;
    private Cli $cli;

    static function tearDownAfterClass() : void
    {
        $c = new FileHelper(new Cli([], __DIR__ . '/mockDrop'));
        $c->deleteRecursively(__DIR__ . '/mockDrop/');
    }

    public function testDownload()
    {
        $this->createMockFile();
        $this->instance->download($this->cli->workPath . '/mock.txt', 'download.txt');
        $this->assertFileExists($this->cli->workPath . '/download.txt');
    }

    public function testDeleteRecursively()
    {
        mkdir($this->cli->workPath . '/deletable');
        file_put_contents($this->cli->workPath . '/deletable/mock.txt', 'mock2');
        $this->instance->deleteRecursively($this->cli->workPath . '/deletable/');
        $this->assertDirectoryDoesNotExist($this->cli->workPath . '/deletable');
    }

    public function testUnZip()
    {
        $this->createMockFile();
        $z = new ZipArchive();
        $z->open($this->cli->workPath . '/zip.zip', ZipArchive::CREATE);
        $z->addEmptyDir('folder');
        $z->addFile($this->cli->workPath . '/mock.txt');
        $z->close();
        $this->instance->unZip($this->cli->workPath . '/zip.zip', '_zip');
        $this->assertDirectoryExists($this->cli->workPath . '/_zip');

    }

    public function testFailUnZip()
    {
        $this->expectException(Exception::class);
        $this->instance->unZip($this->cli->workPath . '/noSuchZip.zip', '_zipme');
    }

    public function testCopyDir()
    {
//        $this->createMockFile();
        $p = $this->cli->workPath;
        if (!file_exists($p . '/_source')) {
            mkdir($p . '/_source');
        }
        if (!file_exists($p . '/_destination')) {
            mkdir($p . '/_destination');
        }
        file_put_contents($p . '/_source/mock.txt', 'another mock');
        $this->instance->copyDir('_source', '_destination');
        $this->assertDirectoryExists($p . '/_destination');
        $this->assertFileExists($p . '/_destination/mock.txt');
    }

    protected function setUp() : void
    {
        $this->cli = new Cli([], __DIR__ . '/mockDrop');
        $this->instance = new FileHelper($this->cli);
        if (!file_exists($this->cli->workPath)) {
            mkdir($this->cli->workPath);
        }
    }

    private function createMockFile()
    {
        $mockFile = $this->cli->workPath . '/mock.txt';
        file_put_contents($mockFile, 'mock file');

        return $mockFile;
    }
}
