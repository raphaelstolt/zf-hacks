<?php
/*
 * Include needed PHPUnit dependencies
 */
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Framework/IncompleteTestError.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/Runner/Version.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
require_once 'PHPUnit/Util/Filter.php';
/*
 * Determine the root directory of the framework playground.
 */
$recordshelfPath = realpath(dirname(dirname(__FILE__)));
set_include_path(get_include_path() . PATH_SEPARATOR . $recordshelfPath);
/*
 * Set error reporting to the level to which Zend Framework code must comply.
 */
error_reporting( E_ALL | E_STRICT );