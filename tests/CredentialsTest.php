<?php


use Credentials\Credentials;
use PHPUnit\Framework\TestCase;
require_once 'MockCli.php';
class CredentialsTest extends TestCase
{
    private string $workPath;
    protected function setUp(): void
    {
        $this->workPath = dirname(__DIR__).'/playground';
    }

    public function testChooseCredentials()
    {
        $cli = new MockCli(['neoan3', 'credentials'], $this->workPath);
        $c = new Credentials($cli);
        $cli->addInput('x');
        $cli->addInput('demo-credentials-testing');
        $cli->addInput('');
        $c->chooseCredentials(['some'=>'value']);
        $this->expectOutputRegex('/Choose credentials/');
    }

    public function testDisplayCredentials()
    {
        $cli = new MockCli(['neoan3', 'credentials'], $this->workPath);
        $c = new Credentials($cli);
        $c->currentCredentials = [
            'key' => 'value'
        ];
        $cli->addInput('x');
        $c->displayCredentials();
        $this->expectOutputRegex('/Listing values/');
    }
}
