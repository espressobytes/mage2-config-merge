
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



