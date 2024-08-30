<?php

include '../site/bootstrap.php';

$app = new \Espo\Core\Application();
$app->setupSystemUser();

if (file_exists('../src/scripts/BeforeUninstall.php')) {
    include('../src/scripts/BeforeUninstall.php');
    $script = new BeforeUninstall($app->getContainer());
    $script->run();
}

if (file_exists('../src/scripts/BeforeUninstallDevelopment.php')) {
    include('../src/scripts/BeforeUninstallDevelopment.php');
    $script = new BeforeUninstallDevelopment($app->getContainer());
    $script->run();
}
