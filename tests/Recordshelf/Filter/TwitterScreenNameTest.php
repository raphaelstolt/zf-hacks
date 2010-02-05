<?php
require_once 'Recordshelf/Filter/TwitterScreenName.php';

class Recordshelf_Filter_TwitterScreenNameTest extends PHPUnit_Framework_TestCase
{
    protected $_filter;
    
    public function setUp()
    {
        $this->_filter = new Recordshelf_Filter_TwitterScreenName();
    }
    public function testAtSignShouldBeRemovedFromScreenName()
    {
        $this->assertSame($this->_filter->filter('@somebody'), 'somebody');
    }
}