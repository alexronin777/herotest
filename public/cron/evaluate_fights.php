<?php

use modules\Fight\Application;
use modules\Fight\Port\Adapter\Controller\EvaluateFightCommand;
use Symfony\Component\Console\Application as ConsoleApplication;

define('BASE_DIR', dirname(dirname(__DIR__)));
require_once BASE_DIR . '/vendor/autoload.php';
require_once BASE_DIR . '/src/config/config.php';

$consoleApp = new ConsoleApplication();
$fightApp = new Application($database);
$consoleApp->addCommands([
    $fightApp->getCommand(EvaluateFightCommand::class)
]);

$consoleApp->setAutoExit(false);
$consoleApp->run();