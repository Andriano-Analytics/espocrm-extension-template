<?php

include '../site/bootstrap.php';

$app = new \Espo\Core\Application();
$app->setupSystemUser();

if (file_exists('../src/scripts/BeforeInstall.php')) {
    include('../src/scripts/BeforeInstall.php');
    $script = new BeforeInstall($app->getContainer());
    $script->run();
}

if (file_exists('../src/scripts/BeforeInstallDevelopment.php')) {
    include('../src/scripts/BeforeInstallDevelopment.php');
    $script = new BeforeInstallDevelopment($app->getContainer());
    $script->run();
}
