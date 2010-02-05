<?php

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Recordshelf_AllTests::main');
}
require_once 'Recordshelf/Validate/TwitterScreenNameTest.php';
require_once 'Recordshelf/Filter/TwitterScreenNameTest.php';

class Recordshelf_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }
    
  /**
     * Regular suite
     *
     * All tests except those that require output buffering.
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Recordshelf');
        $suite->addTestSuite('Recordshelf_Validate_TwitterScreenNameTest');
        $suite->addTestSuite('Recordshelf_Filter_TwitterScreenNameTest');
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Recordshelf_AllTests::main') {
    Recordshelf_AllTests::main();
}
