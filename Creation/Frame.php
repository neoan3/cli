<?php


namespace Creation;


use Cli\Cli;
use Helper\FileHelper;
use Helper\TemplateHelper;
use Neoan3\Apps\Ops;

class Frame
{
    private Cli $cli;
    private string $folder;
    private TemplateHelper $template;
    private FileHelper $fileHelper;

    function __construct(Cli $cli)
    {
        $this->cli = $cli;
        $this->fileHelper = new FileHelper($cli);
        $this->template = new TemplateHelper($cli);

    }
    function init()
    {
        if (!isset($this->cli->arguments[2])) {
            $this->cli->printLn('Malformed command. Expected format:', 'red');
            $this->cli->printLn('neoan3 new frame <frameName>');
            return;
        }
        $opsMethod = $this->cli->versionHelper->appMainVersion < 3 ? 'toCamelCase' : 'toPascalCase';
        $this->folder = $this->cli->workPath . '/frame/' . Ops::$opsMethod($this->cli->arguments[2]);
        if($this->frameExists()){
            return;
        }
        $this->fileHelper->createDirectory($this->folder);
        $this->writeFrame();
    }
    function writeFrame()
    {
        $template = $this->template->readTemplate('frame.php');
        if(!$template){
            $template = $this->template->readPartial('frame');
        }
        $template = $this->template->substituteVariables($template);
        $this->template->writeFrame($template);
    }
    function frameExists()
    {
        if(file_exists($this->folder . '/' . Ops::toPascalCase($this->cli->arguments[2]) . '.php')){
            $this->cli->printLn('Frame already exists', 'red');
            return true;
        }
        return false;
    }
}
