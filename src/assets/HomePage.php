<?php

use \picon\MarkupContainer,\picon\Link,\picon\ListView,\picon\ArrayModel,\picon\BasicModel,\picon\Label;

/**
 * Sample Homepage
 *
 * @author Martin Cassidy
 * @Path(path = 'home')
 */
class HomePage extends AbstractPage
{
    public function __construct()
    {
        $one = new MarkupContainer("one");
        $two = new MarkupContainer("two");
        $one->add($two);
        $this->add($one);
        $me = $this;
        $two->add(new Link('link', function() use($me)
        {
            $me->setPage(Page2::getIdentifier());
        }));
        
        $fruit = array('apples', 'pears', 'bananas', 'oranges');
        
        $this->add(new ListView('fruit', new ArrayModel($fruit), function($entry)
        {
            $entry->add(new Label('name', new BasicModel($entry->getModelObject())));
        }));
        
        $this->add(new ExamplePanel('samplePanel'));
    }
}

?>
