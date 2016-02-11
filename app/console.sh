#!/usr/bin/env php

<?php

require_once __DIR__.'/../vendor/autoload.php';

use Mantainance\Command\InitCommand;
use Mantainance\Command\UpCommand;
use Mantainance\Command\DownCommand;
use Symfony\Component\Console\Application;

$app = new Application('Mantainance', '0.0.1');  
$app->addCommands([new InitCommand(), new UpCommand(), new DownCommand()]);
$app->run();