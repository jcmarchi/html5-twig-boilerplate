<?php
/**
 * THE MULTI-PURPOSE WEBSITE BOILERPLATE - SITECLASS.PHP : Boilerplate Class.
 * @since      July, 2019.
 * @category   Starter
 * @version    1.0.01-RC.1+Alpha:1
 * PHP version 5.6+ (preferable 7.x)
 *
 * This Static Class Object holds the Boilerplate Core.
 *
 * It is based on a simple Class that requires custom initialization (see below)
 * and must receive the address referent to the "root location" (path) where the
 * website resides in the server.
 *
 * This function is auto-initialized in the siteLoader.php as the object "$app".
 *
 * This class can also initialize the Twig template system if it is enabled in the
 * configuration file (also auto-initialized in the siteLoader.php, if set).
 *
 * @copyright  MOODFIRED is a SUNALEI Technologies brand and project.
 *             Built in association with Global COMPEL, LLC.
 * @link       moodfired.org | moodfired.com | sunalei.org | globalcompel.com
 *
 * @author     Julio Marchi <jmarchi@moodfired.org> - Twitter: @MrMarchi
 * @support    Viktor Bludov <vbludov@moodfired.org>
 * @assistant  Elton Branch <ebranch@moodfired.org>
 * @thanksto   Special Thanks to Eliazer Kosciuk (KLAXMSX).
 *
 * LICENSED UNDER THE MIT LICENSE.
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
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
     * @return Boolean  true   if file Twig is installed and initalization is complete
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
            'resources' => $path . "assets/" . 'resources',
            'config'    => &self::$config
        ];

        /**
         * Call funtion to complete Twig implementation
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