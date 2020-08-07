<?php


namespace Creation;


use Cli\Cli;
use Helper\TemplateHelper;
use Neoan3\Apps\Ops;

class Test
{
    private Cli $cli;
    private TemplateHelper $templateHelper;
    private array $methods;
    private string $folder;

    function __construct($cli)
    {
        $this->cli = $cli;
        $this->templateHelper = new TemplateHelper($cli);

    }

    function init()
    {
        if (!$this->validate()) {
            return true;
        }
        $this->getMethods();
        $fileName = $this->folder . '/' . Ops::toPascalCase($this->cli->arguments[3]) . 'Test.php';
        if(file_exists($fileName)){
            $this->cli->printLn('Test already exists', 'red');
            return true;
        }

        $partials = dirname(__DIR__) . '/Helper/partials/';
        $template = file_get_contents($partials . 'test.php');
        $vars = [
            'name' => $this->cli->arguments[3],
            'methods' => ''
        ];
        foreach ($this->methods as $method){
            $subTemplate = file_get_contents($partials . ($method == 'init' ? 'test.init.php' : 'test.api.php'));
            $vars['methods'] .= $this->templateHelper->substituteVariables($subTemplate, ['method'=>$method]);
        }
        $testFile = $this->templateHelper->substituteVariables($template, $vars);
        file_put_contents($fileName, $testFile);

    }
    function getMethods()
    {
        $namespace = $this->cli->arguments[2] == 'component' ? 'Components' : 'Model';
        $file = file_get_contents(
            $this->folder . '/' . Ops::toPascalCase($this->cli->arguments[3])  .
            ($this->cli->arguments[2] == 'component' ? '.ctrl.php' : '.model.php')
        );
        preg_match_all('/function ([a-z]+'.Ops::toPascalCase($this->cli->arguments[3]).'|init)/i', $file, $matches);
        $this->methods = $matches[1];
    }

    function validate()
    {
        if (!in_array($this->cli->arguments[2], ['component', 'model']) || !isset($this->cli->arguments[3])) {
            $this->cli->printLn('Malformed command. Expected format:', 'red');
            $this->cli->printLn('neoan3 new test <component | model> <component-/model-Name>');
            return false;
        }
        $this->folder = $this->cli->workPath . '/'.$this->cli->arguments[2].'/' . $this->cli->arguments[3];
        return true;
    }


}