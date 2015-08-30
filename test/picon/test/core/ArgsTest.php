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

namespace picon\test\core;


use picon\core\Args;
use picon\core\cache\PiconSerializer;
use picon\core\utils\SerializableClosure;

class ArgsTest extends \PHPUnit_Framework_TestCase
{
    public function testValidCallbacks()
    {
        $callback = function(){};
        Args::callBack($callback, "test");

        $serialsed = PiconSerializer::serialize($callback);
        $serialisableClosure = PiconSerializer::unserialize($serialsed);

        Args::callBack($serialisableClosure, "test");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNullCallback()
    {
        Args::callBack(null, "test");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidCallback()
    {
        Args::callBack("string", "test");
    }

    public function testValidCallbackArgs()
    {
        $callback = function(){};
        Args::callBackArgs($callback, 0, "test");

        $callback = function($one, $two, $three){};
        Args::callBackArgs($callback, 3, "test");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidCallbackArgs()
    {
        $callback = function($one, $two, $three){};
        Args::callBackArgs($callback, 2, "test");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidSerialisedCallbackArgs()
    {
        $callback = function($one, $two, $three){};

        $serialsed = PiconSerializer::serialize($callback);
        $serialisableClosure = PiconSerializer::unserialize($serialsed);

        Args::callBackArgs($serialisableClosure, 2, "test");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNoCallbackArgs()
    {
        Args::callBackArgs("not a callback", 2, "test");
    }
}