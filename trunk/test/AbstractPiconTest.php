<?php

/**
 * Picon Framework
 * http://code.google.com/p/picon-framework/
 *
 * Copyright (C) 2011-2012 Martin Cassidy <martin.cassidy@webquub.com>

 * Picon Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * Picon Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with Picon Framework.  If not, see <http://www.gnu.org/licenses/>.
 * */

/**
 * Same constants as defined normally by index.php
 */
define("PICON_DIRECTORY", __DIR__.'\\..\\src\\picon');
define("ASSETS_DIRECTORY", __DIR__.'\\testassets');
define("CONFIG_FILE", __DIR__.'\\config\\picon.xml');

require_once(PICON_DIRECTORY."\\addendum\\annotation_parser.php");
require_once(PICON_DIRECTORY."\\addendum\\annotations.php");
require_once(PICON_DIRECTORY."\\addendum\\doc_comment.php");
require_once("TestAutoLoader.php");

class AbstractPiconTest extends PHPUnit_Framework_TestCase
{
    private static $autoLoader;
    
    public static function setUpBeforeClass()
    {
        AbstractPiconTest::$autoLoader = new TestAutoLoader();
        AbstractPiconTest::loadAssets(ASSETS_DIRECTORY);
    }
    
    public function test()
    {
        /*
        * actually doesn't test anything but allows this super class
        * to exist without an error
        */
    }
    
    private static function loadAssets($directory)
    {
        $d = dir($directory);
        while (false !== ($entry = $d->read()))
        {
            if(preg_match("/\s*.php{1}$/", $entry))
            {
                require_once($directory."\\".$entry);
            }
            if(is_dir($directory."\\".$entry) && !preg_match("/^.{1}.?$/", $entry))
            {
               self::loadAssets($directory."\\".$entry);
            }
        }
        $d->close();
    }
    
    protected function getContext()
    {
        $loader = new \picon\AutoContextLoader();
        return $loader->load();
    }
}

?>
