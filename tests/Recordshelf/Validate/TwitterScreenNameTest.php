<?php
require_once 'Recordshelf/Validate/TwitterScreenName.php';

class Recordshelf_Validate_TwitterScreenNameTest extends PHPUnit_Framework_TestCase
{
    protected $_validator;
    
    public function setUp()
    {
        $this->_validator = new Recordshelf_Validate_TwitterScreenName();
    }
    public function testTooLongScreenNameResultsInFalse()
    {
        $this->assertFalse($this->_validator->isValid('monster_screen_name'));
    }
    public function testNonExistentScreenNameResultsInFalse()
    {
        $this->assertFalse($this->_validator->isValid('mmm_unknowner'));
    }
    public function testInvalidCharactersInScreenNameResultsInFalse()
    {
        $this->assertFalse($this->_validator->isValid('@mmm_unknown#'));
    }
    public function testExistingAndValidScreenNameResultsInTrue()
    {
        $this->assertTrue($this->_validator->isValid('raphaelstolt'));
    }
}