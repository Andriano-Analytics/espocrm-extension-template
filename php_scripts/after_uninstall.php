<?php

include '../site/bootstrap.php';

$app = new \Espo\Core\Application();
$app->setupSystemUser();

if (file_exists('../src/scripts/AfterUninstall.php')) {
    include('../src/scripts/AfterUninstall.php');
    $script = new AfterUninstall($app->getContainer());
    $script->run();
}

if (file_exists('../src/scripts/AfterUninstallDevelopment.php')) {
    include('../src/scripts/AfterUninstallDevelopment.php');
    $script = new AfterUninstallDevelopment($app->getContainer());
    $script->run();
}
