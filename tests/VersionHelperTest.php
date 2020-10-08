<?php


use Helper\VersionHelper;
use PHPUnit\Framework\TestCase;

require_once 'MockCli.php';
class VersionHelperTest extends TestCase
{

    public function testPrintCliVersion()
    {
        $this->expectOutputRegex('/Version:/');
        $i = new VersionHelper(new MockCli(['neoan3-cli','-v'],dirname(__DIR__) . '/playground'));
        $i->printCliVersion();
    }
}
