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
namespace picon\beans;

/**
 *
 * @package picon\beans
 */
interface BeanFactory
{
    /**
     * @param string $name
     * @return mixed
     */
    function getBean($name);

    /**
     * @param string $name
     * @return boolean
     */
    function beanExists($name);
}