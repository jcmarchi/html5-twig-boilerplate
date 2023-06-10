<?php

/**
 * Build Primary Constants.
 * User can custom-define those by using the following disposable variables:
 */
define('DS', DIRECTORY_SEPARATOR);
define('APP', isset($APP) ? $APP : "app");
define('ROOT', isset($ROOT) ? $ROOT : dirname(dirname(realpath(__DIR__))));

/**
 * Define PATHS
 */
$env['environment']          = ['DEV', 'TEST', 'STAGE', 'PRODUCTION'];
$env['error_reporting']      = E_ALL;

$env['path']['app']          = ROOT . DS . APP . DS;
$env['path']['vendor']       = ROOT . DS . "vendor" . DS ;
$env['path']['boilerplate']  = $env['path']['vendor'] . "boilerplate" . DS;
$env['path']['template']     = ROOT . DS . "template" . DS ;
$env['path']['template_rel'] = "template" . DS ;

/**
 * Load Custom Config set by APP
 */
$_set = isset($_set) ? $_set : "set.php";
if (file_exists($env['path']['app'] . $_set)) require_once $env['path']['app'] . $_set;

/**
 * Build Secondary Constants.
 * User can custom-define those by using the following disposable variables:
 */
define('DEBUG', isset($DEBUG) ? $DEBUG : false);
define('CONFIG', isset($CONFIG) ? $CONFIG : ".config");
define('AUTORENDER', isset($AUTORENDER) ? $AUTORENDER : true);

/**
 * Load Core Modules
 */
require_once $env['path']['boilerplate'] . "boilerplate.class.php";
require_once $env['path']['boilerplate'] . "boilerplate.functions.php";
require_once $env['path']['boilerplate'] . "boilerplate.helpers.php";
require_once $env['path']['boilerplate'] . "boilerplate.db.helpers.php";

/**
 * Instantiate siteClass in the $app object
 */
$app = new siteClass(ROOT);

/**
 * Check defined ENVIRONMENT option from Config File
 * Use default ('TEST') if it is not defined or config file doesn't exit.
 */
$possibleEnvironment = (isset(siteClass::$config['environment']) && !empty(siteClass::$config['environment']))
                     ? strtoupper(siteClass::$config['environment'])
                     : 'TEST';
define('ENVIRONMENT', in_array($possibleEnvironment, $env['environment'])
                     ? $possibleEnvironment
                     : 'TEST');

error_reporting( ((ENVIRONMENT == 'PRODUCTION') ? 0 : $env['error_reporting']));
ini_set("error_reporting", ((ENVIRONMENT == 'PRODUCTION') ? 0 : $env['error_reporting']));

/**
 * Initialize and load COMPOSER Objects
 */
if (isset(siteClass::$config['composer']) && siteClass::$config['composer']):


    if (file_exists($env['path']['vendor'] . "autoload.php")) {
        include_once $env['path']['vendor'] . "autoload.php";
        define('COMPOSER', true);
    } else {
        print "<b>Error 4001</b>: COMPOSER not initialized.<br>";
        define('COMPOSER', false);
    }

endif;

/**
 * Initialize Twig Template System
 * Will set the constant TWIG to TRUE if Twig is installed, or as FALSE otherwise.
 * If Twig is present and successfully initialized, also point $twig to the Twig Object.
 */
if (isset(siteClass::$config['twig']['install']) && siteClass::$config['twig']['install'] && COMPOSER):
    /** Set initilize Twig and set status as a result of the initialization */
    define('TWIG', $app::initializeTemplate() );
    /** Point $twig to the Twig Object (pure convenience) */
    $twig = &siteClass::$twig;

else:
    /** Set Twig status */
    define('TWIG', false);

endif;

/**
 * Define Maintenance Mode
 */
if (isset(siteClass::$config['maintenance'])):
    define('MAINTENANCE', siteClass::$config['maintenance']);

else:
    define('MAINTENANCE', false);

endif;

/**
 * Set flag indicating site has been loaded
 */
define('BOILERPLATE', true);

/**
 * Load APP
 */
$_app = isset($_app) ? $_app : "app.php";
if (file_exists($env['path']['app'] . $_app)) require_once $env['path']['app'] . $_app;

/**
 * Clean Disposable Variables
 */
unset($APP, $ROOT, $DEBUG, $CONFIG, $AUTORENDER, $possibleEnvironment, $_set, $_app);

/**
 * Load AUTORENDER Object
 */
if (defined('AUTORENDER') && AUTORENDER) require_once $env['path']['boilerplate'] . "boilerplate.autorender.php";
