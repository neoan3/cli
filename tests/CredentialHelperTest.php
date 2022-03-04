<?php

namespace Neoan\Installer\Tests;

use Neoan\Installer\Helper\CredentialHelper;
use PHPUnit\Framework\TestCase;

class CredentialHelperTest extends TestCase
{
    public function testReadCredentials()
    {
        $h = new CredentialHelper(new MockCli(['neoan3', 'some', 'other'], dirname(__DIR__) . '/playground'));
        $c = $h->readCredentials();

        $this->assertIsArray($c);
    }

    public function testCreateNew()
    {
        $mock = new MockCli(['neoan3', 'some', 'other'], dirname(__DIR__) . '/playground');
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
        $credentials = $h->credentials;
        $this->assertIsArray($credentials);
        $this->assertArrayHasKey('cli-test-credentials', $credentials);
        $this->assertArrayHasKey('test-property', $credentials['cli-test-credentials']);
        $this->assertEquals('test-value', $credentials['cli-test-credentials']['test-property']);
    }
}
