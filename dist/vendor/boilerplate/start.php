<?php
/**
 * THE MULTI-PURPOSE WEBSITE BOILERPLATE WITH TWIG SUPPORT
 * Boilerplate Module :: STARTER
 *
 * This initialize the entire Boilerplate System and auto-instantiates the main
 * boilerplate as "$site". May auto-instantiates Twig Template System as "$twig"
 * (if Twig is installed and enabled).
 *
 * - Sets the following constants: DS, APP, ROOT, DEBUG, CONFIG, AUTORENDER,
 *   ENVIRONMENT, COMPOSER, TWIG, MAINTENANCE, and BOILERPLATE.
 *
 * - Sets the following global variables: $env, $site, and $twig.
 *
 * - Checks for the existence of the following temporary variables: $_set,
 *   $site, $DEBUG, $CONFIG, and $AUTORENDER.
 *
 * For complete description and further information, @see MANUAL at docs/TOC.md.
 *
 * @since      July, 2019.
 * @category   Class
 * @version    3.0.4-beta 2
 *
 * @author     Julio Marchi <contact@juliomarchi.com> | Twitter: @MrMarchi
 * @copyright  See Full Header Comment Blocks at "dist/index.php"
 * @license    https://opensource.org/licenses/MIT
 */

namespace boilerplate;

/**
 * Load and Instantiate boilerplate Installer Class
 */
require_once __DIR__ . DIRECTORY_SEPARATOR . "classes". DIRECTORY_SEPARATOR . "class.installer.php";
$installer = new installer;

/**
 * Execute Installer first steps
 */
$installer::$APPLICATION['start'] = microtime(true);
$installer::$APPLICATION['mbvfl'] = __DIR__; // "mbvfl" stands for "MOODFIRED Boilerplate VENDOR Folder Location" :P
$installer::$APPLICATION['root']  = $installer::fixSlashes(dirname(dirname(realpath(__DIR__))) . DIRECTORY_SEPARATOR);
if ( ! $installer->initializeDefaults()  ) $installer::error(0);
if ( ! $installer->initializeVariables() ) $installer::error(1);

/**
 * Load boilerplate Traits
 */
require_once __DIR__ . DIRECTORY_SEPARATOR . "traits"  . DIRECTORY_SEPARATOR . "trait.database.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . "traits"  . DIRECTORY_SEPARATOR . "trait.functions.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . "traits"  . DIRECTORY_SEPARATOR . "trait.helpers.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . "traits"  . DIRECTORY_SEPARATOR . "trait.validators.php";

/**
 * Build System Constants
 */
define('DS', "/"); // As for the rest of the application, we rather opt for the more up-to-date
                   // and widely accepted global standard "/" instead of DIRECTORY_SEPARATOR.
define('APP'        , $installer::$BOILERPLATE['location']['app']);
define('SET'        , $installer::$BOILERPLATE['location']['set']);
define('ROOT'       , $installer::$BOILERPLATE['location']['root']);
define('CONFIG'     , $installer::$BOILERPLATE['location']['config']);
define('VENDOR'     , $installer::$BOILERPLATE['location']['vendor']);
define('TEMPLATE'   , $installer::$BOILERPLATE['location']['template']);
define('SETUP'      , $installer::$BOILERPLATE['location']['set'] . $installer::$APPLICATION['set']);
define('APPLICATION', $installer::$BOILERPLATE['location']['app'] . $installer::$APPLICATION['app']);
define('AUTORENDER' , isset($AUTORENDER) ?? $DEFAULT_AUTORENDER);

/**
 * Load boilerplate Class
 */
require_once __DIR__ . DS . "classes" . DS . "class.boilerplate.php";

/**
 * Capture the default name specified for the Boilerplate Object and
 * Instantiate it using the great 'variable variable' offered by PHP.
 *
 * Yeah, we know it is a "HACK"! But, it also a valid PHP Language Constructor,
 * and it works preventing initialization errors and avoiding more complex code!
 */
${$installer->DEFAULT_BOILERPLATE_OBJECT} = new boilerplate($installer::$BOILERPLATE);

/**
 * Now that we have loaded all the modules, instantiated the system boilerplate
 * items, parsed all entries, and set all the constants we need, it is time to
 * allow developers to load their own Custom Config Settings, which they can
 * define by setting the the variablesLoad Custom Config set by specifying the
 * path in $DEFAULT_SET_FOLDER and the file in $DEFAULT_SET_FILE, which are
 * combined into the constant SETUP based on the variable evaluation and
 * folder/file existence. It is NOT required.
 */
if (file_exists(SETUP)) require_once SETUP;

/**
 * Check defined ENVIRONMENT option from Config File
 * Use default ('TEST') if it is not defined or config file doesn't exit.
 *
 * Confused about our "code indentation"? Look closer and notice how easier
 * to interpret it whe when you understand how the logic is applied. :)
 */
$possible_environment = (isset(boilerplate::$config['settings']['environment']['current']) && !empty(boilerplate::$config['settings']['environment']['current']))
                      ? strtoupper(boilerplate::$config['settings']['environment']['current'])
                      : 'TEST';
define('ENVIRONMENT', in_array($possible_environment, boilerplate::$config['settings']['environment']['types'])
                      ? $possible_environment
                      : 'TEST');

/**
 * Set Error Reporting based on Environment and respecting the Configuration File.
 * For PRODUCTION environment, all error reporting gets fully disabled.
 */
error_reporting( ((ENVIRONMENT == 'PRODUCTION') ? 0 : boilerplate::$config['settings']['debug']['error_reporting']));
ini_set("error_reporting", ((ENVIRONMENT == 'PRODUCTION') ? 0 : boilerplate::$config['settings']['debug']['error_reporting']));

/**
 * Define Default DEBUG Status based on pre-defined $DEBUG variable.
 * If ENVIRONMENT == 'PRODUCTION' debug is disabled by default,
 * otherwise, $DEBUG will have precedence over the configuration file
 * and the default value.
 */
if (ENVIRONMENT == 'PRODUCTION'):
    boilerplate::$config['settings']['debug']['enabled'] = $DEBUG = false;

else:
    /**
     * First check if debug has been set in the configuration file.
     * If YES, remember it. Otherwise, remember the value of the $installer->DEFAULT_DEBUG
     */
    $DEBUG_SYS = isset(boilerplate::$config['settings']['debug']['enabled'])
               ? boilerplate::$config['settings']['debug']['enabled']
               : $installer->DEFAULT_DEBUG;
    /**
     * Then, check if the variable $DEBUG has been set.
     * If NOT, use it the previous identified DEBUG flag, otherwise, override it.
     */
    boilerplate::$config['settings']['debug']['enabled'] = isset($DEBUG) ? $DEBUG : $DEBUG_SYS;
endif;

/**
 * Initialize and load COMPOSER Objects
 */
if (isset(boilerplate::$config['settings']['composer']) && boilerplate::$config['settings']['composer']):
    /**
     * It is "crazy" but PHP has a BUG in its language model related to
     * "Alternative Syntax for control structures" where a nested if:->else:->endif;
     * inside a pre-existing "Alternative Syntax" "if:" fails validating the nested "else:".
     * The solution is to mix "Traditional Syntax" with "Alternative Syntax" by having the
     * nested if{}else{} written using "Traditional Syntax".
     *
     * Yeah, we will report this bug, which we identified and validated since version 7,
     * but it is now 8+ and yet the bug remains...
     */
    if (file_exists($installer::$BOILERPLATE['location']['vendor'] . "autoload.php")) {
        include_once $installer::$BOILERPLATE['location']['vendor'] . "autoload.php";
        define('COMPOSER', true);
    } else {
        \boilerplate\installer::error(3, false);
        define('COMPOSER', false);
    }
endif;
/** Set COMPOSER constant */
If (!defined('COMPOSER')) define('COMPOSER', false);

/**
 * Initialize Twig Template System
 * Will set the constant TWIG to TRUE if Twig is installed, or as FALSE otherwise.
 * If Twig is present and successfully initialized, also point $twig to the Twig Object.
 */
if (isset(boilerplate::$config['settings']['twig']['install']) && boilerplate::$config['settings']['twig']['install'] && COMPOSER):
    /** Set initialize Twig and set status as a result of the initialization */
    define('TWIG', ${boilerplate::$me}->initializeTemplate() );
    /** Point $twig to the Twig Object (pure convenience) */
    $twig = &boilerplate::$twig;

else:
    /** Set Twig status */
    define('TWIG', false);

endif;

/**
 * Define Maintenance Mode
 */
if (isset(boilerplate::$config['settings']['maintenance'])):
    define('MAINTENANCE', boilerplate::$config['settings']['maintenance']);

else:
    define('MAINTENANCE', false);

endif;

/**
 * Create the constant BOILERPLATE and set it as TRUE indicating the
 * Boilerplate boilerplate has been loaded
 */
define('BOILERPLATE', true);

/**
 * Complete Installation of the Boilerplate
 */
$installer->completeInstallation();

/**
 * Create two Global Variables: '$_' and '$_BOILERPLATE'.
 * Both are set to as "aliases", via reference (pointer),
 * for the contents of the (Static) boilerplate::$core Array Data.
 */
$_ = $_BOILERPLATE = &boilerplate::$core;

/**
 * And, in the spirit of preserving RAM and keeping the software "clean', we
 * disposable some of the variables we certainly don't need beyond this point.
 * Welcome to the most basic concept of "Garbage Collection".
 */
unset($installer, $ROOT, $OBJECT, $VENDOR, $CONFIG, $DEBUG, $DEBUG_SYS, $TEMPLATE, $TEMPLATE_RELATIVE, $APP_FILE, $APP_FOLDER, $SET_FILE, $SET_FOLDER);

/**
 * Time to load the APP. The path is defined in $DEFAULT_SET_FOLDER and the
 * name defined in $DEFAULT_SET_FILE, which are combined into the constant
 * APPLICATION.
 * Required.
 */
if (file_exists(APPLICATION)) require_once APPLICATION;

/**
 * Load AUTORENDER Object
 */
if (defined('AUTORENDER') && AUTORENDER) require_once $_BOILERPLATE['location']['boilerplate'] . "boilerplate.autorender.php";

/**
 * Check if basic debugger PHP file is still in its location and load it if it is.
 */
if (is_file($_['location']['app'] . "helpers" . DS . "debugger.php"))
   require_once $_['location']['app'] . "helpers" . DS . "debugger.php";
