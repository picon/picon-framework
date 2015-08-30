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

use mindplay\annotations\Annotations;
use picon\core\cache\CacheManager;
use picon\core\Picon;

require_once(dirname(__FILE__)."/../vendor/autoload.php");

Picon::$sources = array(dirname(__FILE__) . "/picon/test/app");
CacheManager::$cacheDirectory = dirname(__FILE__)."/cache";
Picon::initialise();

$annotationManager = Annotations::getManager();
$annotationManager->registry['codeCoverageIgnore'] = false;
$annotationManager->registry['scenario'] = false;
$annotationManager->registry['test'] = false;
$annotationManager->registry['expectedException'] = false;