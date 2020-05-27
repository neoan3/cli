<?php


namespace Helper;


use Cli\Cli;

class FileHelper
{
    private Cli $cli;
    function __construct(Cli $cli)
    {
        $this->cli = $cli;
    }

    public function download($source, $destination)
    {
        $file = file_get_contents($source);
        file_put_contents($this->cli->workPath . '/' . $destination, $file);
    }
    public function unZip($source, $destination)
    {
        $zip = new \ZipArchive();
        if ($zip->open($source) === TRUE) {
            $zip->extractTo($this->cli->workPath . '/' . $destination);
            $zip->close();
        } else {
            throw new \Exception('unable to process zip');
        }
    }
    public function copyDir($source, $destination)
    {
        $fileOrFolder = scandir($this->cli->workPath . '/'. $source);
        foreach ($fileOrFolder as $item){
            if($item !== '..' && $item !== '.'){
                rename($this->cli->workPath . '/'. $source . '/' . $item, $this->cli->workPath . '/' . $destination . '/' . $item);
            }
        }
    }
    public function deleteRecursively($target)
    {
        if(is_dir($target)){
            $files = glob( $target . '*', GLOB_MARK );
            foreach( $files as $file ){
                $this->deleteRecursively( $file );
            }
            rmdir( $target );
        } elseif(is_file($target)) {
            unlink( $target );
        }
    }

}