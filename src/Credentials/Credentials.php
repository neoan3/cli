<?php

namespace Neoan\Installer\Credentials;

use Neoan\Installer\Cli\Cli;
use Neoan\Installer\Helper\CredentialHelper;

class Credentials
{
    public array $currentCredentials;

    private Cli $cli;

    private CredentialHelper $credentialHelper;

    private array $credentialArray;

    function __construct(Cli $cli)
    {
        $this->cli = $cli;
        $this->credentialHelper = new CredentialHelper($cli);
        $this->credentialArray = $this->credentialHelper->readCredentials();
    }

    function displayCredentials()
    {
        $this->cli->printLn("Listing values:", 'magenta');
        $this->cli->printLn("");
        foreach ($this->currentCredentials as $key => $value) {
            $this->cli->printLn("$key: $value", 'magenta');
        }
        $this->cli->printLn("");
        $this->cli->printLn("[e] edit");
        $this->cli->printLn("[x] close");
        $this->cli->waitForSingleInput(function ($input) {
            if ($input === 'e') {
                $this->credentialHelper->addCredentialkey();
            } else {
                return true;
            }

            return false;
        });
        $this->credentialHelper->saveCredentials();
    }

    function flagIsSet() : bool
    {
        if (isset($this->cli->flags['c'])) {
            $this->credentialHelper->currentCredentialName = array_keys($this->credentialArray)[$this->fromAlphaNumeric($this->cli->flags['c'])];
            $this->currentCredentials = $this->credentialHelper->credentials[$this->credentialHelper->currentCredentialName];

            return true;
        }
        if (isset($this->cli->flags['n'])) {
            $this->credentialHelper->currentCredentialName = $this->cli->flags['n'];
            $this->currentCredentials = $this->credentialHelper->credentials[$this->credentialHelper->currentCredentialName];

            return true;
        }

        return false;
    }

    function chooseCredentials(array $format = []) : void
    {
        if (!$this->flagIsSet()) {
            $i = 0;
            $this->cli->printLn('Choose credentials', 'green');
            foreach ($this->credentialArray as $key => $credential) {
                $this->cli->printLn('[' . $this->toAlphaNumeric($i) . '] ' . $key, 'green');
                $i++;
            }
            $this->cli->printLn('[x] create new credentials', 'yellow');

            $this->cli->waitForSingleInput(function ($input) use ($format) {
                if ($input === 'x' || $input === '') {
                    $this->cli->printLn('');
                    $this->credentialHelper->createNew($format);
                } else {
                    $this->credentialHelper->currentCredentialName = array_keys($this->credentialArray)[$this->fromAlphaNumeric($input)];
                }
                $this->currentCredentials = $this->credentialHelper->credentials[$this->credentialHelper->currentCredentialName];
            });
        }

    }

    function fromAlphaNumeric($input)
    {
        if (is_numeric($input)) {
            return $input;
        } else {
            return strpos($this->chars(), strtolower($input)) + 10;
        }
    }

    function toAlphaNumeric(int $i)
    {
        return $i < 10 ? $i : substr($this->chars(), $i - 10, 1);
    }

    private function chars()
    {
        return 'abcdefghijklmnopqrstuvwyz';
    }
}