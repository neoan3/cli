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
        $name = $this->cli->arguments[2];
        $variables = [
            'name' => Ops::toPascalCase($name),
            'name.lower' => strtolower($name),
            'name.camel' => Ops::toCamelCase($name),
            'name.kebab' => Ops::toKebabCase($name),
            'name.pascal' => Ops::toPascalCase($name)
        ];
        $variables = array_merge($variables, $additionalVariables);
        foreach ($variables as $key => $variable){
            $pattern = '/{{'. $key .'}}/';
            $template = preg_replace($pattern, $variable, $template);
        }
        return $template;
    }
    function parseFlags()
    {

        foreach ($this->cli->flags as $flag)
        {
            preg_match('/(.)[a-z]*(:)*([a-z]*)/', $flag, $matches);
            if(isset($matches[1])){
                if($matches[1] == 'v'){
                    $this->view = $matches[3] !== 'no';
                }
            }
        }
    }
}