<?php


use Helper\CredentialHelper;
use PHPUnit\Framework\TestCase;
require_once 'MockCli.php';

class CredentialHelperTest extends TestCase
{
    public function testReadCredentials()
    {
        $h = new CredentialHelper(new MockCli(['neoan3','some','other'],dirname(__DIR__).'/playground'));
        $h->readCredentials();
        $this->assertIsArray($h->credentials);
    }
}
