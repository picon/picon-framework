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
 * 
 * Repository path:    $HeadURL$
 * Last committed:     $Revision$
 * Last changed by:    $Author$
 * Last changed date:  $Date$
 * ID:                 $Id$
 * 
 * */

use picon\Panel;

/**
 * Description of CodeOutputPanel
 * 
 * @author Martin Cassidy
 */
class CodeOutputPanel extends Panel
{
    public function __construct($id, $filename)
    {
        parent::__construct($id);
        $file = fopen($filename, 'r');
        $contents = fread($file, filesize($filename));
        fclose($file);
        $this->add(new picon\Label('code', new picon\BasicModel($this->format($contents))));
    }
    
    private function getColour($type)
    {
        $colours = array(T_STRING => '#000', T_VARIABLE => '#800', T_DOC_COMMENT => '#005500', T_COMMENT => '#005500');
        if(array_key_exists($type, $colours))
        {
            return $colours[$type];
        }
        else if($type!=null)
        {
            return '#000099';
        }
        return null;
    }
    
    private function format($code)
    {
        $output = "";

        foreach(token_get_all($code) as $token)
        {
            $token_name = is_array($token) ? $token[0] : null;
            $token_data = is_array($token) ? $token[1] : $token;
            
            $token_data = htmlentities($token_data);
            $token_data = str_replace("\n", '<br/>', $token_data);
            $token_data = str_replace(" ", '&nbsp;', $token_data);
            $token_data = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $token_data);
            
            $colour = $this->getColour($token_name);
            
            if($colour!=null)
            {
                $output .= sprintf('<span style="color:%s;">%s</span>', $colour, $token_data);
            }
            else
            {
                $output .= $token_data;
            }
        }
        
        return $output;
    }
}

?>
