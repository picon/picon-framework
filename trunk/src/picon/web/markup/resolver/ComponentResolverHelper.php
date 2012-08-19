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

namespace picon;

/**
 * Stores all of the available component resolvers for use during rendering
 *
 * @author Martin Cassidy
 * @package web/markup/resolver
 */
class ComponentResolverHelper
{
	private static $resolvers;

	private static function init()
	{
		if(self::$resolvers==null)
		{
			self::$resolvers = array();
			array_push(self::$resolvers, new HeaderResolver());
			array_push(self::$resolvers, new PanelResolver());
			array_push(self::$resolvers, new ExtendResolver());
			array_push(self::$resolvers, new BorderResolver());
		}
	}

	public static function resolve(MarkupContainer $container, ComponentTag &$tag)
	{
		self::init();
		$current = $container;
		$component = null;
		while($current!=null)
		{
			if($current instanceof ComponentResolver)
			{
				$component = $current->resolve($container, $tag);
			}
			if($component!=null)
			{
				return $component;
			}
			$current = $current->getParent();
		}

		foreach(self::$resolvers as $resolver)
		{
			$component = $resolver->resolve($container, $tag);
			if($component!=null)
			{
				return $component;
			}
		}
		return null;
	}
}

?>
