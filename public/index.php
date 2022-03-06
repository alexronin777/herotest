<?php

use modules\Fight\Application;
use modules\Fight\Port\Adapter\Controller\FightController;

define('BASE_DIR', dirname(dirname(__DIR__)));

try {
    require_once BASE_DIR . '/hero/vendor/autoload.php';

    require_once BASE_DIR . '/hero/src/config/config.php';

    if (isset($_GET['action']) && $_GET['action'] == 'fight') {
        $app = new Application($database);
        /** @var  FightController $controller */
        $controller = $app->getController(FightController::class);
        $controller->enqueueFight($_POST);
    }
} catch (Throwable $e) {
    print_r($e->getMessage());
}