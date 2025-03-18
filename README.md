# Template repository for EspoCRM extensions

This repository is a fork of the espocrm/ext-template repository. It has two operating modes, which are invoked as follows:
1. Custom: `node build ...`
2. Original `node buildo ...`

Regular mode has the same behavior as the original repository. Custom mode introduces many new features that make template development faster and easier in many situations. It is assumed you will always use the custom build script, which is why this file uses `node build` in all instructions.

## Custom Extension Tools
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
* `--extension`                  Build the extension for distribution
* `--rebuild`                    Rebuild Espo's configuration (CLI version of UI->Administration->Rebuild)
* `--local`                      Use with any fetch command (`--all`, `--update-archive`, etc.) to use a local version of the repository instead of downloading it
* `--update-archive`             Download and store the latest version of Espo in the given branch for reuse

## Example Workflows
* `npm run clean && npm install && node build --all --local --db-reset`
* `node build --all [--db-reset] [--local]`
* `node build --fetch --local; node build --install`
* `node build --copy`
* `node build --copy; node build --composer-install`

## Development Scripts
The espocrm-extension-template repository defines several files which are meant to be used for development purposes only. The custom build script ignores the development-only files when building the extension package. Here is the list of ignored files:
```
src/scripts/AfterInstallDevelopment.php
src/scripts/AfterUninstallDevelopment.php
src/scripts/BeforeInstallDevelopment.php
src/scripts/BeforeUninstallDevelopment.php
src/files/custom/Espo/Modules/{@name}/Classes/ConstantsDevelopment.php
```

## Constants
This repository includes two files that are meant to allow global constants to be used throughout the module:
* `src/files/custom/Espo/Modules/{@name}/Class/Constants.php`
* `src/files/custom/Espo/Modules/{@name}/Class/ConstantsDevelopment.php`

The development scripts (for example, `BeforeInstallDevelopment.php`) automatically include the appropriate files, and the build script automatically excludes the appropriate files.

## Extension Developer Tools
Use the "Extension Developer Tools" extension to add much-needed functionality during extension development:
* Fake Data - Quickly populate the EspoCRM instance with fake data
* Sandbox Jobs - Create jobs to easily execute functions automatically and manually

# Preparing repository
Run:
```
php init.php
```
The script will ask the user to enter an extension name and some other information. The `init.php` file may be removed from your repository after it successfully completes. At this point, a commit may be made to store the changes to the repository.

## Configuration
Create a file called `config.json` in the root directory. You can copy `config-default.json` and rename it to `config.json`. During the build process, the configuration files will be merged and the keys in `config-default.json` will be overridden by `config.json`, if applicable.

Parameters:
* espocrm.repository - from what repository to fetch EspoCRM;
* espocrm.branch - what branch to fetch (`stable` is set by default); you can specify version number instead (e.g. `5.9.2`);
* database - credentials of the dev database;
* install.siteUrl - site url of the dev instance;
* install.defaultOwner - a webserver owner (important to be set right);
* install.defaultGroup - a webserver group (important to be set right).

## Create the database
Create the database manually or using `node build --db-reset`.

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
3. Use `node build --db-reset` to automatically create the database using the parameters in the configuration file.

### Full EspoCRM instance building
Download the latest release of EspoCRM:
```
node build --update-archive
```

Build and install the application and extension using the local archive of EspoCRM:
```
node build --all --local
```

The `--all` switch will remove previous builds in `site/`, but the database will not be modified. If you want to reset the database, use the `--db-reset` switch as well. If errors occur during installation, check EspoCRM's application log files in `site/data/logs/`. It is also useful to check the webserver's log files in some situations.

### Copying extension files to EspoCRM instance
Run this command whenever changes have been made in `src/` that need to copied to the instance in `site/`:
```
node build --copy
```
Optionally, a file watcher may be configured to run the `--copy` command automatically. See a later section to learn more about file watchers.

Note: Running this command removes the `vendor` folder in `custom/Espo/Modules/{@name}/vendor`. You must run:
```
node build --composer-install
```
to regenerate the `vendor` folder.

### Running the before-install scripts
`BeforeInstall.php` and `BeforeInstallDevelopment.php` will be executed against the EspoCRM installation in `site/` by running the following command:
```
node build --before-install
```

### Running the after-install scripts
`AfterInstall.php` and `AfterInstallDevelopment.php` will be executed against the EspoCRM installation in `site/` by running the following command:
```
node build --after-install
```

### Extension package building
Build the extension using the custom build script to ignore the development scripts and classes:
```
node build --extension
```
The package will be created in `build/` using the version number in `package.json`.

Note: The original build script will not ignore the default development files.

### Installing additional extensions
To install other extensions during the build process, follow the steps below:

1. Add the current EspoCRM version to the `config.php`:
```php
<?php
return [
    'version' => '9.0.0',
];

```
2. Create the `extensions` directory in the root of the repository.
3. Add extensions (e.g. `my-extension-1.0.0.zip`) to the `extensions` directory.

Extensions will be installed automatically after running the command `node build --all` or `node build --install`.

## Development workflow
1. Develop the extension in `src/`
2. Run:
    * `node build --copy` to:
      * Copy the extension to `site/`
      * Set file and folder ownership
      * Optionally: `node build --composer-install` to reinstall vendor packages
    * `node build --copy-to-end` to run:
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
2. Run `node copy-custom.js`. It will copy all files from `site/custom` to `src/files/custom/Espo/Modules/{@name}` and apply needed modifications to files.
3. Remove files from `site/custom`.
4. Run `node build --copy`. It will copy files from the repository to Espo build (`site/custom/Espo/Modules/{@name}` dir).
5. Clear cache in Espo.
6. Test in Espo.
7. Commit changes.

You can remove `copy-custom.js` from the repository if you don't plan to use it future.

## Using composer in the extension

If your extension requires additional libraries, the libraries can be installed by composer:

1. Create a file: `src/files/custom/Espo/Modules/{@name}/composer.json` with the following structure:
    ```json
    {
        "require": {
            "{library/namespace}": "{version}"
        }
    }
    ```
   The `composer.json` file defines a list of required libraries that will be installed with the extension. **Note: The final build will contain only the `vendor` directory without the `composer.json` file.**
2. Run `node build --all` or `node build --composer-install` to run `composer install`. The dependencies in the new `composer.json` file will be installed automatically.
3. Create a file: `src/files/custom/Espo/Modules/{@name}/Resources/autoload.json` with the following structure:
    ```json
    {
        "psr-4": {
            "{LibraryNamespace\\MoreNamespace}\\": "custom/Espo/Modules/{@name}/vendor/<vendor-name>/<library-name>/path/to/src"
        }
    }
    ```
    The `autoload.json` file defines paths for namespaces in PHP.

### Example:
To use the [fzaninotto/Faker](https://github.com/fzaninotto/Faker) library, update the following files accordingly:

`src/files/custom/Espo/Modules/{@name}/composer.json`:
```json
{
    "require": {
        "fakerphp/faker": "^1.23"
    }
}
```
`src/files/custom/Espo/Modules/{@name}/Resources/autoload.json`:
```json
{
    "psr-4": {
        "Faker\\": "custom/Espo/Modules/{@name}/vendor/faker/src/Faker/"
    }
}
```
Run `node build --composer-install`. You can now use the library in PHP; e.g.:
```php
use Faker\Generator;
```

## Using composer to install development packages
The original repository does _not_ allow composer to install development packages. The custom repository _does_ allow development packages to be installed with composer. In the `composer.json` file, add libraries to `require-dev` instead of `require` to tell the build system to install the libraries during development only. The production build process will ignore the libraries in `require-dev`. However, the production build process will **not** ignore the entries in `autoload.json`, so be aware of what you are adding to the project. In many cases, development libraries are better to add with a custom extension that is only intended to be used for the development of other extensions.

For example, [fzaninotto/Faker](https://github.com/fzaninotto/Faker) allows PHP scripts to generate fake data, which is helpful for testing. To make the library available only for development, change the previously created `composer.json` as follows:

```json
{
    "require-dev": {
        "fakerphp/faker": "^1.23"
    }
}
```
The `autoload.json` needs to have the same `psr-4` definition, which means the namespace will be available in the final build of the extension even if the library is only available during development.
`src/files/custom/Espo/Modules/{@name}/Resources/autoload.json`:

## Versioning

The version number is stored in `package.json` and `package-lock.json`.

Bumping version:

```
npm version patch
npm version minor
npm version major
```

## Tests

To prepare the Espo instance:

```
node build --prepare-test
```

Fetches the instance and runs composer install. To be used for unit tests and static analysis in CI environment. Takes less time than the full installation.


### Unit

Run composer install for the site:

```
(cd site; composer install)
```

Command to run unit tests:

```
(node build --copy; node build --composer-install; cd site; vendor/bin/phpunit tests/unit/Espo/Modules/{@name})
```
or
```
npm run unit-tests
```

### Integration

You need to build a test instance first:

1. `node build --copy`
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

Command to run integration tests:

```
(node build --copy; node build --composer-install; cd site; vendor/bin/phpunit tests/integration/Espo/Modules/{@name})
```

or
```
npm run integration-tests
```

Note that integration tests need a full installation.

### Static analysis

Command to run:

```
node build --copy; node build --composer-install; site/vendor/bin/phpstan
```

or
```
npm run sa
```

If your extension contains additional PHP packages, you also need to add `site/custom/Espo/Modules/{@name}/vendor` to the *scanDirectories* section in *phpstan.neon* config.

Note: You can omit *composer-install* command if your extension does not contain PHP packages.

## Configuring IDE

You need to set the following paths to be ignored in your IDE:

* `build`
* `site/build`
* `site/custom/`
* `site/client/custom/`
* `site/tests/unit/Espo/Modules/{@name}`
* `site/tests/integration/Espo/Modules/{@name}`

### File watcher

To avoid running this command manually, use a file watcher in your IDE. The configuration for PhpStorm is included in this repository. See below about the file watcher.

File watcher parameters for PhpStorm:

* Program: `node`
* Arguments: `build --copy-file --file=$FilePathRelativeToProjectRoot$`
* Working Directory: `$ProjectFileDir$`

Note: The File Watcher configuration for PhpStorm is included in this reposistory.

## Using ES modules

*As of EspoCRM v8.0.*

The initialization script in the original repository asks if you want to use ES6 modules. This repository forces the use of ES6 modules, which is the better way to do it in the long term. As a result, the following settings are configured by default:

1. Set *bundled* to true in `extension.json`.
2. Set *bundled* and *jsTranspiled* to true in `src/files/custom/Espo/Modules/{@name}/Resources/module.json`.
3. Add `src/files/custom/Espo/Modules/{@name}/Resources/metadata/app/client.json`
    ```json
    {
        "scriptList": [
            "__APPEND__",
            "client/custom/modules/{@nameHyphen}/lib/init.js"
        ]
    }
    ```

## Javascript frontend libraries
Install *rollup*.
In `extension.json`, add a command that will bundle the needed library into an AMD module. Example:
```json
{
    "scripts": [
        "npx rollup node_modules/some-lib/build/esm/index.mjs --format amd --file build/assets/lib/some-lib.js --amd.id some-lib"
    ]
}
```
Add the library module path to `src/files/custom/Espo/Modules/{@name}/Resources/metadata/app/jsLibs.json`
```json
{
    "some-lib": {
        "path": "client/custom/modules/{@nameHyphen}/lib/some-lib.js"
    }
}
```
When you build, the library module will be automatically included in the needed location.

Note that you may also need to create *rollup.config.js* to set some additional Rollup parameters that are not supported via CLI usage.

## License

Change a license in `LICENSE` file. The current license is intended for scripts of this repository. It's not supposed to be used for code of your extension.