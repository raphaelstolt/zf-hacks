<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'AllTests::main');
}

require_once 'Recordshelf/AllTests.php';

class AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Recordshelf');
        $suite->addTest(Recordshelf_AllTests::suite());
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'AllTests::main') {
    AllTests::main();
}