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
    private string $folder;
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
        $this->folder = $this->cli->workPath . '/model/' . Ops::toCamelCase($this->cli->arguments[2]);
        if($this->modelExists()){
            return;
        }
        $this->fileHelper->createDirectory($this->folder);
        $this->writeModel();
    }
    function writeModel()
    {
        $template = $this->template->readTemplate('model.php');
        if(!$template){
            $template = file_get_contents(dirname(__DIR__) . '/Helper/partials/model.php');
        }
        $template = $this->template->substituteVariables($template);
        file_put_contents($this->folder . '/' . Ops::toPascalCase($this->cli->arguments[2]) . '.model.php', $template);
        file_put_contents($this->folder . '/' . 'migrate.json', "{}");

    }
    function modelExists()
    {
        if(file_exists($this->folder . '/' . Ops::toPascalCase($this->cli->arguments[2]) . '.model.php')){
            $this->cli->printLn('Model already exists', 'red');
            return true;
        }
        return false;
    }
}
