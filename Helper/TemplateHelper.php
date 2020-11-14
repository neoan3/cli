<?php


namespace Helper;


use Cli\Cli;
use Neoan3\Apps\Ops;

class TemplateHelper
{
    private Cli $cli;
    private FileHelper $fileHelper;
    public string $componentFolder;
    public string $modelFolder;
    function __construct(Cli $cli)
    {
        $this->fileHelper = new FileHelper($cli);
        $this->cli = $cli;

        $opsMethod = $this->cli->versionHelper->casing;
        if(isset($this->cli->arguments[2])){
            $this->componentFolder = $this->cli->workPath . '/component/' . Ops::$opsMethod($this->cli->arguments[2]) . '/';
            $this->modelFolder = $this->cli->workPath . '/model/' . Ops::$opsMethod($this->cli->arguments[2]) . '/';
        }

    }


    public function readTemplate($requestFile)
    {
        if($this->fileHelper->checkFile($this->cli->workPath . '/_template/' . $requestFile)) {
            return $this->fileHelper->readFile($this->cli->workPath . '/_template/' . $requestFile);
        }
        return false;
    }
    public function writeView($content){
        $this->fileHelper->writeFile($this->componentFolder . Ops::toCamelCase($this->cli->arguments[2]) . '.view.html', $content);
    }
    public function writeController($content)
    {
        if($this->cli->versionHelper->appMainVersion < 3){
            $this->fileHelper->writeFile($this->componentFolder . Ops::toPascalCase($this->cli->arguments[2]) . '.ctrl.php', $content);
        } else {
            $this->fileHelper->writeFile($this->componentFolder . Ops::toPascalCase($this->cli->arguments[2]) . 'Controller.php', $content);
        }
    }
    public function writeFrame($content)
    {
        $opsMethod = $this->cli->versionHelper->appMainVersion < 3 ? 'toCamelCase' : 'toPascalCase';
        $destination = $this->cli->workPath . '/frame/' . Ops::$opsMethod($this->cli->arguments[2]) . '/' . Ops::toPascalCase($this->cli->arguments[2]) . '.php';
        $this->fileHelper->writeFile($destination, $content);
    }
    public function writeModel($content)
    {
        if($this->cli->versionHelper->appMainVersion < 3){
            $this->fileHelper->writeFile($this->modelFolder . Ops::toPascalCase($this->cli->arguments[2]) . '.model.php', $content);
        } else {
            $this->fileHelper->writeFile($this->modelFolder . Ops::toPascalCase($this->cli->arguments[2]) . 'Model.php', $content);
        }
        $this->fileHelper->writeFile($this->modelFolder . 'migrate.json', "{}");
    }
    public function readPartial($name)
    {
        return $this->fileHelper->readFile(__DIR__ . '/partials/v' . $this->cli->versionHelper->appMainVersion."/$name.tmp");
    }
    public function substituteVariables($template, $additionalVariables= [])
    {
        if(!isset($additionalVariables['name'])){
            $additionalVariables['name'] = $this->cli->arguments[2];
        }
        $this->generateCases($additionalVariables);
        foreach ($additionalVariables as $key => $variable){
            $pattern = '/{{'. $key .'}}/';
            $template = preg_replace($pattern, $variable, $template);
        }
        return $template;
    }
    function generateCases(&$variableArray)
    {
        foreach ($variableArray as $key => $value){
            $variableArray[$key . '.lower'] = strtolower($value);
            $variableArray[$key . '.camel'] = Ops::toCamelCase($value);
            $variableArray[$key . '.kebab'] = Ops::toKebabCase($value);
            $variableArray[$key . '.pascal'] = Ops::toPascalCase($value);
        }
    }

}