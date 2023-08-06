<?php
/**
 * THE MULTI-PURPOSE WEBSITE BOILERPLATE WITH TWIG SUPPORT
 * Boilerplate Module :: MAIN CLASS
 *
 * This Static Class Object holds the Boilerplate boilerplate.
 *
 * Requires custom initialization (see below) and must receive the address
 * referent to the "root location" (physical path) of the website root folder
 * in the server filesystem.
 *
 * This CLASS is auto-instantiated by "boilerplate.start.php" as "$site".
 *
 * @since      March, 2019.
 * @category   Class
 * @version    1.8.1-beta 5
 *
 * @author     Julio Marchi <contact@juliomarchi.com> | Twitter: @MrMarchi
 * @copyright  See Full Header Comment Blocks at "dist/index.php"
 * @license    https://opensource.org/licenses/MIT
 */

namespace boilerplate;

class boilerplate {

    use validators, functions, database, helpers;

    /** Private Dataset */
    private static $mainClassInitialized = false;

    /** Public Static Datasets */
    public static $core     = [],
                  $config   = [],
                  $template = [],
                  $drive    = null,
                  $base     = null,
                  $root     = null,
                  $url      = null,
                  $uri      = null,
                  $twig     = false,
                  $me       = false;


    /**
     * Default self initializer method for instantiated Classes
     *
     * @param  string  $boilerplate  An Array with the initial data collection obtained by
     *                               the Installer and the Start file. It is a reference to
     *                               the public static \boilerplate\installer::$BOILERPLATE.
     */
    public function __construct($boilerplate)
    {
        /**
         * The '$me' string should contain the name of the
         * instantiable object. Of course we have other means
         * to retrieve it but, why make it complicated? :P
         */
        self::$me = installer::$APPLICATION['object'];
        self::initiateBoilerplate($boilerplate);
    }


    /**
     * This Method initializes the required SDA for the application to run
     *
     * @param  string  $boilerplate  An Array with the initial data collection obtained by
     *                               the Installer and the Start file. It is a reference to
     *                               the public static \boilerplate\installer::$BOILERPLATE.
     */
    private function initiateBoilerplate($boilerplate)
    {
        /**
         * If already Initialize, just quit (no double initialization allowed)
         */
        if (self::$mainClassInitialized) return;

        /**
         * Set mainClass as Initialized and save received data
         */
        self::$mainClassInitialized = true;
        self::$core = $boilerplate;

        /**
         * Define $root and $base for direct and relative calls,
         *
         * IMPORTANT:
         * The site ROOT must ALWAYS be based on the __DIR__
         * of the index.php file located in the root of the site.
         */
        self::$root = self::$core['location']['root'];
        self::$base = $this->fixSlashes( $_SERVER['DOCUMENT_ROOT'] );

        /**
         * Windows Protection for Drive Letters
         */
        if (defined('PHP_WINDOWS_VERSION_MAJOR')):
            self::$drive = explode(":", self::$root)[0] . ":";
            self::$root  = explode(":", self::$root)[1];
            self::$base  = rtrim(explode(":", self::$base)[1], DS);
        endif;

        /**
         * Save URL/URI parts
         */
        self::$core['location']['_root'] = self::$root;
        self::$core['location']['_base'] = self::$base;

        /**
         * Recursively loads all .json files from the /.config folder
         */
        $RDI = new \RegexIterator(
                 new \RecursiveIteratorIterator(
                   new \RecursiveDirectoryIterator(CONFIG)
                 ), '/^((.+?)(\.)(json))$/i'
              );
        foreach ($RDI as $CFG) self::$config[pathinfo(basename($CFG), PATHINFO_FILENAME)] = json_decode(file_get_contents($CFG), true);

        /**
         * Identify parts of the URL/URI to compose reference links for the template
         */

        /** Exposes custom Server PORT */
        $serverport = ($_SERVER["SERVER_PORT"] == 80 || $_SERVER["SERVER_PORT"] == 443) ? "" : ":" . $_SERVER["SERVER_PORT"];
        /** Build relative based (URI) on website root location (not URL page location). Correct slashes and clean double slashes. */
        $pathinside = self::$root;
        self::$uri  = ($_SERVER["SERVER_PORT"] == 80 || $_SERVER["SERVER_PORT"] == 443) ? "/" . $pathinside : $this->fixSlashes($pathinside);
        self::$uri  = self::$uri == "" ? '/' : self::$uri;
        /** Windows protection for empty base URI that result in "double-slashes". */
        self::$uri  = $this->fixSlashes(self::$uri);
        /** Build the full Web Location (URL) for site resources based on website root location (not URL page location). */
        self::$url  = DS . DS . rtrim( $this->fixSlashes($_SERVER['SERVER_NAME'] . $serverport . "/" . self::$uri), DS );
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
     * @param   string   $templateName  A string with the name of the template.
     *                                  The variable only passed on to "setTemplate()".
     *
     * @return  boolean  TRUE   if file Twig is installed and initialization is complete,
     *                   FALSE  if file Twig is not exist installed ad initialization failed.
     */
    public function initializeTemplate($templateName = false)
    {
        /** Check for cache configuration and folder location of the Cache Folder */
        $cache_folder = $this->fixSlashes(self::$root . DS . self::$config['settings']['cache']['folder']);
        /** Check if Cache Folder is a real folder */
        if (!is_dir($cache_folder)) $cache_folder = false;
        /** If Cache is set as active, set Cache Folder location (or false if not) */
        $cache  = self::$config['settings']['cache']['enabled']
                ? $cache_folder
                : false;

        /**
         * TWIG Environment Options defined in the site's configuration file.
         */
        self::$template['environment'] = array(
          'cache'               => $cache,
          'debug'               => self::$config['settings']['debug']['enabled'],
          'charset'             => (!empty(self::$config['settings']['charset'])) ? self::$config['settings']['charset'] : "UTF-8", // Default = 'UTF-8'
          'base_template_class' => 'Twig_Template',   // Default = 'Twig_Template'
          'strict_variables'    => false,             // Default = false
          'autoescape'          => 'html',            // Default = 'html'
          'auto_reload'         => null               // Default = null
        );

        /**
         * Set reference variables for templates in regards to important resources
         */
        $path = self::$config['settings']['twig']['fullpath'] ? self::$url . "/" : self::$uri;
        self::$template['path'] = [
            'url'       => self::$url,
            'uri'       => rtrim(self::$uri,DS),
            'template'  => $this->fixSlashes($path . "template"),
            'vendor'    => $this->fixSlashes($path . "vendor"),
            'assets'    => $this->fixSlashes($path . "assets"),
            'js'        => $this->fixSlashes($path . "assets" . DS . "js"),
            'css'       => $this->fixSlashes($path . "assets" . DS . "css"),
            'img'       => $this->fixSlashes($path . "assets" . DS . "img"),
            'font'      => $this->fixSlashes($path . "assets" . DS . "font"),
            'audio'     => $this->fixSlashes($path . "assets" . DS . "audio"),
            'video'     => $this->fixSlashes($path . "assets" . DS . "video"),
            'plugins'   => $this->fixSlashes($path . "assets" . DS . "plugins"),
            'resources' => $this->fixSlashes($path . "assets" . DS . "resources")
        ];

        /**
         * Call function to complete Twig implementation
         */
        return $this->setTemplate($templateName);
    }


    /**
     * This Method sets the template system based on Twig
     *
     * @param   string   $templateName  A string with the name of the template.
     *
     * @return  boolean  TRUE   if file Twig is installed and initialization is complete,
     *                   FALSE  if file Twig is not exist installed ad initialization failed.
     */
    public function setTemplate($templateName = false)
    {
        /**
         * Properly discover and set template physical path
         */
        if ($templateName && file_exists(self::$core['location']['template'] . $templateName)):
            $direct   = $this->fixSlashes(self::$core['location']['template'] . $templateName);
            $relative = $this->fixSlashes(self::$core['location']['templ_rel'] . $templateName);
        else:
            $direct   = self::$core['location']['template']  . self::$config['settings']['twig']['template'] . DS;
            $relative = self::$core['location']['templ_rel'] . self::$config['settings']['twig']['template'] . DS;
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
                'url'    => $this->fixSlashes( str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI'] ) ),
                'index'  => &self::$config['settings']['index']
            ]
        ];

        /**
         * Check if Twig is physically installed.
         * Terminate and return FALSE if not.
         */
        if (!is_dir(self::$core['location']['vendor'] . "twig")) return false;

        /**
         * Initialize (or override) Twig Template boilerplate.
         * Based on NEW TWIG 3.0+.
         */
        self::$twig = new \Twig\Environment(
                        new \Twig\Loader\FilesystemLoader(
                            self::$template['access']['local']['direct']
                        ), self::$template['environment']
                    );

        /**
         * Return TRUE to indicate Twig is installed and set.
         */
        return true;
    }
}
