<?php

namespace Neoan\Installer\Tests;

use Neoan\Installer\Credentials\Credentials;
use PHPUnit\Framework\TestCase;

class CredentialsTest extends TestCase
{
    private string $workPath;

    public function testChooseCredentials()
    {
        $cli = new MockCli(['neoan3', 'credentials'], $this->workPath);
        $c = new Credentials($cli);
        $cli->addInput('x');
        $cli->addInput('demo-credentials-testing');
        $cli->addInput('');
        $c->chooseCredentials(['some' => 'value']);
        $this->expectOutputRegex('/Choose credentials/');
    }

    public function testFlags()
    {
        /*// generate
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
        $h->createNew();*/
        $cli = new MockCli(['neoan3', 'credentials', '-n:cli-test-credentials'], $this->workPath);
        $cli->globalVars['credential-path'] = __DIR__;
        $cli->addInput('x');
        $c = new Credentials($cli);
        $c->chooseCredentials();
        $c->displayCredentials();
        $this->expectOutputRegex('/some:/');
    }

    public function testDisplayCredentials()
    {
        $cli = new MockCli(['neoan3', 'credentials'], $this->workPath);
        $c = new Credentials($cli);
        $c->currentCredentials = [
            'key' => 'value',
        ];
        $cli->addInput('x');
        $c->displayCredentials();
        $this->expectOutputRegex('/Listing values/');
    }

    protected function setUp() : void
    {
        $this->workPath = dirname(__DIR__) . '/playground';
    }
}
