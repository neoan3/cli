<?php


namespace Creation;


use Cli\Cli;
use Helper\FileHelper;
use Helper\TemplateHelper;
use Neoan3\Apps\Ops;

class Component
{
    private Cli $cli;
    private ?string $type = null;
    private ?string $view = null;
    private string $folder;
    private ?string $frame = null;
    private array $componentTypes = ['route', 'api', 'custom'];
    private TemplateHelper $template;
    private FileHelper $fileHelper;

    function __construct(Cli $cli)
    {
        $this->cli = $cli;
        $this->fileHelper = new FileHelper($cli);
        $this->template = new TemplateHelper($cli);
        $this->folder = $this->cli->workPath . '/component/' . Ops::toCamelCase($this->cli->arguments[2]);
    }

    function init()
    {
        $this->parseFlags();
        if (!isset($this->cli->arguments[2])) {
            $this->cli->printLn('Malformed command. Expected format:', 'red');
            $this->cli->printLn('neoan3 new component <componentName>');
        } else {
            if($this->controllerExists()){
                return;
            }

            if ($this->type && in_array($this->type, $this->componentTypes)) {
                $this->generateFiles();
            } else {
                $this->cli->printLn('What type of component?', 'green');
                $this->cli->printLn('Route component  [0] (default)', 'magenta');
                $this->cli->printLn('API component    [1]', 'magenta');
                $this->cli->printLn('Custom component [2]', 'magenta');
                $this->askForType();
            }
        }
    }

    function askForType()
    {
        $this->cli->waitForSingleInput(function ($userInput) {
            if (isset($this->componentTypes[$userInput])) {
                $this->type = $this->componentTypes[$userInput];
            } else {
                $this->type = $this->componentTypes[0];
            }
            $this->generateFiles();
        });
    }

    function askForView()
    {
        $this->cli->printLn("Generate a view? [Y/n]", 'green');
        $this->cli->waitForSingleInput(function ($input) {
            $this->view = $input !== 'n';
        });
    }

    function generateFiles()
    {
        $this->fileHelper->createDirectory($this->folder);
        switch ($this->type) {
            case 'route':
                if ($this->view === null) {
                    $this->askForView();
                }
                if ($this->view) {
                    $this->writeView();
                }
                $this->writeCtrl();
                break;
            case 'api':
                $this->writeApi();
                break;
            case 'custom':
                $this->writeCustom();
                break;
        }

    }

    function askForFrame()
    {
        $this->cli->printLn('Which frame do you want to use?', 'green');

        $path = $this->cli->workPath . '/frame';
        $frames = [];
        $frameFolders = glob($path . "/*", GLOB_ONLYDIR);
        foreach ($frameFolders as $i => $frame) {
            $frames[$i] = substr($frame, strlen($path) + 1);
            $this->cli->printLn($frames[$i] . "  [$i]" . ($i < 1 ? ' (default)' : ''), 'magenta');
        }
        $this->cli->waitForSingleInput(function ($input) use ($frames) {
            if ($input !== '') {
                $input = 0;
            }
            $this->frame = $frames[(int)$input];
        });
    }
    function writeCustom()
    {
        $template = $this->template->readTemplate('ce.js');
        if(!$template){
            $template .= '(function(){ console.log("{{name}}: no custom element template found") })();';
        }
        $template = $this->template->substituteVariables($template);
        file_put_contents($this->folder . '/' . Ops::toCamelCase($this->cli->arguments[2]) . '.ce.js', $template);
        $phpTemplate = $this->template->readTemplate('ce.php');
        if($phpTemplate){
            $phpTemplate = $this->template->substituteVariables($phpTemplate);
            file_put_contents($this->folder . '/' . Ops::toPascalCase($this->cli->arguments[2]) . '.ce.php', $phpTemplate);
        }
        $viewTemplate = $this->template->readTemplate('ce.html');
        if($viewTemplate){
            $viewTemplate = $this->template->substituteVariables($phpTemplate);
            file_put_contents($this->folder . '/' . Ops::toPascalCase($this->cli->arguments[2]) . '.ce.html', $viewTemplate);
        }
    }
    function writeApi()
    {
        $this->askForFrame();
        $template = $this->template->readTemplate('api.php');
        if (!$template) {
            $template = file_get_contents(dirname(__DIR__) . '/Helper/partials/api.php');
        }
        $template = $this->template->substituteVariables($template, [
            'frame' => $this->frame,
            'frame.pascal' => Ops::toPascalCase($this->frame),
        ]);
        file_put_contents($this->folder . '/' . Ops::toPascalCase($this->cli->arguments[2]) . '.ctrl.php', $template);
    }

    function writeCtrl()
    {
        $template = $this->template->readTemplate('route.php');
        if (!$template) {
            $this->cli->printLn("Use a frame? [Y/n]", 'green');
            $this->cli->waitForSingleInput(function ($input) {
                if ($input !== 'n' && $input !== 'no') {
                    $this->askForFrame();
                }
            });
            $routePartial = file_get_contents(dirname(__DIR__) . '/Helper/partials/route.php');
            $extends = 'Neoan3\Core\Unicore';
            $extended = 'Unicore';
            if (!$this->frame) {
                $this->frame = '';
            } else {
                $this->frame = '\'' . Ops::toPascalCase($this->frame) . '\'';
            }
            $template = $this->template->substituteVariables($routePartial, [
                'extends' => $extends,
                'extended' => $extended,
                'hook' => $this->view ? '->hook(\'main\', \'' . Ops::toCamelCase($this->cli->arguments[2]) . '\')' : ''
            ]);
        }
        $template = $this->template->substituteVariables($template, ['frame' => $this->frame]);
        file_put_contents($this->folder . '/' . Ops::toPascalCase($this->cli->arguments[2]) . '.ctrl.php', $template);
    }

    function writeView()
    {
        $template = $this->template->readTemplate('view.html');
        if (!$template) {
            $template = "<h1>{$this->cli->arguments[2]}</h1>";
        }
        file_put_contents($this->folder . '/' . Ops::toCamelCase($this->cli->arguments[2]) . '.view.html', $template);
    }


    function parseFlags()
    {
        foreach ($this->cli->flags as $flag) {
            preg_match('/(.)[a-z]*(:)*([a-z]*)/', $flag, $matches);
            if (isset($matches[1])) {
                if ($matches[1] == 't') {
                    $this->type = $matches[3];
                }
                if ($matches[1] == 'v') {
                    $this->view = $matches[3] !== 'no';
                }
            }
        }
    }
    function controllerExists()
    {
        if(file_exists($this->folder . '/' . Ops::toPascalCase($this->cli->arguments[2]) . '.ctrl.php')){
            $this->cli->printLn('Component already exists', 'red');
            return true;
        }
        return false;
    }
}