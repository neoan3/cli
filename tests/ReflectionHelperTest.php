<?php

namespace Neoan\Installer\Tests;

use Neoan\Installer\Helper\ReflectionHelper;
use PHPUnit\Framework\TestCase;

class ReflectionHelperTest extends TestCase
{

    public function testLoadFail()
    {
        $r = new ReflectionHelper(new MockCli(['neoan3', '-v'], dirname(__DIR__)));
        $this->expectOutputRegex('/Unable to/');
        $false = $r->load('Another\Boring\LoveSong\Class');
        $this->assertIsBool($false);
        $this->assertSame(false, $false);
    }
}
