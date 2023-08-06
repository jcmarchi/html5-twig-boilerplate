<?php
/**
 * THE MULTI-PURPOSE WEBSITE BOILERPLATE WITH TWIG SUPPORT
 * Boilerplate Module :: FUNCTIONS
 *
 * This file holds a collection of supporting functions for a multitude of usages.
 *
 * For complete description and further information, @see MANUAL at docs/TOC.md.
 *
 * @since      May, 2022.
 * @category   Supporting Functions
 * @version    2.4.2-beta 2
 *
 * @author     Julio Marchi <contact@juliomarchi.com> | Twitter: @MrMarchi
 * @copyright  See Full Header Comment Blocks at "dist/index.php"
 * @license    https://opensource.org/licenses/MIT
 */

namespace boilerplate;

class installer {

    /** Set Static Service Arrays */
    public static $APPLICATION = [],
                  $BOILERPLATE = [];

    /** Set Default variables for customization references */
    public  $DEFAULT_BOILERPLATE_OBJECT = "site",
            $DEFAULT_ROOT_FOLDER = "",
            $DEFAULT_APP_FOLDER = "",
            $DEFAULT_SET_FOLDER = "",
            $DEFAULT_CONFIG_FOLDER = "",
            $DEFAULT_VENDOR_FOLDER = "",
            $DEFAULT_TEMPLATE_FOLDER = "",
            $DEFAULT_TEMPLATE_RELATIVE = "template" . DIRECTORY_SEPARATOR,
            $DEFAULT_APP_FILE = "app.php",
            $DEFAULT_SET_FILE = "set.php",
            $DEFAULT_DEBUG = false,
            $DEFAULT_AUTORENDER = true;


    /**
     * Initialize Defaults
     */
    public function initializeDefaults()
    {
        /** If ROOT FOLDER is not valid, fail and terminate! */
        if (!is_dir(self::$APPLICATION['root'])) return false;
        /** If the site structure change, this must be adjusted in the caller. */
        $this->DEFAULT_ROOT_FOLDER = self::$APPLICATION['root'];
        /** Set Defaults */
        $this->DEFAULT_APP_FOLDER        = $this->DEFAULT_ROOT_FOLDER . DIRECTORY_SEPARATOR . "app"      . DIRECTORY_SEPARATOR;
        $this->DEFAULT_SET_FOLDER        = $this->DEFAULT_ROOT_FOLDER . DIRECTORY_SEPARATOR . "app"      . DIRECTORY_SEPARATOR;
        $this->DEFAULT_CONFIG_FOLDER     = $this->DEFAULT_ROOT_FOLDER . DIRECTORY_SEPARATOR . ".config"  . DIRECTORY_SEPARATOR;
        $this->DEFAULT_VENDOR_FOLDER     = $this->DEFAULT_ROOT_FOLDER . DIRECTORY_SEPARATOR . "vendor"   . DIRECTORY_SEPARATOR;
        $this->DEFAULT_TEMPLATE_FOLDER   = $this->DEFAULT_ROOT_FOLDER . DIRECTORY_SEPARATOR . "template" . DIRECTORY_SEPARATOR;
        /** Return true for convention only */
        return true;
    }


    /**
     * Initialize Environment Variables based on
     * possible developer's overrides (accessed
     * via globals). Check manual for more info.
     */
    public function initializeVariables()
    {
        /** Connect Globals */
        global $ROOT, $OBJECT, $APP_FOLDER, $SET_FOLDER, $VENDOR, $CONFIG, $TEMPLATE, $TEMPLATE_RELATIVE, $SET_FILE, $APP_FILE;

        /** Define the default Object Name for the Instantiated Boilerplate Object */
        self::$APPLICATION['object'] = $this->DEFAULT_BOILERPLATE_OBJECT;
        /** If the Global Variable $OBJECT is set, override the Instantiated Boilerplate Object name */
        if (isset($OBJECT)) self::$APPLICATION['object'] = $OBJECT;
        /** Validate the new Instantiated Boilerplate Object name. Return false if it fails */
        if (!is_string(self::$APPLICATION['object']) ||
             empty(self::$APPLICATION['object']) ||
            !$this::is_valid_object_name(self::$APPLICATION['object'])) return false;

        /**
         * Build Boilerplate Global Variable and check if "developer's customizations" exist.
         * Use it if it does. Otherwise, use pre-defined defaults.
         */
        self::$BOILERPLATE['location']['root']      = self::fixSlashes( (!empty($ROOT)       && is_dir($ROOT))       ? $ROOT       : $this->DEFAULT_ROOT_FOLDER);
        self::$BOILERPLATE['location']['app']       = self::fixSlashes( (!empty($APP_FOLDER) && is_dir($APP_FOLDER)) ? $APP_FOLDER : $this->DEFAULT_APP_FOLDER );
        self::$BOILERPLATE['location']['set']       = self::fixSlashes( (!empty($SET_FOLDER) && is_dir($SET_FOLDER)) ? $SET_FOLDER : $this->DEFAULT_SET_FOLDER );
        self::$BOILERPLATE['location']['vendor']    = self::fixSlashes( (!empty($VENDOR)     && is_dir($VENDOR))     ? $VENDOR     : $this->DEFAULT_VENDOR_FOLDER );
        self::$BOILERPLATE['location']['config']    = self::fixSlashes( (!empty($CONFIG)     && is_dir($CONFIG))     ? $CONFIG     : $this->DEFAULT_CONFIG_FOLDER);
        self::$BOILERPLATE['location']['template']  = self::fixSlashes( (!empty($TEMPLATE)   && is_dir($TEMPLATE))   ? $TEMPLATE   : $this->DEFAULT_TEMPLATE_FOLDER );
        /** Relative Path for the Template's folder */
        self::$BOILERPLATE['location']['templ_rel'] = self::fixSlashes( (!empty($TEMPLATE_RELATIVE)) ? $TEMPLATE_RELATIVE : $this->DEFAULT_TEMPLATE_RELATIVE );
        /** Temporary SETUP and APPLICATION files */
        self::$APPLICATION['set'] = (!empty($SET_FILE) && file_exists($SET_FOLDER . $SET_FILE)) ? $SET_FILE : "set.php";
        self::$APPLICATION['app'] = (!empty($APP_FILE) && file_exists($APP_FOLDER . $APP_FILE)) ? $APP_FILE : "app.php";
        /** The Boilerplate vendor's folder */
        self::$BOILERPLATE['location']['boilerplate'] = self::fixSlashes( self::$BOILERPLATE['location']['vendor'] . "boilerplate" . DIRECTORY_SEPARATOR );
        /** Load the HTML5—Twig Boilerplate Signature */
        $file = new \SplFileObject( self::$APPLICATION['mbvfl'] . DIRECTORY_SEPARATOR . ".boilerplate", "r" );
        $file->setFlags(\SplFileObject::READ_CSV);
        foreach ($file as $row):
            list($key, $value) = $row;
            self::$BOILERPLATE['_'][$key] = $value;
        endforeach;

        /** Return true if all is OK */
        return true;
    }


    /**
     * The BOILERPLATE houses a series of important variables and collections created
     * during the initialization of the System and based on many factors, including
     * developers customizations. However, the BOILERPLATE itself is a culmination of
     * the experiences acquired from many projects through many years of professional
     * development. As a result, as hard as we work to keep things organized, it is
     * almost impossible to change all pieces of code to create a single variable set
     * in perfect order and harmony.
     *
     * There is why we have this "extra step" (yeah, you can call it hack, or lazy coding)
     * that combines all relevant data in a single Array, the 'boilerplate::$core'.
     *
     * The we link it by reference (the PHP pointer using "&") to make it accessible to
     * the global scope requiring less writing and absolutely no additional RAM.
     *
     * Ultimately, if there is something relevant a code based on BOILERPLATE needs, it
     * will certainly be found in the $_ or $_BOILERPLATE Global Variables, onr in the
     * boilerplate::$core Static Array.
     *
     * P.S.: Yeah, sorry for a few redundancies in some of the sub-arrays (we are aware
     *       of it, but we strongly believe you and your application can survive it!).
     */
    public function completeInstallation()
    {
        /**
         * Retrofit several variables in a more orderly manner.
         * This same Array, plus the contents of $_SESSION and $_SERVER,
         * will be pushed into the Browser Console when ENVIRONMENT is NOT
         * set to Production, which will be invaluable for your day-to-day
         * coding needs. You are welcome!
         */
        boilerplate::$core = array_merge(boilerplate::$core, boilerplate::$config);
        boilerplate::$core['settings']['twig']['environment'] = &boilerplate::$template['environment'];
        boilerplate::$core['environment']       = &boilerplate::$config['settings']['environment'];
        boilerplate::$core['environment']       = &boilerplate::$config['settings']['environment'];
        boilerplate::$core['access']            = &boilerplate::$template['access'];
        boilerplate::$core['path']              = &boilerplate::$template['path'];
        boilerplate::$core['location']['root']  = &boilerplate::$root;
        boilerplate::$core['location']['cache'] =  boilerplate::$root . boilerplate::$config['settings']['cache']['folder'] . DS;
        boilerplate::$core['location']['drive'] = &boilerplate::$drive;
        boilerplate::$core['CONSTANTS']         = &get_defined_constants(true)['user'];

        /** Return true for convention only */
        return true;
    }


    /**
     * This Method is used to display core error messages and terminate the process
     * when errors are critical. Not an error treatment method for the application.
     * It is only suitable for the INSTALLER and the INSTALLATION PROCESS.
     *
     * @param  integer  $err  The Error Number referent to the internal Message Array $ERR_MSG.
     * @param  boolean  $die  TRUE terminate the program execution after displaying the message,
     *                        FALSE display the message with terminate the program execution.
     *                        Default = TRUE.
     *
     */
    public static function error($err, $die = true)
    {
        $ERR_MSG = [
            0 => 'ROOT Folder is invalid or misconfigured',
            1 => '$DEFAULT_BOILERPLATE_OBJECT is invalid or empty. It must be a String with a valid PHP variable name',
            2 => 'BOILERPLATE environment not initialized',
            3 => 'COMPOSER not initialized',
            4 => 'When everything fails, FALLBACK pages are rendered',
            5 => 'Unrecoverable condition encountered',
            6 => 'Undefined',
            7 => 'Undefined',
            8 => 'Undefined',
            9 => 'Undefined'
         ];
        $era = $die ? ' :: ABORTING' : '';
        $MSG = '<b>ERROR 400' . $err . $era .'!</b><br>' . $ERR_MSG[$err] . '.<br><i>Please check Boilerplate documentation for more information about this error.</i>';
        if ($die) die($MSG);
        echo $MSG;
    }


    /**
     * Quick HELPER function to fix slashes in crazy or misformed paths.
     *
     * This function will replace backslashes (\) by normal slashes (/),
     * then it will search for duplicates (in any quantity) and make it
     * only one.
     *
     * We use "/" instead of DIRECTORY_SEPARATOR because the current standard
     * for slashes across systems is for web address and path separation is
     * now widely accepted as slash (/), even in system that previously rely
     * on back-slashes (\). Besides, as for PHP, it can use slashes for local
     * paths AND for web addresses without distinction independent of platforms
     * (Linux/Windows/Mac), so the DIRECTORY_SEPARATOR has become irrelevant.
     *
     * Note: For convenience, this function will be WRAPPED in the trait.functions.php file.
     *
     * @param   string  $str  The String with possible "slashes" to be corrected.
     * @return  string  With correct set of "slashes" to satisfy web-addresses and
     *                  local paths.
     */
    public static function fixSlashes($str) {
        return preg_replace('~/+~', '/', str_replace("\\", "/", $str) );
    }


    /**
     * Simply check if the given string can be used to name a property or a function.
     * The evaluation is made based on PHP naming convention standards found at
     * https://www.php.net/manual/en/language.variables.basics.php
     *
     * Note: For convenience, this function will be WRAPPED in the trait.validators.php file.
     *
     * @param   string  $name  The string to be evaluated.
     * @return  mixed   TRUE if the string can be used as a name for a variable or a function,
     *                  FALSE otherwise.
     *                  NULL if variable is NOT a string.
     */
    public static function is_valid_object_name($name = false)
    {
        if (!$name) return null;
        if (!is_string($name)) return false;
        return preg_match('/^([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)$/', $name);
    }
}