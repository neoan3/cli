<?php

namespace Neoan\Installer\Tests;

use Neoan\Installer\Cli\Cli;
use PHPUnit\Framework\TestCase;
use Neoan\Installer\Set\Set;

class SetTest extends TestCase
{
    private string $workPath;

    static function tearDownAfterClass() : void
    {
        $path = dirname(__DIR__) . '/playground/default.php';
        if (file_exists($path)) {
            $c = file_get_contents($path);
            if (substr($c, 0, 3) === '123') {
                unlink($path);
            }
        }
        file_put_contents(dirname(__DIR__) . '/bin/_vars.json', '{}');

    }

    public function testErrors()
    {
        // test malformed
        $this->expectOutputRegex('/Malformed command/');
        $set = new Set(new Cli(['neoan3-cli', 'set'], $this->workPath));
        // test unwritable
        $this->expectOutputRegex('/Cannot locate default\.php/');
        $set = new Set(new Cli(['neoan3-cli', 'set', 'default_ctrl', 'judy'], $this->workPath . '/deep/'));
        // test unsupported
        $this->expectOutputRegex('/Unknown or unsupported variable/');
        $set = new Set(new Cli(['neoan3-cli', 'set', 'default_variable', 'some'], $this->workPath));
    }

    public function testWriteDefault()
    {
        $this->ensureDefault();
        new Set(new Cli(['neoan3-cli', 'set', 'default_ctrl', 'judy'], $this->workPath));
        new Set(new Cli(['neoan3-cli', 'set', 'default_404', 'error'], $this->workPath));
        $c = file_get_contents($this->workPath . '/default.php');
        $this->assertStringContainsString('"judy"', $c);
        $this->assertStringContainsString('"error"', $c);
    }

    public function testGlobal()
    {
        new Set(new Cli(['neoan3-cli', 'set', 'credential-path', '/some-path'], $this->workPath));
        $this->assertStringContainsString('/some-path', file_get_contents(dirname(__DIR__) . '/bin/_vars.json'));
    }

    protected function setUp() : void
    {
        $this->workPath = dirname(__DIR__) . '/playground';
    }

    private function ensureDefault()
    {
        if (!file_exists($this->workPath . '/default.php')) {
            $content = "123\ndefine('default_ctrl', 'untouched');\ndefine('default_404','404');";
            file_put_contents($this->workPath . '/default.php', $content);
        }
    }
}
