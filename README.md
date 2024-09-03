# Template repository for EspoCRM extensions

This repository is a fork of the espocrm/ext-template repository. It has two operating modes, which are invoked as follows:
1. Regular: `node build ...`
2. Custom `node buildc ...`

Regular mode has the same behavior as the original repository. Custom mode introduces many new features that make template development faster and easier in many situations.

### Custom Extension Tools
This repository uses bandtank/espocrm-extension-tools, which is based on espocrm/extension-tools. The enhancements provided by the custom tools repository speed up development in some situations, such as:
* Dropping and recreating the database to start fresh
* Running the sequence of steps from the `all` flag starting at `copy` until the end
* Running the `Before Install` scripts
* Including development packages in `composer install`
* Using a local copy of the EspoCRM archive instead of downloading it from Github. Multiple branches can be archived simultaneously.
* Running development-only PHP scripts, such as BeforeInstallDevelopment.php
* Adding module-specific constants, both for development and production

The original commandline switches are as follows:
* `--after-install`
* `--all`
* `--composer-install`
* `--copy`
* `--copy-file`
* `--extension`
* `--fetch`
* `--rebuild`

The new commandline switches are as follows:
* `--before-install`             Run only the beforeInstall process for the extension
* `--copy-to-end`                Run the `all` switch from the `copy` step until the end
* `--db-reset`                   Create (or drop and recreate) the database schema (only the schema, no tables)
* `--local`                      Use with any fetch command (`--all`, `--update-archive`, etc.) to use a local version of the repository instead of downloading it
* `--update-archive`             Download and store the latest version of Espo in the given branch for reuse


### Example Workflows
* `node buildc --all [--db-reset] [--local]`
* `node buildc --fetch --local; node buildc --install`
* `node buildc --copy`
* `node buildc --copy; node buildc --composer-install`

### Development Scripts
The espocrm-extension-template repository defines several files which are meant to be used for development purposes only. This repository is configured to ignore the development-only files when building the extension package. Here is the list of ignored files:
```javascript
const ignore = [
    cwd + '/src/files/custom/Espo/Modules/' + extensionParams.module + '/Classes/ConstantsDevelopment.php',
    cwd + '/src/scripts/AfterInstallDevelopment.php',
    cwd + '/src/scripts/AfterUninstallDevelopment.php',
    cwd + '/src/scripts/BeforeInstallDevelopment.php',
    cwd + '/src/scripts/BeforeUninstallDevelopment.php',
];
```

### Constants
This repository includes two files that are meant to allow global constants to be used throughout the module:
* `src/files/custom/Espo/Modules/<ModuleName>/Class/Constants.php`
* `src/files/custom/Espo/Modules/<ModuleName>/Class/ConstantsDevelopment.php`

The development scripts automatically include the appropriate files, and the build script automatically exclused the appropriate files.

## Preparing repository

Run:

```
php init.php
```

It will ask to enter an extension name and some other information.

After that, you can remove `init.php` file from your respository. Commit changes and proceed to configuration & building.


## Configuration

Create `config.json` file in the root directory. You can copy `config-default.json` and rename it to `config.json`.

When reading, this config will be merged with `config-default.json`. You can override default parameters in the created config.

Parameters:

* espocrm.repository - from what repository to fetch EspoCRM;
* espocrm.branch - what branch to fetch (`stable` is set by default); you can specify version number instead (e.g. `5.9.2`);
* database - credentials of the dev database;
* install.siteUrl - site url of the dev instance;
* install.defaultOwner - a webserver owner (important to be set right);
* install.defaultGroup - a webserver group (important to be set right).

## Create the database

Create the databse manually or using `node buildc --db-reset`.

## Config for EspoCRM instance

You can override EspoCRM config. Create `config.php` in the root directory of the repository. This file will be applied after EspoCRM installation (when building).

Example:

```php
<?php
return [
    'useCache' => False,
    'useCacheInDeveloperMode' => False,
    'isDeveloperMode' => True,
    'logger' => [
      'level' => 'DEBUG',
    ],
];
```

## Building

After building, EspoCRM instance with installed extension will be available at `site` directory. You will be able to access it with credentials:

* Username: admin
* Password: 1

### Preparation

1. You need to have *node*, *npm*, *composer* installed.
2. Run `npm install`.
3. Create a database. The database name is set in the config file.

### Full EspoCRM instance building

Download the latest release of EspoCRM:

```
node buildc --update-archive
```

Build and install the application and extension using the local archive of EspoCRM:
```
node buildc --all --local
```

The `--all` switch will remove previous builds in `site/`, but the database will not be modified. If you want to reset the database, use the `--db-reset` switch as well. If errors occur during installation, check EspoCRM's application log files in `site/data/logs/`. It is also useful to check the webserver's log files in some situations.

### Copying extension files to EspoCRM instance

You need to run this command every time you make changes in `src` directory and you want to try these changes on Espo instance.

Command:

```
node buildc --copy
```

You can set up a file watcher in your IDE to avoid running this command manually. See below about the file watcher.

### Running the before-install script
BeforeInstall.php will be executed against the EspoCRM installation in `site/` by running the following command:
```
node buildc --before-install
```

### Running the after-install script
AfterInstall.php will be executed against the EspoCRM installation in `site/` by running the following command:
```
node buildc --after-install
```
### Extension package building
Build the extension using the custom build script to ignore the development scripts and classes:
```
node buildc --extension
```
The original build script will not ignore the default development files. The list of default development files is as follows:
```
src/scripts/AfterInstallDevelopment.php
src/scripts/AfterUninstallDevelopment.php
src/scripts/BeforeInstallDevelopment.php
src/scripts/BeforeUninstallDevelopment.php
src/files/custom/Espo/Modules/MyModuleName/Classes/ConstantsDevelopment.php
```
The package will be created in `build/` using the version number in `package.json`.

### Installing addition extensions

If your extension requires other extensions, there is a way to install them automatically while building the instance.

Necessary steps:

1. Add the current EspoCRM version to the `config.php`:

```php
<?php
return [
    'version' => '6.2.0',
];

```

2. Create the `extensions` directory in the root directory of your repository.
3. Put needed extensions (e.g. `my-extension-1.0.0.zip`) in this directory.

Extensions will be installed automatically after running the command `node buildc --all` or `node buildc --install`.

## Development workflow
1. Develop the extension in `src/`
2. Run:
    * `node buildc --copy` to:
      * Copy the extension to `site/`
      * Set file and folder ownership
    * `node buildc --copy-to-end` to run:
      * Copy the extension to `site/`
      * Run the BeforeInstall scripts (production then development)
      * Run composer install (including development packages)
      * Rebuild the EspoCRM instance
      * Run the AfterInstall scripts (production then development)
      * Set file and folder ownership
3. Test the changes in the application:
    * GUI - Visit the site in a browser (`site/` is the application's root folder)
    * API - Query the instance using HTTP requests

## Using entity manager to create entities

You can block out new entity types right in Espo (using Entity Manager) and then copy generated custom files (`site/custom` dir) to the repository (`src` dir) using `copy-custom.js` script.

1. Create entity types, fields, layouts, relationships in Espo (it should be available in `site` dir after building).
2. Run `node copy-custom.js`. It will copy all files from `site/custom` to `src/files/custom/Espo/Modules/{ModuleName}` and apply needed modifications to files.
3. Remove files from `site/custom`.
4. Run `node buildc --copy`. It will copy files from the repository to Espo build (`site/custom//Espo/Modules/{ModuleName}` dir).
5. Clear cache in Espo.
6. Test in Espo.
7. Commit changes.
8. Profit.

You can remove `copy-custom.js` from the repository if you don't plan to use it future.

## Using composer in the extension

If your extension requires additional libraries, the libraries can be installed by composer:

1. Create a file: `src/files/custom/Espo/Modules/{ModuleName}/composer.json`
2. Run `node buildc --all` or `node buildc --composer-install` to run `composer install`. The dependencies in the new `composer.json` file will be installed automatically.
3. Create a file: `src/files/custom/Espo/Modules/{ModuleName}/Resources/autoload.json`

Note: The extension build will contain only the `vendor` directory without the `composer.json` file.

The `autoload.json` file defines paths for namespaces:

```json
{
    "psr-4": {
        "LibraryNamespace\\": "custom/Espo/Modules/{ModuleName}/vendor/<vendor-name>/<library-name>/path/to/src"
    }
}
```


### Development Packages
The original repository does _not_ allow composer to install development packages. The custom repository _does_ allow development packages to be installed with composer. For example, `fzaninotto/Faker` allows PHP scripts to generate fake data, which is helpful for testing. However, `fzaninotto/Faker` most likely should not be installed by the extension on a production system. To add development dependencies, add the following to the `composer.json` file in your module's code:

`src/files/custom/Espo/Modules/{ModuleName}/composer.json`:
```json
{
    "require-dev": {
        "fakerphp/faker": "^1.23"
    }
}
```
`src/files/custom/Espo/Modules/{ModuleName}/Resources/autoload.json`:
```json
{
    "psr-4": {
        "Faker\\": "custom/Espo/Modules/{ModuleName}/vendor/faker/src/Faker/"
    }
}
```
## Versioning

The version number is stored in `package.json` and `package-lock.json`.

Bumping version:

```
npm version patch
npm version minor
npm version major
```

## Tests

### Unit

Run composer install:

```
`(cd site; composer install)`
```

Command to run unit tests:

```
node buildc --copy; site/vendor/bin/phpunit site/tests/unit/Espo/Modules/EntraSSO
```

### Integration

You need to build a test instance first:

1. `node buildc --copy`
2. `(cd site; grunt test)`

You need to create a config file `tests/integration/config.php`:

```php
<?php

return [
    'database' => [
        'driver' => 'pdo_mysql',
        'host' => 'localhost',
        'charset' => 'utf8mb4',
        'dbname' => 'TEST_DB_NAME',
        'user' => 'YOUR_DB_USER',
        'password' => 'YOUR_DB_PASSWORD',
    ],
];
```

The file should exist before you run `node buildc --copy`.

Command to run integration tests:

```
(cd site && vendor/bin/phpunit tests/integration/Espo/Modules/EntraSSO)
```

## Configuring IDE

You need to set the following paths to be ignored in your IDE:

* `build`
* `site/build`
* `site/custom/`
* `site/client/custom/`
* `site/tests/unit/Espo/Modules/EntraSSO`
* `site/tests/integration/Espo/Modules/EntraSSO`

### File watcher

You can set up a file watcher in the IDE to automatically copy and transpile files upon saving.

File watcher parameters for PhpStorm:

* Program: `node`
* Arguments: `buildc --copy-file --file=$FilePathRelativeToProjectRoot$`
* Working Directory: `$ProjectFileDir$`

## Using ES modules

*As of v8.0.*

The initialization script asks whether you want to use ES6 modules. If you choose "NO", you still can switch to ES6 later:

1. Set *bundled* to true in `extension.json`.
2. Set *bundled* and *jsTranspiled* to true in `src/files/custom/Espo/Modules/EntraSSO/Resources/module.json`.
3. Add `src/files/custom/Espo/Modules/EntraSSO/Resources/metadata/app/client.json`
    ```json
    {
        "scriptList": [
            "__APPEND__",
            "client/custom/modules/entra-s-s-o/lib/init.js"
        ]
    }
    ```

## License

Change a license in `LICENSE` file. The current license is intended for scripts of this repository. It's not supposed to be used for code of your extension.
