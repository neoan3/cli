<?php

namespace Neoan\Installer\Helper;

use Exception;
use Neoan\Installer\Cli\Cli;
use ReflectionClass;

class ReflectionHelper
{
    public ReflectionClass $class;

    public array $methods;

    private Cli $cli;

    public function __construct(Cli $cli)
    {
        $this->cli = $cli;

    }

    public function load($className) : bool
    {
        require_once $this->cli->workPath . '/vendor/autoload.php';
        try {
            $this->class = new ReflectionClass($className);
            $this->retrieveOwnMethods();
        } catch (Exception $e) {
            $this->cli->printLn('Unable to create ReflectionClass', 'red');

            return false;
        }

        return true;
    }

    private function retrieveOwnMethods()
    {

        foreach ($this->class->getMethods() as $method) {
            if ($method->class === $this->class->getName()) {
                $this->methods[] = $this->class->getMethod($method->name);

            }
        }
    }
}