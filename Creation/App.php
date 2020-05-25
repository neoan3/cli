<?php


namespace Creation;


use Cli\Cli;
use Helper\FileHelper;

class App extends Cli
{
    function init()
    {
        $helper = new FileHelper();
        // download zip
        $helper->download('https://neoan3.rocks/asset/neoan3-master.zip','app.zip');
        // unpack
        try{
            $helper->unZip('app.zip','');
        } catch (\Exception $e){
            $helper->printLn("Warning: unable to unpack neoan3-zip");
        }
        // copy
        $helper->copyDir('neoan3-master','');
        // remove folder
        $helper->deleteRecursively($this->workPath . '/neoan3-master/');
        // remove archive
        try{
            unlink($this->workPath . '/app.zip');
        } catch (\Exception $e){
            $this->printLn("Warning: unable to delete app.zip");
        }
        // rewrite .htaccess
        $this->htaccessRewrite();

    }
    function htaccessRewrite()
    {
        $htaccess = file_get_contents($this->workPath . '/.htaccess');
        $htaccess = preg_replace('/RewriteBase\s[a-z0-9\/]+$/im','RewriteBase /', $htaccess);
        file_put_contents($this->workPath . '/.htaccess_', $htaccess);
    }

}