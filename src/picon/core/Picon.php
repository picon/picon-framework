<?php
/**
 * Picon Framework
 * http://piconframework.com
 *
 * Copyright (C) 2011-2015 Martin Cassidy <martin.cassidy@webquub.com>

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

namespace picon\core;

/**
 * Class Picon
 * @package picon\core
 */
class Picon
{
    /**
     * @var array The directory or directories in which all the application source code resides
     */
    public static $sources = array();

    /**
     * @var array The Picon Framework modules to search for
     */
    private static $modules = array("core", "web", "beans", "context", "jquery");

    /**
     * Initialise the Picon Framework. Callable after the auto loader has been added and Picon::$sources has been set.
     */
    public static function initialise()
    {
        PiconErrorHandler::initialise();
        foreach(Picon::$modules as $module)
        {
            $className = sprintf("picon\%s\%sInitialiser", $module, ucwords($module));

            if(class_exists($className) && in_array(sprintf("%s\ModuleInitialiser", __NAMESPACE__), class_implements($className)))
            {
                $reflection = new \ReflectionClass($className);
                $reflection->getMethod("initialise")->invoke($reflection->newInstance());
            }
        }
    }
}