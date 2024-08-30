<?php

include '../site/bootstrap.php';

$app = new \Espo\Core\Application();
$app->setupSystemUser();

if (file_exists('../src/scripts/AfterInstall.php')) {
    include('../src/scripts/AfterInstall.php');
    $script = new AfterInstall($app->getContainer());
    $script->run();
}

if (file_exists('../src/scripts/AfterInstallDevelopment.php')) {
    include('../src/scripts/AfterInstallDevelopment.php');
    $script = new AfterInstallDevelopment($app->getContainer());
    $script->run();
}
