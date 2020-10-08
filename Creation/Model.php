<?php


namespace Creation;


use Cli\Cli;
use Helper\FileHelper;
use Helper\TemplateHelper;
use Neoan3\Apps\Ops;

class Model
{
    private Cli $cli;
    private FileHelper $fileHelper;
    private TemplateHelper $template;

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
            $this->cli->printLn('neoan3 new model <modelName>');
            return;
        }
        if($this->modelExists()){
            return;
        }
        $this->fileHelper->createDirectory($this->template->modelFolder);
        $this->writeModel();
    }
    function writeModel()
    {
        $template = $this->template->readTemplate('model.php');
        if(!$template){
            $template = $this->template->readPartial('model');
        }
        $template = $this->template->substituteVariables($template);
        $this->template->writeModel($template);
    }
    function modelExists()
    {
        if(!empty(glob($this->template->modelFolder . '/*Model.php'))){
            $this->cli->printLn('Model already exists', 'red');
            return true;
        }
        return false;
    }
}
