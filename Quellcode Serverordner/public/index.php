<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */

//error_reporting(E_ALL);

// Fehler fuer Produktivbetrieb abgeschaltet!
error_reporting(0);
@ini_set('display_errors', 0);


chdir(dirname(__DIR__));
defined('PUBLIC_PATH')
|| define('PUBLIC_PATH', realpath(dirname(__FILE__)));
// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
