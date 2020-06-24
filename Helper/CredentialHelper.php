<?php


namespace Helper;


use Cli\Cli;

class CredentialHelper
{
    private Cli $cli;
    public array $credentials;
    public string $currentCredentialName = '';
    function __construct(Cli $cli)
    {
        $this->cli = $cli;
        if(file_exists($this->cli->workPath . '/_neoan/base/functions.php')){
            require_once $this->cli->workPath . '/_neoan/base/functions.php';
        }
    }
    function readCredentials()
    {
        try{
            $this->credentials = getCredentials();
            return $this->credentials;
        } catch (\Exception $e){
            return [];
        }
    }
    function createNew($targetStructure=[])
    {
        $this->cli->printLn('How do you want to call these credentials?');
        $this->cli->waitForInput(function($input) {
            $this->credentials[$input] = [];
            $this->currentCredentialName = $input;
        });
        if(!empty($targetStructure)){
            foreach($targetStructure as $key => $value){
                $this->addCredentialValue($key,$value);
            }
        } else {
            $this->addCredentialKey();
        }
        $this->saveCredentials();

    }
    function saveCredentials()
    {
        $path = DIRECTORY_SEPARATOR .'credentials'.DIRECTORY_SEPARATOR.'credentials.json';
        file_put_contents($path, json_encode($this->credentials));
    }
    function addCredentialKey()
    {
        $this->cli->printLn('What is the name of this property?');
        $this->cli->waitForInput(function($input){
            $this->credentials[$this->currentCredentialName] = $input;
            $this->addCredentialValue($input);
            $this->cli->printLn('Add another property? [Y/n]', 'green');
            $this->cli->waitForSingleInput(function ($input){
                if($input == 'default'){
                    $this->addCredentialKey();
                }
            });
        });

    }
    function addCredentialValue($key, $value = false)
    {
        $this->cli->printLn($key . ($value ? ' ['.$value.']' : '').':');
        $hidden = preg_match('/(api|key|password)/', $key) == 1;
        $this->cli->waitForInput(function($input) use ($key,$value){
            $input = $input != '' ? $input : $value;
            $input = ($input === 'false' || $input === 'true') ? (bool) $input : $input;
            $this->credentials[$this->currentCredentialName][$key] = $input;
        }, $hidden);
    }
}