<?php


namespace Helper;


use Cli\Cli;

class TemplateHelper
{
    private Cli $cli;
    function __construct(Cli $cli)
    {
        $this->cli = $cli;
    }

    public function generate($type) {
//        $template = $this->readTemplate($type);
    }

    public function readTemplate($type)
    {
        if(file_exists($this->cli->workPath . '/_template')) {
            switch ($type) {
                case 'route':

            }
        }
    }

}