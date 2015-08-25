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

namespace picon\web\markup\resolver;
use picon\web\domain\ComponentTag;
use picon\web\domain\PiconTag;
use picon\web\markup\html\HeaderContainer;
use picon\web\MarkupContainer;
use picon\web\pages\WebPage;
use picon\web\markup\html\TransparentMarkupContainer;

/**
 * Resolver for picon:hid and also the normal html head
 *
 * @author Martin Cassidy
 * @package web/markup/resolver
 */
class HeaderResolver implements ComponentResolver
{
    const HEADER_ID = 'picon_header';
    public function resolve(MarkupContainer $container, ComponentTag &$tag)
    {
        if($tag instanceof PiconTag && $tag->getName()=='head')
        {
            return new HeaderContainer(self::HEADER_ID);
        }
        else if($tag instanceof PiconTag && $tag->isHeaderTag())
        {
            if($container instanceof WebPage)
            {
                //@todo this block is UNTESTED 
                $header = new HeaderContainer('header'.$container->getPage()->getAutoIndex());
                
                $inner = new TransparentMarkupContainer('picon_header');
                $inner->setRenderBodyOnly(true);
                $header->add($inner);
                return $header;
                
            }
            elseif($container instanceof HeaderContainer)
            {
                $header = new TransparentMarkupContainer('picon_header');
                $header->setRenderBodyOnly(true);
                return $header;
            }
            
            throw new \RuntimeException('<picon:head> tag was in an invalid location. It must be outside of <picon:extend> and <picon:panel>');
        }
        
        return null;
    }
}

?>
