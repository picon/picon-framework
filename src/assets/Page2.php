<?php

use picon\TabPanel;
use picon\TabCollection;
/**
 * Sample page
 *
 * @author Martin Cassidy
 * @Path(path = 'home1')
 */
class Page2 extends AbstractPage
{
    public function __construct()
    {
        parent::__construct(); 
        $container = new TabCollection();
        
        $container->addTab('Tab 1', function($id)
        {
            return new ExamplePanel($id);
        });
        
        $container->addTab('Tab 2', function($id)
        {
            return new SamplePanel2($id);
        });
        
        $this->add(new TabPanel('tabs', $container));
    }
}

?>
