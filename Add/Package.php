<?php


namespace Add;


use Cli\Cli;
use Helper\FileHelper;
use Neoan3\Apps\Ops;

class Package
{
    private Cli $cli;
    private FileHelper $fileHelper;
    public array $composer = [];
    public bool $workable = true;

    function __construct(Cli $cli)
    {
        $this->cli = $cli;
        $this->fileHelper = new FileHelper($cli);
        $this->run();
    }
    private function run()
    {
        if($this->needed()){
            $composerFile = $this->fileHelper->readFile($this->cli->workPath . '/composer.json');
            $this->composer = json_decode($composerFile, true);
            $this->write();
            $this->cli->printLn("Installation...");
            $this->cli->io('composer update --no-dev -d ' . $this->cli->workPath);
            $this->cli->printLn("Don't forget to run `neoan3 migrate models up`", "magenta");
        } else {
            $this->workable = false;
        }
    }
    function write()
    {
        // custom path
        $nameParts = explode('/', $this->cli->arguments[2]);
        $customPath = './' . $this->cli->arguments[1] . '/' . Ops::toPascalCase(end($nameParts));

        $this->composer['extra']['installer-paths'][$customPath][] = $this->cli->arguments[2];
        // repo?
        if(isset($this->cli->arguments[3])){
            $this->composer['repositories'][] = [
                'type'=> 'vcs',
                'url'=> $this->cli->arguments[3]
            ];
        }
        // require
        $this->composer['require'][$this->cli->arguments[2]] = '*';
        $this->fileHelper->writeFile(
            $this->cli->workPath . '/composer.json',
            json_encode($this->composer,JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
        );
    }
    /*
     * Issue with new github behavior master vs. main branch
    function extract()
    {
        if(preg_match('/^http(s)*:\/\/github/',$this->cli->arguments[2])){
            $parts = explode('/', $this->cli->arguments[2]);
            $name = $parts[-2] . '/' . $parts[-1];
            $readExternal = $this->fileHelper->readFile('https://raw.githubusercontent.com/'.$name.'/master/composer.json');
        }
    }*/
    function needed(): bool
    {
        // add [what] [name] ([url]) //e.g. add model user package
        if(!isset($this->cli->arguments[1])){
            $this->cli->printLn("missing argument <Type>");
            return false;
        }
        if(!isset($this->cli->arguments[2])){
            $this->cli->printLn("missing argument <Name>");
            return false;
        }
        return true;
    }
}