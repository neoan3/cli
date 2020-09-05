<?php


use Helper\CredentialHelper;
use PHPUnit\Framework\TestCase;
require_once 'MockCli.php';

class CredentialHelperTest extends TestCase
{
    public function testReadCredentials()
    {
        $h = new CredentialHelper(new MockCli(['neoan3','some','other'],dirname(__DIR__).'/playground'));
        $c = $h->readCredentials();

        $this->assertIsArray($c);
    }
    public function testCreateNew()
    {
        $mock = new MockCli(['neoan3','some','other'],dirname(__DIR__).'/playground');
        // name
        $mock->addInput('cli-test-credentials');
        // add property
        $mock->addInput('test-property');
        // add value
        $mock->addInput('test-value');
        // next
        $mock->addInput('default');
        // add property
        $mock->addInput('test-property2');
        // add value
        $mock->addInput('test-value2');
        // end
        $mock->addInput('n');
        // write
        $h = new CredentialHelper($mock);
        $h->createNew();
        $c = $h->readCredentials();
        $this->assertIsArray($c);
        $this->assertArrayHasKey('cli-test-credentials', $c);
        $this->assertArrayHasKey('test-property', $c['cli-test-credentials']);
        $this->assertEquals('test-value',$c['cli-test-credentials']['test-property']);
    }
}
