<?php
/**
 * THE MULTI-PURPOSE WEBSITE BOILERPLATE WITH TWIG SUPPORT
 * Boilerplate Module :: STARTER
 *
 * This initialize the entire Boilerplate System and auto-instantiates the main
 * siteClass as "$app". May auto-instantiates Twig Template System as "$twig"
 * (if Twig is installed and enabled).
 *
 * - Sets the following constants: DS, APP, ROOT, DEBUG, CONFIG, AUTORENDER,
 *   ENVIRONMENT, COMPOSER, TWIG, MAINTENANCE, and BOILERPLATE.
 *
 * - Sets the following global variables: $env, $app, and $twig.
 *
 * - Checks for the existence of the following temporary variables: $_set,
 *   $_app, $DEBUG, $CONFIG, and $AUTORENDER.
 *
 * For complete description and further information, @see MANUAL at docs/TOC.md.
 *
 * @since      July, 2019.
 * @category   Class
 * @version    2.0.2-beta 1
 *
 * @author     Julio Marchi <contact@juliomarchi.com> | Twitter: @MrMarchi
 * @copyright  See Full Header Comment Blocks at "dist/index.php"
 * @license    https://opensource.org/licenses/MIT
 */

/**
 * Quick HELPER function to fix slashes in crazy paths
 */
function fixSlashes($str) {
    return preg_replace('~/+~', '/', str_replace("\\", "/", $str) );
}

 /**
 * Set Default variables for customization
 */
$DEFAULT_ROOT      = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI'];
$DEFAULT_ROOT      = fixSlashes( (is_dir($DEFAULT_ROOT)) ? $DEFAULT_ROOT : dirname(dirname(realpath(__DIR__))) . DIRECTORY_SEPARATOR );
/** If ROOT FOLDER is not valid, fail and terminate! */
if (!is_dir($DEFAULT_ROOT)) die('<b>ABORTING!</b> ROOT Folder is invalid or misconfigured. Please check Boilerplate documentation.');

$DEFAULT_APP        = $DEFAULT_ROOT . DIRECTORY_SEPARATOR . "app"      . DIRECTORY_SEPARATOR;
$DEFAULT_SET        = $DEFAULT_ROOT . DIRECTORY_SEPARATOR . "app"      . DIRECTORY_SEPARATOR;
$DEFAULT_VENDOR     = $DEFAULT_ROOT . DIRECTORY_SEPARATOR . "vendor"   . DIRECTORY_SEPARATOR;
$DEFAULT_TEMPLATE   = $DEFAULT_ROOT . DIRECTORY_SEPARATOR . "template" . DIRECTORY_SEPARATOR;
$DEFAULT_CONFIG     = $DEFAULT_ROOT . DIRECTORY_SEPARATOR . ".config" . DIRECTORY_SEPARATOR;
$DEFAULT_TEMPL_REL  = "template"    . DIRECTORY_SEPARATOR;
$DEFAULT_APP_FILE   = "app.php";
$DEFAULT_SET_FILE   = "set.php";
$DEFAULT_DEBUG      = false;
$DEFAULT_AUTORENDER = true;

/**
 * Check User Customizations and if valid use it, otherwise, use pre-defined defaults above.
 */
$_BOILERPLATE['location']['root']         = fixSlashes( (!empty($ROOT) && is_dir($ROOT))         ? $ROOT              : $DEFAULT_ROOT );
$_BOILERPLATE['location']['app']          = fixSlashes( (!empty($APP) && is_dir($APP))           ? $APP               : $DEFAULT_APP );
$_BOILERPLATE['location']['set']          = fixSlashes( (!empty($SET) && is_dir($SET))           ? $SET               : $DEFAULT_SET );
$_BOILERPLATE['location']['vendor']       = fixSlashes( (!empty($VENDOR) && is_dir($VENDOR))     ? $VENDOR            : $DEFAULT_VENDOR );
$_BOILERPLATE['location']['config']       = fixSlashes( (!empty($CONFIG) && is_dir($CONFIG))     ? $CONFIG            : $DEFAULT_CONFIG );
$_BOILERPLATE['location']['template']     = fixSlashes( (!empty($TEMPLATE) && is_dir($TEMPLATE)) ? $TEMPLATE          : $DEFAULT_TEMPLATE );
$_BOILERPLATE['location']['template_rel'] = fixSlashes( (!empty($TEMPLATE_RELATIVE))             ? $TEMPLATE_RELATIVE : $DEFAULT_TEMPL_REL );  // Relative Path for the Template's folder

$_BOILERPLATE['location']['app_file']     = (!empty($APP_FILE) && file_exists($APP . $APP_FILE)) ? $APP_FILE : "app.php";
$_BOILERPLATE['location']['set_file']     = (!empty($SET_FILE) && file_exists($SET . $SET_FILE)) ? $SET_FILE : "set.php";

$_BOILERPLATE['location']['boilerplate']  = fixSlashes( $_BOILERPLATE['location']['vendor'] . "boilerplate" . DIRECTORY_SEPARATOR );

/**
 * Build Primary Constants.
 * User can custom-define those by using the following disposable variables:
 */
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', $_BOILERPLATE['location']['root']);
define('CONFIG', $_BOILERPLATE['location']['config']);
define('APP', $_BOILERPLATE['location']['app'] . $_BOILERPLATE['location']['app_file']);
define('SET', $_BOILERPLATE['location']['set'] . $_BOILERPLATE['location']['set_file']);
define('DEBUG', isset($DEBUG) ?? $DEFAULT_DEBUG);
define('AUTORENDER', isset($AUTORENDER) ?? $DEFAULT_AUTORENDER);

/**
 * Load Custom Config set by APP
 */
if (file_exists(SET)) require_once SET;

/**
 * Load Core Modules
 */
require_once $_BOILERPLATE['location']['boilerplate'] . "boilerplate.class.php";
require_once $_BOILERPLATE['location']['boilerplate'] . "boilerplate.functions.php";
require_once $_BOILERPLATE['location']['boilerplate'] . "boilerplate.helpers.php";
require_once $_BOILERPLATE['location']['boilerplate'] . "boilerplate.db.php";

/**
 * Instantiate siteClass in the $app object
 * and set Config Data mirror in $_BOILERPLATE['config']
 */
$app = new siteClass(ROOT);

/**
 * Check defined ENVIRONMENT option from Config File
 * Use default ('TEST') if it is not defined or config file doesn't exit.
 */
$possibleEnvironment = (isset(siteClass::$config['environment']['current']) && !empty(siteClass::$config['environment']['current']))
                     ? strtoupper(siteClass::$config['environment']['current'])
                     : 'TEST';
define('ENVIRONMENT', in_array($possibleEnvironment, siteClass::$config['environment']['types'])
                     ? $possibleEnvironment
                     : 'TEST');

error_reporting( ((ENVIRONMENT == 'PRODUCTION') ? 0 : siteClass::$config['debug']['error_reporting']));
ini_set("error_reporting", ((ENVIRONMENT == 'PRODUCTION') ? 0 : siteClass::$config['debug']['error_reporting']));

/**
 * Initialize and load COMPOSER Objects
 */
if (isset(siteClass::$config['composer']) && siteClass::$config['composer']):


    if (file_exists($_BOILERPLATE['location']['vendor'] . "autoload.php")) {
        include_once $_BOILERPLATE['location']['vendor'] . "autoload.php";
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
    /** Set initialize Twig and set status as a result of the initialization */
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
 * Create the constant BOILERPLATE and set it as TRUE indicating the
 * Boilerplate Core has been loaded
 */
define('BOILERPLATE', true);

/**
 * Create an variable named '$_' that will act as an "alias"
 * to reference (pointer) to the $_BOILERPLATE Global Variable.
 */
$_ = &$_BOILERPLATE;

/**
 * The $_BOILERPLATE Global Variable houses a series of important variables and collected
 * created across the initialization of the Boilerplate System. AS the Boilerplate combines
 * a fair amount of multiple pieces of multiple projects, as hard as we work to keep things
 * organized, it is almost impossible to change all pieces of code to create and set
 * variables in a perfect order and harmony. There is why we combine all relevant data in
 * the $_BOILERPLATE Global Variable, and we do it by reference (pointers), using "&", so
 * we do not consume more RAM than we need from the server.
 *
 * Ultimately, if there is something relevant your code will need to function within the
 * Boilerplate, it can certainly be found in the $_BOILERPLATE Global Variable.
 *
 * P.S.: Sorry for some redundancy in few of the sub-arrays (yeah, we know about that!).
 */
$_BOILERPLATE['environment']           = &siteClass::$config['environment'];
$_BOILERPLATE['template']              = &siteClass::$template;
$_BOILERPLATE['access']                = &siteClass::$template['access'];
$_BOILERPLATE['path']                  = &siteClass::$template['path'];
$_BOILERPLATE['location']['root']      = &siteClass::$root;
$_BOILERPLATE['location']['drive']     = &siteClass::$drive;
$_BOILERPLATE['db']                    = &siteClass::$config['databases'];
$_BOILERPLATE['config']['maintenance'] = &siteClass::$config['maintenance'];
$_BOILERPLATE['config']['composer']    = &siteClass::$config['composer'];
$_BOILERPLATE['config']['twig']        = &siteClass::$config['twig'];
$_BOILERPLATE['config']['debug']       = &siteClass::$config['debug'];
$_BOILERPLATE['config']['cache']       = &siteClass::$config['cache'];
$_BOILERPLATE['config']['log']         = &siteClass::$config['log'];
$_BOILERPLATE['config']['timezone']    = &siteClass::$config['timezone'];
$_BOILERPLATE['config']['country']     = &siteClass::$config['country'];
$_BOILERPLATE['config']['language']    = &siteClass::$config['language'];
$_BOILERPLATE['config']['charset']     = &siteClass::$config['charset'];
$_BOILERPLATE['config']['extensions']  = &siteClass::$config['extensions'];
$_BOILERPLATE['CONSTANT']              = &get_defined_constants(true)['user'];
$_BOILERPLATE['settings']              = &siteClass::$config;
$_BOILERPLATE['app']                   = &$app;
$_BOILERPLATE['twig']                  = &$twig;

/**
 * And, in the spirit of preserving RAM and keeping the software "clean', we
 * disposable some of the variables we certainly don't need beyond this point.
 * Welcome to the most basic concept of "Garbage Collection".
 */
unset($DEFAULT_ROOT, $DEFAULT_APP, $DEFAULT_SET, $DEFAULT_VENDOR, $DEFAULT_TEMPLATE, $DEFAULT_CONFIG, $DEFAULT_TEMPL_REL, $DEFAULT_APP_FILE, $DEFAULT_SET_FILE, $DEFAULT_DEBUG, $DEFAULT_AUTORENDER);
unset($ROOT, $APP, $SET, $VENDOR, $CONFIG, $TEMPLATE, $TEMPLATE_RELATIVE, $APP_FILE, $SET_FILE);
unset($possibleEnvironment, $_set, $_app);

/**
 * Load the APP
 */
if (file_exists(APP)) require_once APP;

/**
 * Load AUTORENDER Object
 */
if (defined('AUTORENDER') && AUTORENDER) require_once $_BOILERPLATE['location']['boilerplate'] . "boilerplate.autorender.php";
