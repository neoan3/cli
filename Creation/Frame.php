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
        $this->folder = $this->cli->workPath . '/frame/' . Ops::toCamelCase($this->cli->arguments[2]);
    }
    function init()
    {
        if (!isset($this->cli->arguments[2])) {
            $this->cli->printLn('Malformed command. Expected format:', 'red');
            $this->cli->printLn('neoan3 new frame <frameName>');
            return;
        }
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
            $template = file_get_contents(dirname(__DIR__) . '/Helper/partials/frame.php');
        }
        $template = $this->template->substituteVariables($template);
        file_put_contents($this->folder . '/' . Ops::toPascalCase($this->cli->arguments[2]) . '.php', $template);
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