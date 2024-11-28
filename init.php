<?php

fwrite(\STDOUT, "Enter an extension name:\n");
$fh = fopen('php://stdin', 'r');
$name = trim(fgets($fh));
fclose($fh);

$nameLabel = $name;

$name = ucfirst($name);

$name = str_replace(' ', '', ucwords(preg_replace('/^a-z0-9]+/', ' ', $name)));
$nameHyphen = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $name));

fwrite(\STDOUT, "Enter a description text:\n");
$fh = fopen('php://stdin', 'r');
$description = trim(fgets($fh));
fclose($fh);

if (substr($description, -1) !== '.') $description .= '.';

fwrite(\STDOUT, "Enter an author name:\n");
$fh = fopen('php://stdin', 'r');
$author = trim(fgets($fh));
fclose($fh);

//All front-end code is forced to be written in es6
$bundled = "true";
$jsTranspiled = "true";

$replacePlaceholders = function (string $file) use ($name, $nameHyphen, $nameLabel, $description, $author, $bundled, $jsTranspiled)
{
    $content = file_get_contents($file);

    $content = str_replace('{@name}', $name, $content);
    $content = str_replace('{@nameHyphen}', $nameHyphen, $content);
    $content = str_replace('{@nameLabel}', $nameLabel, $content);
    $content = str_replace('{@description}', $description, $content);
    $content = str_replace('{@author}', $author, $content);
    $content = str_replace('{@bundled}', $bundled, $content);
    $content = str_replace('{@jsTranspiled}', $jsTranspiled, $content);

    file_put_contents($file, $content);
};

$replacePlaceholders('package.json');
$replacePlaceholders('extension.json');
$replacePlaceholders('jsconfig.json');
$replacePlaceholders('config-default.json');
$replacePlaceholders('README.md');
$replacePlaceholders('src/scripts/AfterInstall.php');
$replacePlaceholders('src/scripts/AfterInstallDevelopment.php');
$replacePlaceholders('src/scripts/AfterUninstall.php');
$replacePlaceholders('src/scripts/AfterUninstallDevelopment.php');
$replacePlaceholders('src/scripts/BeforeInstall.php');
$replacePlaceholders('src/scripts/BeforeInstallDevelopment.php');
$replacePlaceholders('src/scripts/BeforeUninstall.php');
$replacePlaceholders('src/scripts/BeforeUninstallDevelopment.php');
$replacePlaceholders('src/files/custom/Espo/Modules/MyModuleName/Classes/Constants.php');
$replacePlaceholders('src/files/custom/Espo/Modules/MyModuleName/Classes/ConstantsDevelopment.php');
$replacePlaceholders('src/files/custom/Espo/Modules/MyModuleName/Resources/autoload.json');
$replacePlaceholders('src/files/custom/Espo/Modules/MyModuleName/Resources/module.json');
$replacePlaceholders('src/files/custom/Espo/Modules/MyModuleName/Resources/i18n/en_US/Global.json');
$replacePlaceholders('src/files/custom/Espo/Modules/MyModuleName/Resources/metadata/app/client.json');
$replacePlaceholders('src/files/custom/Espo/Modules/MyModuleName/Resources/metadata/app/adminPanel.json');
$replacePlaceholders('src/files/client/custom/modules/my-module-name/src/views/admin/my-settings.js');

rename('src/files/custom/Espo/Modules/MyModuleName', 'src/files/custom/Espo/Modules/'. $name);
rename('src/files/client/custom/modules/my-module-name', 'src/files/client/custom/modules/'. $nameHyphen);

rename('tests/unit/Espo/Modules/MyModuleName', 'tests/unit/Espo/Modules/'. $name);
rename('tests/integration/Espo/Modules/MyModuleName', 'tests/integration/Espo/Modules/'. $name);

echo "Ready. Now you need to run 'npm install'.\n";
