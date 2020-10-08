<?php


use Helper\ReflectionHelper;
use PHPUnit\Framework\TestCase;

require_once 'MockCli.php';

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
