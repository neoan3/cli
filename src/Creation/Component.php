<?php

namespace Neoan\Installer\Creation;

use Neoan\Installer\Cli\Cli;
use Neoan\Installer\Helper\FileHelper;
use Neoan\Installer\Helper\TemplateHelper;
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
    }

    function init()
    {
        $this->parseFlags();

        if (!isset($this->cli->arguments[2])) {

            $this->cli->printLn('Malformed command. Expected format:', 'red');
            $this->cli->printLn('neoan3 new component <componentName>');

            return;
        }

        $folderName = $this->cli->versionHelper->appMainVersion < 3
            ? Ops::toCamelCase($this->cli->arguments[2])
            : Ops::toPascalCase($this->cli->arguments[2]);

        $this->folder = $this->cli->workPath . '/component/' . $folderName;

        if ($this->controllerExists()) {

            $this->cli->printLn('Component already exists', 'red');

            return;
        }

        if ($this->type && in_array($this->type, $this->componentTypes)) {

            $this->generateFiles();

            return;
        }

        $this->askForType();
    }

    function askForType()
    {
        $this->cli->printLn('What type of component?', 'green');
        $this->cli->printLn('Route component  [0] (default)', 'magenta');
        $this->cli->printLn('API component    [1]', 'magenta');
        $this->cli->printLn('Custom component [2]', 'magenta');

        $this->cli->waitForSingleInput(function ($userInput) {
            $this->type = $this->componentTypes[$userInput] ?? $this->componentTypes[0];
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
            if ($input === '') {
                $input = 0;
            }
            $this->frame = $frames[(int) $input];
        });
    }

    function writeCustom()
    {
        $template = $this->template->readTemplate('ce.js');

        if (!$template) {
            $template .= '(function(){ console.log("{{name}}: no custom element template found") })();';
        }

        $template = $this->template->substituteVariables($template);

        file_put_contents($this->folder . '/' . Ops::toCamelCase($this->cli->arguments[2]) . '.ce.js', $template);

        $phpTemplate = $this->template->readTemplate('ce.php');

        if ($phpTemplate) {
            $phpTemplate = $this->template->substituteVariables($phpTemplate);
            file_put_contents($this->folder . '/' . Ops::toPascalCase($this->cli->arguments[2]) . '.ce.php', $phpTemplate);
        }

        $viewTemplate = $this->template->readTemplate('ce.html');

        if ($viewTemplate) {
            $viewTemplate = $this->template->substituteVariables($phpTemplate);
            file_put_contents($this->folder . '/' . Ops::toCamelCase($this->cli->arguments[2]) . '.ce.html', $viewTemplate);
        }
    }

    function writeApi()
    {
        if (!$this->frame) {
            $this->askForFrame();
        }

        $template = $this->template->readTemplate('api.php');

        if (!$template) {
            $template = $this->template->readPartial('api');
        }

        $template = $this->template->substituteVariables($template, [
            'frame'        => $this->frame,
            'frame.pascal' => Ops::toPascalCase($this->frame),
        ]);

        $this->template->writeController($template);
    }

    function writeCtrl()
    {
        $template = $this->template->readTemplate('route.php');

        if (!$template) {

            if (!$this->frame) {
                $this->cli->printLn("Use a frame? [Y/n]", 'green');
                $this->cli->waitForSingleInput(function ($input) {
                    if ($input !== 'n' && $input !== 'no') {
                        $this->askForFrame();
                    }
                });
            }

            $routePartial = $this->template->readPartial('route');

            $extends = 'Neoan3\Core\Unicore';
            $extended = 'Unicore';

            $this->frame = !$this->frame
                ? ''
                : '\'' . Ops::toPascalCase($this->frame) . '\'';

            $template = $this->template->substituteVariables($routePartial, [
                'extends'  => $extends,
                'extended' => $extended,
                'hook'     => $this->view ? '->hook(\'main\', \'' . Ops::toCamelCase($this->cli->arguments[2]) . '\')' : '',
            ]);
        }

        $template = $this->template->substituteVariables($template, ['frame' => $this->frame]);

        $this->template->writeController($template);
    }

    function writeView()
    {
        $template = $this->template->readTemplate('view.html');

        if (!$template) {
            // @todo check should this really have `substituteVariables`? Or would `$template = $this->template->readPartial('view');` be correct?
            $template = $this->template->substituteVariables($this->template->readPartial('view'));
        }

        $template = $this->template->substituteVariables($template);

        $this->template->writeView($template);
    }


    function parseFlags()
    {
        $this->type = $this->cli->flags['t'] ?? null;

        $this->frame = isset($this->cli->flags['f'])
            ? Ops::toPascalCase($this->cli->flags['f'])
            : null;

        $this->view = isset($this->cli->flags['v'])
            ? $this->cli->flags['v'] !== 'no'
            : null;
    }

    function controllerExists()
    {
        return !empty(glob($this->folder . '/*.php'));
    }
}
