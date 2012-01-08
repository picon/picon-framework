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
 * Description of HeaderResponse
 *
 * @author Martin Cassidy
 */
class HeaderResponse
{
    private $actualResponse;
    private $rendered = array();
    
    public function __construct(Response $response)
    {
        $this->actualResponse = $response;
    }
    
    public function renderCSS($css)
    {
        if(is_string($css))
        {
            $this->renderCSSFile($css);
        }
        else if($css instanceof ResourceReference)
        {
            $this->renderCSSResourceReference($css);
        }
        else
        {
            throw new \InvalidArgumentException('renderCSS() expects a string');
        }
    }
     
    public function renderCSSFile($file)
    {
        if(!$this->checkRenderedFile($file))
        {
            $this->actualResponse->write(sprintf("<link type=\"text/css\" rel=\"stylesheet\" href=\"%s\">", $file));
            array_push($this->rendered, $file);
        }
    }
    
    public function renderJavaScript($javaScript)
    {
        if(is_string($javaScript))
        {
            $this->renderJavaScriptFile($javaScript);
        }
        else if($javaScript instanceof ResourceReference)
        {
            $this->renderJavaScriptResourceReference($javaScript);
        }
        else
        {
            throw new \InvalidArgumentException('renderJavaScript() expects a string');
        }
    }
    
    public function renderJavaScriptFile($file)
    {
        if(!$this->checkRenderedFile($file))
        {
            $this->actualResponse->write(sprintf("<script type=\"text/javascript\" src=\"%s\"></script>", $file));
            array_push($this->rendered, $file);
        }
    }
    
    private function checkRenderedFile($file)
    {
        return in_array($file, $this->rendered);
    }
    
    public function renderCSSResourceReference(ResourceReference $reference)
    {
        $target = new ResourceRequestTarget($reference);
        $url = $GLOBALS['requestCycle']->generateUrl($target);
        $this->renderCSSFile($url);
    }
    
    public function renderJavaScriptResourceReference(ResourceReference $reference)
    {
        $target = new ResourceRequestTarget($reference);
        $url = $GLOBALS['requestCycle']->generateUrl($target);
        $this->renderJavaScriptFile($url);
    }
    
    public function renderScript($script)
    {
        $this->actualResponse->write(sprintf("<script type=\"text/javascript\">%s</script>", $script));
    }
}

?>
