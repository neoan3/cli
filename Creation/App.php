<?php


namespace Creation;


use Cli\Cli;
use Helper\FileHelper;

class App
{
    private Cli $cli;
    private string $version;
    function __construct(Cli $cli)
    {
        $this->cli = $cli;
        $this->version = $this->cli->flags['v'] ?? 'master';
    }

    function init()
    {
        $helper = new FileHelper($this->cli);
        echo "downloading...\n";
        // download zip
        try{
            $version = $this->version != 'master' ? 'v' . $this->version : 'master';
            $helper->download("https://github.com/neoan3/neoan3/archive/$version.zip",'app.zip');
        } catch (\Exception $e) {
            $this->cli->printLn('Error:', 'red');
            $this->cli->printLn($e->getMessage(), 'red');
            $this->cli->printLn('This version does not exist or GitHub is unreachable', 'red');
            return;
        }

        // unpack
        try{
            $helper->unZip($this->cli->workPath . '/app.zip','');
        } catch (\Exception $e){
            $this->cli->printLn("Warning: unable to unpack neoan3-zip");
            return;
        }
        // copy
        try{
            $helper->copyDir("neoan3-$this->version",'');
            // remove folder
            $helper->deleteRecursively($this->cli->workPath . "/neoan3-$this->version/");
            // rewrite .htaccess
            $this->htaccessRewrite();
            // write readme
            $this->writeReadme();
            $this->cli->printLn("Dependency installation...");
            $this->cli->io('composer install --no-dev -d ' . $this->cli->workPath);
            $this->cli->printLn("Skipped dev-dependencies like PHPUnit: run 'composer update' before testing!",'magenta');
        } catch (\Exception $e){
            $this->cli->printLn("Warning: unable to process app.zip");
            return;
        }

        // remove archive
        try{
            unlink($this->cli->workPath . '/app.zip');
        } catch (\Exception $e){
            $this->cli->printLn("Warning: unable to delete app.zip");
            return;
        }

    }
    function htaccessRewrite()
    {
        $htaccess = file_get_contents($this->cli->workPath . '/.htaccess');
        $htaccess = preg_replace('/RewriteBase\s[a-z0-9\/]+$/im','RewriteBase /', $htaccess);
        file_put_contents($this->cli->workPath . '/.htaccess', $htaccess);
    }
    function writeReadme()
    {
        $template = file_get_contents(dirname(__DIR__) . '/Helper/partials/README.md');
        file_put_contents($this->cli->workPath . '/readme.md', $template);
    }

}