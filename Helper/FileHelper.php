<?php


namespace Helper;


class FileHelper extends \Cli\Cli
{
    public function download($source, $destination)
    {
        $file = file_get_contents($source);
        file_put_contents($this->workPath . '/' . $destination, $file);
    }
    public function unZip($source, $destination)
    {
        $zip = new \ZipArchive();
        if ($zip->open($source) === TRUE) {
            $zip->extractTo($this->workPath . '/' . $destination);
            $zip->close();
        } else {
            throw new \Exception('unable to process zip');
        }
    }
    public function copyDir($source, $destination)
    {
        $fileOrFolder = scandir($this->workPath . '/'. $source);
        foreach ($fileOrFolder as $item){
            if($item !== '..' && $item !== '.'){
                rename($this->workPath . '/'. $source . '/' . $item, $this->workPath . '/' . $destination . '/' . $item);
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