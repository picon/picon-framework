<?php

use picon\ApplicationInitializer;
use picon\PiconApplication;
use picon\BaseApplicationInitializer;

/**
 * Path to the root picon directory without a trailing slash
 * This is the directory containing PiconApplication
 */
define("PICON_DIRECTORY", __DIR__.'/../src/picon');

/**
 * Path to the assets directory in which the user
 * created classes reside
 */
define("ASSETS_DIRECTORY", __DIR__.'/../demo/assets');

/**
 * Path to the config directory in which the xml config files
 * reside
 */
define("CONFIG_FILE", __DIR__.'/../demo/config/picon.xml');

/**
 * Path to the cache directory in which persisted resources
 * will be stored. This directory needs write access
 */
define("CACHE_DIRECTORY", __DIR__.'/../demo/cache');

require_once(dirname(__FILE__) . "/picon/core/PiconApplication.php");
require_once(dirname(__FILE__) . "/picon/core/BaseApplicationInitializer.php");

class StandaloneApplication extends PiconApplication
{
    public function run()
    {

    }

    protected function getApplicationInitializer()
    {
        return new BaseApplicationInitializer();
    }
}

$app = new StandaloneApplication();

ApplicationInitializer::loadAssets(__DIR__);

function handleFile($fileName)
{
    $file = new \SplFileObject($fileName);

    $code = "";
    while (!$file->eof())
    {
        $code .= $file->current();
        $file->next();
    }

    $codeBlocks = token_get_all($code);

    foreach($codeBlocks as $block)
    {
        if(!is_array($block))
        {
            continue;
        }
        if($block[0]==T_FUNCTION)
        {
            print($fileName . " - uses " . $block[1]."\r\n");
        }
    }
}

function process($directory)
{
    $d = dir($directory);

    while ((false !== ($entry = $d->read())))
    {
        if(preg_match("/.*\\.php/", $entry))
        {
            require_once($directory."/".$entry);
            handleFile($directory."/".$entry);
            print($directory."/".$entry."\r\n");
        }
        if(is_dir($directory."/".$entry) && !preg_match("/^\\.{1}\\.?$/", $entry))
        {
            process($directory."/".$entry);
        }
    }
    $d->close();
}

process(__DIR__);