<?php

use \picon\MarkupContainer;
use \picon\Link;
use \picon\ListView;
use \picon\ArrayModel;
use \picon\BasicModel;
use \picon\Label;
use \picon\ResourceReference;
use \picon\HeaderResponse;

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
        parent::__construct();
        $one = new MarkupContainer("one");
        $two = new MarkupContainer("two");
        $one->add($two);
        $this->add($one);
        $me = $this;
        $two->add(new Link('link', function() use($me)
        {
            $me->setPage(Page2::getIdentifier());
        }));
        $two->add(new Link('formLink', function() use($me)
        {
            $me->setPage(FormPage::getIdentifier());
        }));
        $two->add(new Link('dbLink', function() use($me)
        {
            $me->setPage(DatabaseTestPage::getIdentifier());
        }));
        
        $two->add(new Link('authPage', function() use($me)
        {
            $me->setPage(SampleAuthorisedPage::getIdentifier());
        }));
        $two->add(new Link('ajaxPage', function() use($me)
        {
            $me->setPage(AjaxPage::getIdentifier());
        }));
        
        
        
        $fruit = array('apples', 'pears', 'bananas', 'oranges');
        
        $this->add(new ListView('fruit', function($entry)
        {
            $entry->add(new Label('name', new BasicModel($entry->getModelObject())));
        }, new ArrayModel($fruit)));
        
        $this->add(new ExamplePanel('samplePanel'));
        
        $this->add(new SampleBorder('sampleBorder'));
    }
    
    public function renderHead(HeaderResponse $headerResponse)
    {
        $headerResponse->renderCSS(new ResourceReference('test.css', static::getIdentifier()));
    }
}

?>
