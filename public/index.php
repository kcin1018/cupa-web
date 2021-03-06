<?php
// convert url to remove www
if(strpos($_SERVER['HTTP_HOST'], 'www.cincyultimate.org') !== false) {
    header('Location: http://' . str_replace('www.cincyultimate.org', 'cincyultimate.org', $_SERVER['HTTP_HOST']) . $_SERVER['REQUEST_URI']);
}

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

if(APPLICATION_ENV == 'production') {
    // Define path to application directory
    defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../cupaweb/application'));

    define('APPLICATION_WEBROOT', realpath(dirname(__FILE__)));
} else {
    // Define path to application directory
    defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

    define('APPLICATION_WEBROOT', APPLICATION_PATH . '/../public');
}
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap('session')
            ->bootstrap()
            ->run();
