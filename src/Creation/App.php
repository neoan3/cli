<?php

namespace Neoan\Installer\Creation;

use Exception;
use Neoan\Installer\Cli\Cli;
use Neoan\Installer\Helper\FileHelper;

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

        try {

            // determine version to download
            $version = $this->version != 'master' ? 'v' . $this->version : 'master';

            // download zip
            $helper->download("https://github.com/neoan3/neoan3/archive/$version.zip", 'app.zip');

        } catch (Exception $e) {

            $this->cli->printLn('Error:', 'red');
            $this->cli->printLn($e->getMessage(), 'red');
            $this->cli->printLn('This version does not exist or GitHub is unreachable', 'red');

            return;
        }

        try {

            // unpack
            $helper->unZip($this->cli->workPath . '/app.zip', '');

        } catch (Exception $e) {

            $this->cli->printLn("Warning: unable to unpack neoan3-zip");

            return;
        }

        try {

            // copy folder
            $helper->copyDir("neoan3-$this->version", '');

            // remove folder
            $helper->deleteRecursively($this->cli->workPath . "/neoan3-$this->version/");

            // rewrite .htaccess
            $this->htaccessRewrite();

            // write readme
            $this->writeReadme();
            $this->cli->printLn("Dependency installation...");
            $this->cli->io('composer install --no-dev -d ' . $this->cli->workPath);
            $this->cli->printLn("Skipped dev-dependencies like PHPUnit: run 'composer update' before testing!", 'magenta');

        } catch (Exception $e) {

            $this->cli->printLn("Warning: unable to process app.zip");

            return;
        }

        // remove archive
        try {

            unlink($this->cli->workPath . '/app.zip');

        } catch (Exception $e) {

            $this->cli->printLn("Warning: unable to delete app.zip");

            return;
        }
    }

    function htaccessRewrite()
    {
        // get current file content
        $htaccess = file_get_contents($this->cli->workPath . '/.htaccess');

        // make replacements
        $htaccess = preg_replace('/RewriteBase\s[a-z0-9\/]+$/im', 'RewriteBase /', $htaccess);

        // store new file content
        file_put_contents($this->cli->workPath . '/.htaccess', $htaccess);
    }

    function writeReadme()
    {
        // get file content
        $template = file_get_contents(dirname(__DIR__) . '/Helper/partials/README.md');

        // store new file
        file_put_contents($this->cli->workPath . '/readme.md', $template);
    }
}