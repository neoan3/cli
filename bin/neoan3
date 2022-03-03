#!/usr/bin/env php
<?php

namespace Neoan\Installer\Cli;

if (file_exists(__DIR__ . '/../../../autoload.php')) {
    require __DIR__ . '/../../../autoload.php';
} else {
    require __DIR__ . '/../vendor/autoload.php';
}

global $argv;
$app = new Cli($argv, getcwd());

$app->run();
