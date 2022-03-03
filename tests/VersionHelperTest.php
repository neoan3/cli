<?php

namespace Neoan\Installer\Tests;

use Neoan\Installer\Helper\VersionHelper;
use PHPUnit\Framework\TestCase;

class VersionHelperTest extends TestCase
{

    public function testPrintCliVersion()
    {
        $this->expectOutputRegex('/Version:/');
        $i = new VersionHelper(new MockCli(['neoan3-cli', '-v'], dirname(__DIR__) . '/playground'));
        $i->printCliVersion();
    }
}
