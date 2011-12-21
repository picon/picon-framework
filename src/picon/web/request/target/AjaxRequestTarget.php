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
 * Description of AjaxRequestTarget
 *
 * @author Martin Cassidy
 */
class AjaxRequestTarget implements RequestTarget
{
    private $components = array();
    private $script = array();

    /**
     * Add the component to the ajax response. This component will be re rendered
     * and replaced on the client side with the new version
     * @param Component $component The component to rerender. It must have output markup id as true
     * and must not be render body only
     */
    public function add(Component &$component)
    {
        if($component instanceof WebPage)
        {
            throw new \InvalidArgumentException('A page cannot be added the ajax request target');
        }
        elseif($component instanceof AbstractRepeater)
        {
            throw new \InvalidArgumentException('A repeater cannot be directly added to an ajax request target. Add a parent component instead');
        }
        array_push($this->components, $component);
    }
    
    /**
     * Add some javascript to execute to the ajax response
     * @param string $script The script to run
     */
    public function executeScript($script)
    {
        array_push($this->script, $script);
    }
    
    public function respond(Response $response)
    {
        $ajaxResponse = array();
        $ajaxResponse['components'] = array();
        $ajaxResponse['script'] = $this->script;
        
        foreach($this->components as $component)
        {
            $response->clean();
            $component->render();
            $value = $response->getBody();
            $response->clean();
            array_push($ajaxResponse['components'], array('id' => $component->getMarkupId(), 'value' => $value));
        }
        
        print(json_encode($ajaxResponse));
    }
}

?>
