<?php


namespace Creation;


use Cli\Cli;
use Helper\FileHelper;

class App
{
    private Cli $cli;
    function __construct(Cli $cli)
    {
        $this->cli = $cli;
    }

    function init()
    {
        $helper = new FileHelper($this->cli);
        echo "downloading...\n";
        // download zip
        $helper->download('https://neoan3.rocks/asset/neoan3-master.zip','app.zip');
        // unpack
        try{
            $helper->unZip($this->cli->workPath . '/app.zip','');
        } catch (\Exception $e){
            $this->cli->printLn("Warning: unable to unpack neoan3-zip");
        }
        // copy
        $helper->copyDir('neoan3-master','');
        // remove folder
        $helper->deleteRecursively($this->cli->workPath . '/neoan3-master/');
        // remove archive
        try{
            unlink($this->cli->workPath . '/app.zip');
        } catch (\Exception $e){
            $this->cli->printLn("Warning: unable to delete app.zip");
        }
        // rewrite .htaccess
        $this->htaccessRewrite();
        echo "Dependencies...";
        $this->cli->io('composer update');

    }
    function htaccessRewrite()
    {
        $htaccess = file_get_contents($this->cli->workPath . '/.htaccess');
        $htaccess = preg_replace('/RewriteBase\s[a-z0-9\/]+$/im','RewriteBase /', $htaccess);
        file_put_contents($this->cli->workPath . '/.htaccess', $htaccess);
    }

}