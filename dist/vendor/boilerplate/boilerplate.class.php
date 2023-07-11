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
 * This CLASS is auto-instantiated by "boilerplate.start.php" as "$app".
 *
 * @since      March, 2019.
 * @category   Class
 * @version    1.8.0-beta 5
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
 *    $app = new siteClass(__DIR__);
 *
 */
class siteClass {

    /** Private Datasets */
    private static $mainClassInitialized = false;

    /** Public Datasets */
    public static $drive    = null,
                  $base     = null,
                  $root     = null,
                  $url      = null,
                  $uri      = null,
                  $env      = [],
                  $config   = [],
                  $template = [],
                  $twig     = null;


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

        global $env;

        /**
         * If already Initialize, just quit (no double initialization allowed)
         */
        if (self::$mainClassInitialized) return;

        /**
         * Set mainClass as Initialized
         */
        self::$mainClassInitialized = true;
        self::$env = $env;

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
                   new \RecursiveDirectoryIterator(self::$root . DS . CONFIG . DS)
                 ), '/^((.+?)(\.)(json))$/i'
               );
        foreach ($RDI as $CFG) self::$config = array_merge(self::$config, json_decode(file_get_contents($CFG), true));

        /**
         * Identify parts of the URL/URI to compose reference links for the template
         */

        /** Exposes custom Server PORT */
        $serverport = ($_SERVER["SERVER_PORT"] == 80 || $_SERVER["SERVER_PORT"] == 443) ? "" : ":" . $_SERVER["SERVER_PORT"];
        /** Build relative based (URI) on website root location (not URL page location). Correct slashes and clean double slashes. */
        $pathinside = str_replace('\\', "/", str_replace(self::$base, "", self::$root)) . "/";
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
          'cache'               => self::$config['cache']['enabled'] ? self::$root . DS . self::$config['cache']['folder'] : false,
          'debug'               => self::$config['debug']['enabled'],
          'charset'             => (!empty(self::$config['charset'])) ? self::$config['charset'] : "UTF-8", // Default = 'UTF-8'
          'base_template_class' => 'Twig_Template',   // Default = 'Twig_Template'
          'strict_variables'    => false,             // Default = false
          'autoescape'          => 'html',            // Default = 'html'
          'auto_reload'         => null               // Default = null
        );

        /**
         * Set reference variables for templates in regards to important resources
         */
        $path = self::$config['twig']['fullpath'] ? self::$url . "/" : self::$uri;
        self::$template['vars'] = [
            'url'       => &self::$url,
            'uri'       => &self::$uri,
            'assets'    => $path . "assets",
            'js'        => $path . "assets/" . 'js',
            'css'       => $path . "assets/" . 'css',
            'img'       => $path . "assets/" . 'img',
            'font'      => $path . "assets/" . 'font',
            'audio'     => $path . "assets/" . 'audio',
            'video'     => $path . "assets/" . 'video',
            'plugins'   => $path . "assets/" . 'plugins',
            'resources' => $path . "assets/" . 'resources',
            'config'    => &self::$config
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
        if ($templateName && file_exists(self::$env['path']['template'] . $templateName)):
            $direct   = self::$env['path']['template'] . $templateName;
            $relative = self::$env['path']['template_rel'] . $templateName;
        else:
            $direct   = self::$env['path']['template'] . self::$config['twig']['template'] . DS;
            $relative = self::$env['path']['template_rel'] . self::$config['twig']['template'] . DS;
        endif;

        /**
         * Save template path information
         */
        self::$template['path'] = [
            'local' => [
                'direct'   => $direct,
                'relative' => $relative
            ],
            'web' => [
            'domain' => $_SERVER['SERVER_NAME'],
            'url'    => &self::$url,
            'uri'    => &self::$uri
            ]
        ];

        /**
         * Check if Twig is physically installed.
         * Terminate and return FALSR if not.
         */
        if (!is_dir(self::$env['path']['vendor'] . "twig")) return false;

        /**
         * Initialize (or overrride) Twig Template Core.
         * Based on NEW TWIG 3.0.
         */
        self::$twig = new \Twig\Environment(
                      new \Twig\Loader\FilesystemLoader( self::$template['path']['local']['direct'] ), self::$template['environment']
                    );

        /**
         * Return TRUE to inwdicate Twig is installed and set
         */
        return true;
    }


}

?>