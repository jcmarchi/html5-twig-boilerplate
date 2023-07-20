<?php
/**
 * THE MULTI-PURPOSE WEBSITE BOILERPLATE WITH TWIG SUPPORT
 * Boilerplate Module :: MAIN CLASS
 *
 * This Static Class Object holds the Boilerplate Core.
 *
 * Requires custom initialization (see below) and must receive the address
 * referent to the "root location" (physical path) of the website root folder
 * in the server filesystem.
 *
 * This CLASS is auto-instantiated by "boilerplate.start.php" as "$_APP".
 *
 * @since      March, 2019.
 * @category   Class
 * @version    1.8.1-beta 5
 *
 * @author     Julio Marchi <contact@juliomarchi.com> | Twitter: @MrMarchi
 * @copyright  See Full Header Comment Blocks at "dist/index.php"
 * @license    https://opensource.org/licenses/MIT
 */


/**
 * Initialize the system core object.
 * It holds all important System Variables and Methods
 * Can be used either as STATIC or INSTANTIATE.
 * Requires __DIR__ to be send as parameter. Examples:
 *
 * Static:
 *    siteClass::startSite(__DIR__);
 *
 * Instantiated:
 *    $_APP = new siteClass(__DIR__);
 *
 */
class siteClass {
    const BOILERPLATE = '1.0.1-beta 18';

    /** Private Datasets */
    private static $mainClassInitialized = false;

    /** Public Datasets */
    public static $settings    = [],
                  $config      = [],
                  $template    = [],
                  $drive       = null,
                  $base        = null,
                  $root        = null,
                  $url         = null,
                  $uri         = null,
                  $twig        = null;


    /**
     * Default self initializer method for instantiated Classes
     *
     * @param  String  $root  Expected to be the equivalent to __DIR__ from the
     *                        index.php file located in the root of the site.
     *
     * @return void
     */
    public function __construct($root) {
        self::startSite($root);
    }


    /**
     * This Method initializes the required SDA for the application to run
     *
     * @param  String  $root  Expected to be the equivalent to __DIR__ from the
     *                        index.php file located in the root of the site.
     *
     * @return void
     */
    public static function startSite($root) {

        global $_BOILERPLATE;

        /**
         * If already Initialize, just quit (no double initialization allowed)
         */
        if (self::$mainClassInitialized) return;

        /**
         * Set mainClass as Initialized
         */
        self::$mainClassInitialized = true;
        self::$settings = $_BOILERPLATE;

        /**
         * Define $roo and $base for direct and relative calls
         *
         * IMPORTANT:
         * The site ROOT must ALWAYS be based on the __DIR__
         * of the index.php file located in the root of the site.
         */
        self::$root = $root;
        self::$base = str_replace(array('/', '\\'), DS, $_SERVER['DOCUMENT_ROOT']);

        /**
         * Windows Protection for Drive Letters
         */
        if (defined('PHP_WINDOWS_VERSION_MAJOR')):
            self::$drive = explode(":", self::$root)[0] . ":";
            self::$root  = explode(":", self::$root)[1];
            self::$base  = rtrim(explode(":", self::$base)[1],'\\');
        endif;

        /**
         * Recursively loads all .json files from the /.config folder
         */
        $RDI = new \RegexIterator(
                 new \RecursiveIteratorIterator(
                   new \RecursiveDirectoryIterator(CONFIG)
                 ), '/^((.+?)(\.)(json))$/i'
              );
        $test = [];
        foreach ($RDI as $CFG) self::$config[pathinfo(basename($CFG), PATHINFO_FILENAME)] = json_decode(file_get_contents($CFG), true);

        /**
         * Identify parts of the URL/URI to compose reference links for the template
         */

        /** Exposes custom Server PORT */
        $serverport = ($_SERVER["SERVER_PORT"] == 80 || $_SERVER["SERVER_PORT"] == 443) ? "" : ":" . $_SERVER["SERVER_PORT"];
        /** Build relative based (URI) on website root location (not URL page location). Correct slashes and clean double slashes. */
        // $pathinside = str_replace('\\', "/", str_replace(self::$base, "", self::$root)) . "/";
        $pathinside = self::$root;
        self::$uri  = ($_SERVER["SERVER_PORT"] == 80 || $_SERVER["SERVER_PORT"] == 443) ? "/" . $pathinside : str_replace('//', '/', $pathinside);
        self::$uri  = self::$uri == "" ? '/' : self::$uri;
        /** Windows protection for empty base URI that result in "double-slashes". */
        self::$uri  = str_replace('//', '/', self::$uri);
        /** Build the full Web Location (URL) for site resources based on website root location (not URL page location). */
        self::$url  = rtrim("//" . str_replace('//', '/', $_SERVER['SERVER_NAME'] . $serverport . "/" . self::$uri), "/");
        /** In Windows, we must put back the drive in self::$root and self::$base. */
        if (defined('PHP_WINDOWS_VERSION_MAJOR')):
            self::$root = self::$drive . self::$root;
            self::$base = self::$drive . self::$base;
        endif;
    }


    /**
     * This Method initializes the Twig Template Environment System.
     * The Method automatically calls "setTemplate()" to complete the initialization.
     *
     * @param  String   $templateName  A string with the name of the template.
     *                                 The variable only passed on to "setTemplate()".
     *
     * @return Boolean  true   if file Twig is installed and initialization is complete
     *                  false  if file Twig is not exist installed ad initialization failed
     */
    public static function initializeTemplate($templateName = false) {

        /**
         * TWIG Environment Options defined in the site's configuration file.
         */
        self::$template['environment'] = array(
          'cache'               => fixSlashes(self::$config['config']['cache']['enabled'] ? self::$root . DS . self::$config['config']['cache']['folder'] : false),
          'debug'               => self::$config['config']['debug']['enabled'],
          'charset'             => (!empty(self::$config['config']['charset'])) ? self::$config['config']['charset'] : "UTF-8", // Default = 'UTF-8'
          'base_template_class' => 'Twig_Template',   // Default = 'Twig_Template'
          'strict_variables'    => false,             // Default = false
          'autoescape'          => 'html',            // Default = 'html'
          'auto_reload'         => null               // Default = null
        );

        /**
         * Set reference variables for templates in regards to important resources
         */
        $path = self::$config['config']['twig']['fullpath'] ? self::$url . "/" : self::$uri;
        self::$template['path'] = [
            'url'       => &self::$url,
            'uri'       => &self::$uri,
            'vendor'    => fixSlashes($path . "vendor"),
            'assets'    => fixSlashes($path . "assets"),
            'js'        => fixSlashes($path . "assets" . DS . 'js'),
            'css'       => fixSlashes($path . "assets" . DS . 'css'),
            'img'       => fixSlashes($path . "assets" . DS . 'img'),
            'font'      => fixSlashes($path . "assets" . DS . 'font'),
            'audio'     => fixSlashes($path . "assets" . DS . 'audio'),
            'video'     => fixSlashes($path . "assets" . DS . 'video'),
            'plugins'   => fixSlashes($path . "assets" . DS . 'plugins'),
            'resources' => fixSlashes($path . "assets" . DS . 'resources')
        ];

        /**
         * Call function to complete Twig implementation
         */
        return self::setTemplate($templateName);
    }


    /**
     * This Method sets the template system based on Twig
     *
     * @param  String   $templateName  A string with the name of the template.
     *
     * @return Boolean  true   if file Twig is installed and initalization is complete
     *                  false  if file Twig is not exist installed ad initialization failed
     */
    public static function setTemplate($templateName = false) {
        /**
         * Properly discover and set template physical path
         */
        if ($templateName && file_exists(self::$settings['location']['template'] . $templateName)):
            $direct   = fixSlashes(self::$settings['location']['template'] . $templateName);
            $relative = fixSlashes(self::$settings['location']['templ_rel'] . $templateName);
        else:
            $direct   = self::$settings['location']['template'] . self::$config['config']['twig']['template'] . DS;
            $relative = self::$settings['location']['templ_rel'] . self::$config['config']['twig']['template'] . DS;
        endif;

        /**
         * Save template path information
         */
        self::$template['access'] = [
            'local' => [
                'direct'   => $direct,
                'relative' => $relative
            ],
            'web' => [
                'domain' => $_SERVER['SERVER_NAME'],
                'url'    => &self::$url,
                'uri'    => &self::$uri
            ],
            'requested' => [
                'url'    => fixSlashes( str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI'] ) ),
                'index'  => &self::$config['config']['index']
            ]
        ];

        /**
         * Check if Twig is physically installed.
         * Terminate and return FALSE if not.
         */
        if (!is_dir(self::$settings['location']['vendor'] . "twig")) return false;

        /**
         * Initialize (or override) Twig Template Core.
         * Based on NEW TWIG 3.0.
         */
        self::$twig = new \Twig\Environment(
                        new \Twig\Loader\FilesystemLoader(
                            self::$template['access']['local']['direct']
                        ), self::$template['environment']
                    );

        /**
         * Return TRUE to indicate Twig is installed and set
         */
        return true;
    }

}

?>