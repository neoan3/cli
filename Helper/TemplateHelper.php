<?php


namespace Helper;


use Cli\Cli;
use Neoan3\Apps\Ops;

class TemplateHelper
{
    private Cli $cli;
    private ?bool $view = null;
    private FileHelper $fileHelper;
    function __construct(Cli $cli)
    {
        $this->fileHelper = new FileHelper($cli);
        $this->cli = $cli;
    }

    public function generate($type) {
        $this->parseFlags();
        $name = $this->cli->arguments[2];
        switch ($type){
            case 'route':
                if($this->view === null){
                    $this->cli->printLn("Generate a view? [Y/n]",'green');
                    $this->cli->waitForInput(function($input){
                        $this->view = $input !== 'n';
                    });
                }
                if($this->view){
                    // write view
                    $template = $this->substituteVariables($this->readTemplate('view.html'));
                    if($template == ''){
                        $template = "<h1>$name</h1>";
                    }
                    $destination = $this->cli->workPath . '/component/' .
                        Ops::toCamelCase($name);

                    $this->fileHelper->createDirectory($destination);
                    file_put_contents($destination . '/' . Ops::toCamelCase($name) . '.view.html', $template);
                }
                $template = $this->substituteVariables($this->readTemplate('route.php'));
                file_put_contents($destination . '/', $template);
                break;
        }

    }

    public function readTemplate($requestFile)
    {
        if(file_exists($this->cli->workPath . '/_template/' . $requestFile)) {
            return file_get_contents($this->cli->workPath . '/_template/' . $requestFile);
        }
        return false;
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

    function parseFlags()
    {
        $this->view = isset($this->cli->flags['v']) ? $this->cli->flags['v'] !== 'no' : null;
    }
}