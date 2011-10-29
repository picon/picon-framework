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
 * @todo Document, alter to use callback method and create markup domain classes
 * @author Martin Cassidy
 */
class MarkupParser
{
    private $pageStack;
    //@todo shouldn't be static
    private static $xmlParser;

    public function __construct()
    {
        $pageStack = array();

        if (!isset(self::$xmlParser))
        {
            self::$xmlParser = xml_parser_create('');
            xml_parser_set_option(self::$xmlParser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
            xml_parser_set_option(self::$xmlParser, XML_OPTION_CASE_FOLDING, 0);
            xml_parser_set_option(self::$xmlParser, XML_OPTION_SKIP_WHITE, 1);
        }
    }

    public function process($html)
    {
        $values = array();
        xml_parse_into_struct(self::$xmlParser, trim($html), $values);
        xml_parser_free(self::$xmlParser);
        return $this->processTags($values);
    }

    private function processTags($markup)
    {
        $rootTag = $this->createTagFromElement($markup[0]);
        $this->pageStack[0] = $rootTag;
        foreach (array_slice($markup, 1, count($markup)) as $element)
        {
            $tag = $this->createTagFromElement($element);
            $level = $element['level'] - 1; //XML Parser starts with 1 not 0
            $this->pageStack[$level] = $tag;

            switch ($element['type'])
            {
                case 'complete':
                    $this->addToParent($tag, $level);
                    break;
                case 'cdata':
                    $this->addToParent($element['value'], $level);
                    break;
                case 'open':
                    $tag->setContents(array());
                    $this->addToParent($tag, $level);
                    break;
                case 'close':
                    break;
                default:
                    trigger_error(sprintf("Unexpected tag type %s", $element['type']), E_USER_ERROR);
            }
        }
        return $rootTag;
    }

    private function addToParent($content, $level)
    {
        $contents = $this->pageStack[$level - 1]->getContents();
        array_push($contents, $content);
        $this->pageStack[$level - 1]->setContents($contents);
    }

    private function createTagFromElement($element)
    {

        $tag = new Tag();
        $tag->setElement($element['tag']);

        if (array_key_exists('attributes', $element))
        {
            $tag->setAttributes($element['attributes']);
            $tag->setAttributes($element['attributes']);
        }
        $tag->setContents(array());
        if (array_key_exists('value', $element))
        {
            $tag->setContents(array($element['value']));
        }

        return $tag;
    }

}

?>
