
## Module Espressobytes_MergeConfigFiles

### Introduction

This module has the purpose to merge two config-files (in the form of config.php) to a new config-file 

### Installation

Installation via composer

    composer require espressobytes/mergeconfigfiles

### Usage

In Magento-Root, use:

     bin/magento config-merge:merge-files <input config file 1> <input config file 2> <output config file> [<output config file> Default: app/etc/]

Example:

     bin/magento config-merge:merge-files config.php config_build_addons.php config_buildsystem.php

### Integration within deployment of Magento 2

To use this command within the deployment of Magento, the default command list has to be modified.
You can do that via composer-patches:

In your `composer.json add (example):

    ...
    "extra": {
        "patches": {
            "magento/magento2-base": {
                "Add new Command to CommandList": "patches/composer/magento2base_add_command.patch"
            },


Following this example, you have to have a new patch-file in the folder `patches/composer` named `magento2base_add_command.patch`.

Add this content to that file:

    Index: a/setup/src/Magento/Setup/Console/CommandList.php
    <+>UTF-8
    ===================================================================
    --- a/setup/src/Magento/Setup/Console/CommandList.php
    +++ a/setup/src/Magento/Setup/Console/CommandList.php
    @@ -74,7 +74,8 @@
                 \Magento\Setup\Console\Command\RollbackCommand::class,
                 \Magento\Setup\Console\Command\UpgradeCommand::class,
                 \Magento\Setup\Console\Command\UninstallCommand::class,
    -            \Magento\Setup\Console\Command\DeployStaticContentCommand::class
    +            \Magento\Setup\Console\Command\DeployStaticContentCommand::class,
    +            \Espressobytes\MergeConfigFiles\Command\MergeConfigFilesCommand::class
             ];
         }


