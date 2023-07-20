<?php
/**
 * THE MULTI-PURPOSE WEBSITE BOILERPLATE WITH TWIG SUPPORT
 * Boilerplate Module :: STARTER
 *
 * This initialize the entire Boilerplate System and auto-instantiates the main
 * siteClass as "$_APP". May auto-instantiates Twig Template System as "$_TWIG"
 * (if Twig is installed and enabled).
 *
 * - Sets the following constants: DS, APP, ROOT, DEBUG, CONFIG, AUTORENDER,
 *   ENVIRONMENT, COMPOSER, TWIG, MAINTENANCE, and BOILERPLATE.
 *
 * - Sets the following global variables: $env, $_APP, and $_TWIG.
 *
 * - Checks for the existence of the following temporary variables: $_set,
 *   $_app, $DEBUG, $CONFIG, and $AUTORENDER.
 *
 * For complete description and further information, @see MANUAL at docs/TOC.md.
 *
 * @since      July, 2019.
 * @category   Class
 * @version    2.0.4-beta 2
 *
 * @author     Julio Marchi <contact@juliomarchi.com> | Twitter: @MrMarchi
 * @copyright  See Full Header Comment Blocks at "dist/index.php"
 * @license    https://opensource.org/licenses/MIT
 */

/**
 * Load Core Modules
 */
require_once __DIR__ . DIRECTORY_SEPARATOR . "boilerplate.class.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . "boilerplate.functions.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . "boilerplate.helpers.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . "boilerplate.db.php";

/**
 * Set Default variables for customization
 */
// $DEFAULT_ROOT_FOLDER = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI'];
// $DEFAULT_ROOT_FOLDER = fixSlashes( (is_dir($DEFAULT_ROOT_FOLDER)) ? $DEFAULT_ROOT_FOLDER: dirname(dirname(realpath(__DIR__))) . DIRECTORY_SEPARATOR );
$DEFAULT_ROOT_FOLDER = fixSlashes(dirname(dirname(realpath(__DIR__))) . DIRECTORY_SEPARATOR);
/** If ROOT FOLDER is not valid, fail and terminate! */
if (!is_dir($DEFAULT_ROOT_FOLDER)) die('<b>ABORTING!</b> ROOT Folder is invalid or misconfigured. Please check Boilerplate documentation.');

$DEFAULT_APP_FOLDER      = $DEFAULT_ROOT_FOLDER. DIRECTORY_SEPARATOR . "app"      . DIRECTORY_SEPARATOR;
$DEFAULT_SET_FOLDER      = $DEFAULT_ROOT_FOLDER. DIRECTORY_SEPARATOR . "app"      . DIRECTORY_SEPARATOR;
$DEFAULT_CONFIG_FOLDER   = $DEFAULT_ROOT_FOLDER. DIRECTORY_SEPARATOR . ".config"  . DIRECTORY_SEPARATOR;
$DEFAULT_VENDOR_FOLDER   = $DEFAULT_ROOT_FOLDER. DIRECTORY_SEPARATOR . "vendor"   . DIRECTORY_SEPARATOR;
$DEFAULT_TEMPLATE_FOLDER = $DEFAULT_ROOT_FOLDER. DIRECTORY_SEPARATOR . "template" . DIRECTORY_SEPARATOR;
$DEFAULT_TEMPL_REL       = "template" . DIRECTORY_SEPARATOR;
$DEFAULT_APP_FILE        = "app.php";
$DEFAULT_SET_FILE        = "set.php";
$DEFAULT_DEBUG           = false;
$DEFAULT_AUTORENDER      = true;

/**
 * Build Boilerplate Global Variable and check if "developer's customizations" exist.
 * Use it if it does. Otherwise, use pre-defined defaults.
 */
$_BOILERPLATE['location']['root']      = fixSlashes( (!empty($ROOT)       && is_dir($ROOT))       ? $ROOT       : $DEFAULT_ROOT_FOLDER);
$_BOILERPLATE['location']['app']       = fixSlashes( (!empty($APP_FOLDER) && is_dir($APP_FOLDER)) ? $APP_FOLDER : $DEFAULT_APP_FOLDER );
$_BOILERPLATE['location']['set']       = fixSlashes( (!empty($SET_FOLDER) && is_dir($SET_FOLDER)) ? $SET_FOLDER : $DEFAULT_SET_FOLDER );
$_BOILERPLATE['location']['vendor']    = fixSlashes( (!empty($VENDOR)     && is_dir($VENDOR))     ? $VENDOR     : $DEFAULT_VENDOR_FOLDER );
$_BOILERPLATE['location']['config']    = fixSlashes( (!empty($CONFIG)     && is_dir($CONFIG))     ? $CONFIG     : $DEFAULT_CONFIG_FOLDER);
$_BOILERPLATE['location']['template']  = fixSlashes( (!empty($TEMPLATE)   && is_dir($TEMPLATE))   ? $TEMPLATE   : $DEFAULT_TEMPLATE_FOLDER );
// Relative Path for the Template's folder
$_BOILERPLATE['location']['templ_rel'] = fixSlashes( (!empty($TEMPLATE_RELATIVE)) ? $TEMPLATE_RELATIVE : $DEFAULT_TEMPL_REL );
// Temporary SETUP and APPLICATION files
$setup_file = (!empty($SET_FILE) && file_exists($SET_FOLDER . $SET_FILE)) ? $SET_FILE : "set.php";
$application_file = (!empty($APP_FILE) && file_exists($APP_FOLDER . $APP_FILE)) ? $APP_FILE : "app.php";
// The Boilerplate vendor's folder
$_BOILERPLATE['location']['boilerplate'] = fixSlashes( $_BOILERPLATE['location']['vendor'] . "boilerplate" . DIRECTORY_SEPARATOR );

/**
 * Build Primary Constants.
 * User can custom-define those by using the following disposable variables:
 */
define('DS'         , "/");  // We rather use the most modern global standard "/" instead of DIRECTORY_SEPARATOR
define('APP'        , $_BOILERPLATE['location']['app']);
define('SET'        , $_BOILERPLATE['location']['set']);
define('ROOT'       , $_BOILERPLATE['location']['root']);
define('CONFIG'     , $_BOILERPLATE['location']['config']);
define('SETUP'      , $_BOILERPLATE['location']['set'] . $setup_file);
define('APPLICATION', $_BOILERPLATE['location']['app'] . $application_file);
define('AUTORENDER' , isset($AUTORENDER) ?? $DEFAULT_AUTORENDER);

/**
 * Instantiate siteClass in the $_APP object.
 */
$_APP = new siteClass(ROOT);

/**
 * Now that we have loaded all the modules, instantiated the system core items,
 * parsed all entries, and set all the constants we need, it is time to allow
 * developers to load their own Custom Config Settings, which they can define
 * by setting the the variablesLoad Custom Config set by specifying the path
 * in $DEFAULT_SET_FOLDER and the file in $DEFAULT_SET_FILE, which are combined
 * into the constant SETUP based on the variable evaluation and folder/file
 * existence. Not required.
 */
if (file_exists(SETUP)) require_once SETUP;

/**
 * Check defined ENVIRONMENT option from Config File
 * Use default ('TEST') if it is not defined or config file doesn't exit.
 */
$possible_environment = (isset(siteClass::$config['config']['environment']['current']) && !empty(siteClass::$config['config']['environment']['current']))
                     ? strtoupper(siteClass::$config['config']['environment']['current'])
                     : 'TEST';
define('ENVIRONMENT', in_array($possible_environment, siteClass::$config['config']['environment']['types'])
                     ? $possible_environment
                     : 'TEST');

error_reporting( ((ENVIRONMENT == 'PRODUCTION') ? 0 : siteClass::$config['config']['debug']['error_reporting']));
ini_set("error_reporting", ((ENVIRONMENT == 'PRODUCTION') ? 0 : siteClass::$config['config']['debug']['error_reporting']));

/**
 * Initialize and load COMPOSER Objects
 */
if (isset(siteClass::$config['config']['composer']) && siteClass::$config['config']['composer']):
    if (file_exists($_BOILERPLATE['location']['vendor'] . "autoload.php")) {
        include_once $_BOILERPLATE['location']['vendor'] . "autoload.php";
        define('COMPOSER', true);
    } else {
        print "<b>Error 4001</b>: COMPOSER not initialized.<br>";
        define('COMPOSER', false);
    }
endif;
If (!defined('COMPOSER')) define('COMPOSER', false);

/**
 * Initialize Twig Template System
 * Will set the constant TWIG to TRUE if Twig is installed, or as FALSE otherwise.
 * If Twig is present and successfully initialized, also point $_TWIG to the Twig Object.
 */
if (isset(siteClass::$config['config']['twig']['install']) && siteClass::$config['config']['twig']['install'] && COMPOSER):
    /** Set initialize Twig and set status as a result of the initialization */
    define('TWIG', $_APP::initializeTemplate() );
    /** Point $_TWIG to the Twig Object (pure convenience) */
    $_TWIG = &siteClass::$twig;

else:
    /** Set Twig status */
    define('TWIG', false);

endif;

/**
 * Define Maintenance Mode
 */
if (isset(siteClass::$config['config']['maintenance'])):
    define('MAINTENANCE', siteClass::$config['config']['maintenance']);

else:
    define('MAINTENANCE', false);

endif;

/**
 * Create the constant BOILERPLATE and set it as TRUE indicating the
 * Boilerplate Core has been loaded
 */
define('BOILERPLATE', true);

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
siteClass::$settings['environment']           = &siteClass::$config['config']['environment'];
siteClass::$settings['template']              = &siteClass::$template;
siteClass::$settings['access']                = &siteClass::$template['access'];
siteClass::$settings['path']                  = &siteClass::$template['path'];
siteClass::$settings['location']['root']      = &siteClass::$root;
siteClass::$settings['location']['drive']     = &siteClass::$drive;
siteClass::$settings['databases']             = &siteClass::$config['databases'];
siteClass::$settings['config']['maintenance'] = &siteClass::$config['config']['maintenance'];
siteClass::$settings['config']['composer']    = &siteClass::$config['config']['composer'];
siteClass::$settings['config']['twig']        = &siteClass::$config['config']['twig'];
siteClass::$settings['config']['debug']       = &siteClass::$config['config']['debug'];
siteClass::$settings['config']['cache']       = &siteClass::$config['config']['cache'];
siteClass::$settings['config']['log']         = &siteClass::$config['config']['log'];
siteClass::$settings['config']['timezone']    = &siteClass::$config['config']['timezone'];
siteClass::$settings['config']['country']     = &siteClass::$config['config']['country'];
siteClass::$settings['config']['language']    = &siteClass::$config['config']['language'];
siteClass::$settings['config']['charset']     = &siteClass::$config['config']['charset'];
siteClass::$settings['config']['index']       = &siteClass::$config['config']['index'];
siteClass::$settings['config']['extensions']  = &siteClass::$config['config']['extensions'];
siteClass::$settings['settings']              = &siteClass::$config;
siteClass::$settings['CONSTANTS']             = &get_defined_constants(true)['user'];

/**
 * Create a Global Variable '$_' and overwrite '$_BOILERPLATE'.
 * Both variables are set to be used as "aliases", via reference (pointer),
 * for the contents of the (Static) siteClass::$settings Array Data.
 */
$_ = $_BOILERPLATE = &siteClass::$settings;

/**
 * TODO: DEPRECATED references. To be removed: $app and $twig.
 * Create two additional Global Variables: '$app' and '$twig'.
 * Both variables are "aliases" and access the contents of
 * the $_APP and $_TWIG respectively via reference (pointer).
 */
// $app = &$_APP;
// $twig = &$_TWIG;

// insight($_);
// insight($_APP);
// insight($_TWIG);
// insight($_BOILERPLATE);
// insight(siteClass::$settings);
// die;

/**
 * And, in the spirit of preserving RAM and keeping the software "clean', we
 * disposable some of the variables we certainly don't need beyond this point.
 * Welcome to the most basic concept of "Garbage Collection".
 */
// Unset $DEFAULTS_*
unset($DEFAULT_ROOT_FOLDER, $DEFAULT_APP_FOLDER, $DEFAULT_SET_FOLDER, $DEFAULT_CONFIG_FOLDER, $DEFAULT_VENDOR_FOLDER);
unset($DEFAULT_TEMPLATE_FOLDER, $DEFAULT_TEMPL_REL, $DEFAULT_APP_FILE, $DEFAULT_SET_FILE, $DEFAULT_DEBUG, $DEFAULT_AUTORENDER);
// Unset Developer's Custom Variables
unset($ROOT, $APP, $SET, $VENDOR, $CONFIG, $TEMPLATE, $TEMPLATE_RELATIVE, $APP_FILE, $SET_FILE);
// Unset Temporary Variables
unset($possible_environment, $setup_file, $application_fie);

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
