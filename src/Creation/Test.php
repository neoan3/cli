<?php

namespace Neoan\Installer\Creation;

use Neoan\Installer\Cli\Cli;
use Neoan\Installer\Helper\FileHelper;
use Neoan\Installer\Helper\ReflectionHelper;
use Neoan\Installer\Helper\TemplateHelper;
use Neoan3\Apps\Ops;

class Test
{
    private Cli $cli;

    private TemplateHelper $templateHelper;

    private array $methods = [];

    private string $folder;

    private FileHelper $fileHelper;

    private ReflectionHelper $reflection;

    function __construct($cli)
    {
        $this->cli = $cli;
        $this->templateHelper = new TemplateHelper($cli);
        $this->fileHelper = new FileHelper($cli);
        $this->reflection = new ReflectionHelper($cli);
    }

    function init()
    {
        if (!$this->validate()) {
            return;
        }

        $fileName = $this->folder . Ops::toPascalCase($this->cli->arguments[3]) . 'Test.php';
        if ($this->fileHelper->checkFile($fileName)) {
            $this->cli->printLn('Test already exists', 'red');

            return;
        }
        $this->getMethods();
        $vars = [
            'name'    => Ops::toPascalCase($this->cli->arguments[3]),
            'methods' => '',
        ];
        switch ($this->cli->arguments[2]) {
            case 'model':
                $template = $this->templateHelper->readPartial('modelTest');
                break;
            default:
                $template = $this->templateHelper->readPartial('test');
                foreach ($this->methods as $method) {
                    $passIn = ['method' => $method->name, 'paramString' => '', 'expected' => ''];
                    $params = $method->getParameters();
                    foreach ($params as $i => $param) {
                        if (!$param->getType()) {
                            $this->cli->printLn("Warning: Missing parameter typing in method $method->name", 'red');
                            $passIn['expected'] = '[]';
                        } else {
                            $passIn['expected'] = $this->getParameterMock($param->getType()->getName()) ?? '[]';
                        }

                        $passIn['paramString'] .= ($i > 0 ? ', ' : '') . $passIn['expected'];
                    }
                    switch (mb_substr($method->name, 0, 3)) {
                        case 'ini':
                            $subTemplate = $this->templateHelper->readPartial('test.init');
                            $vars['methods'] .= $this->templateHelper->substituteVariables($subTemplate, $passIn);
                            break;
                        case 'get':
                        case 'pos':
                        case 'put':
                        case 'pat':
                        case 'del':
                            $subTemplate = $this->templateHelper->readPartial('test.api');
                            $vars['methods'] .= $this->templateHelper->substituteVariables($subTemplate, $passIn);
                            break;
                        default:
                            break;
                    }

                }
                break;
        }

        $testFile = $this->templateHelper->substituteVariables($template, $vars);
        $this->fileHelper->writeFile($fileName, $testFile);

    }

    function getParameterMock($type)
    {
        switch ($type) {
            case 'string':
                return "'" . Ops::randomString(4) . "'";
            case 'array':
                return "['" . Ops::randomString(4) . "'=>'" . Ops::randomString(6) . "']";
            case 'bool':
                return 'true';
            default:
                return Ops::pin();
        }
    }

    function getMethods()
    {
        $className = 'Neoan3\\';
        if ($this->cli->versionHelper->appMainVersion < 3) {
            if ($this->cli->arguments[2] == 'component') {
                $className .= 'Components\\' . Ops::toPascalCase($this->cli->arguments[3]);
            } else {
                $className .= 'Model\\' . Ops::toPascalCase($this->cli->arguments[3]) . 'Model';
            }
        } else {
            if ($this->cli->arguments[2] == 'component') {
                $className .= 'Component\\' . Ops::toPascalCase($this->cli->arguments[3]) . '\\' . Ops::toPascalCase($this->cli->arguments[3]) . 'Controller';
            } else {
                $className .= 'Model\\' . Ops::toPascalCase($this->cli->arguments[3]) . '\\' . Ops::toPascalCase($this->cli->arguments[3]) . 'Model';
            }
        }
        if ($this->reflection->load($className)) {
            $this->methods = $this->reflection->methods;
        }

    }

    function validate()
    {
        if (!in_array($this->cli->arguments[2], ['component', 'model']) || !isset($this->cli->arguments[3])) {
            $this->cli->printLn('Malformed command. Expected format:', 'red');
            $this->cli->printLn('neoan3 new test <component | model> <component-/model-Name>');

            return false;
        }
        $opsMethod = $this->cli->versionHelper->appMainVersion < 3 ? 'toCamelCase' : 'toPascalCase';
        $this->folder = $this->cli->workPath . '/' . $this->cli->arguments[2] . '/' . Ops::$opsMethod($this->cli->arguments[3]) . '/';

        return true;
    }


}