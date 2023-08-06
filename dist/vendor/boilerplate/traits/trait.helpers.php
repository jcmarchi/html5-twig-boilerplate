<?php
/**
 * THE MULTI-PURPOSE WEBSITE BOILERPLATE WITH TWIG SUPPORT
 * Boilerplate Module :: HELPER
 *
 * This file holds some quick functions that can be accessed from the global scope
 * to facilitate the utilization of the Boilerplate installed resources.
 *
 * For complete description and further information, @see MANUAL at docs/TOC.md.
 *
 * @since      December, 2021.
 * @category   Helper
 * @version    1.0.6-beta 1
 *
 * @author     Julio Marchi <contact@juliomarchi.com> | Twitter: @MrMarchi
 * @copyright  See Full Header Comment Blocks at "dist/index.php"
 * @license    https://opensource.org/licenses/MIT
 */

namespace boilerplate;

trait helpers {

    /**
     * Helper function to quickly call $twig->render
     * with pre-set global variables from self::$template
     *
     * @param  string  $page  A string with the name of the TWIG file to render (no extension required)
     * @param  array   $vars  Any additional set of variables a page may need
     */
    public function renderPage($page, $vars = [])
    {
        /**
         * Capture execution time ended and save it to $_.
         */
        self::$core['execution'] = microtime(true)-installer::$APPLICATION['start'];

        if (self::$twig):
            /** Bring the contents of the Array self::$core to $twig_vars['boilerplate'] */
            $vars['boilerplate'] = self::$core;
            /** Bring the contents of the Array $_SERVER to $twig_vars['server'] */
            $vars['boilerplate']['server'] = !empty($_SERVER) ? $_SERVER : [];
            /** Bring the contents of the Array $_SESSION (if it exists) to $twig_vars['session'] */
            $vars['boilerplate']['session'] = !empty($_SESSION) ? $_SESSION : [];
            /** Merge Dynamic Paths Array with Config Data Array and custom sent $vars Array/variable */
            $twig_vars = array_merge(self::$template['path'], $vars);
            /** Check if $page has been sent with an extension (I.e. 'page.twig') */
            $twig_fext = pathinfo($page, PATHINFO_EXTENSION);
            /** If no extension is defined, add it to the filename */
            $twig_file = ($twig_fext == "" || $twig_fext == NULL) ? $page . ".twig" : $page;
            /** Render requested page with merged variables added */
            print self::$twig->render($twig_file, $twig_vars);
        endif;

        return (bool)self::$twig;
    }


    /**
     * Helper function to check if the page from URL actually exist in the filesystem.
     *
     * The URL request is based on $_SERVER['DOCUMENT_ROOT'] +  $_SERVER['REQUEST_URI'].
     *
     * This function uses a "default model" to confirm the requested URL points to a usable
     * folder and file in the physical directory tree.
     *
     * If a parameter is sent, the function will check the provided String instead of the
     * URL contents.
     *
     * While you can use this function to implement a many levels of "path virtualization",
     * the single purpose of this function it to add to the traditional URL -> File System
     * reference some of the features Boilerplate provides for "path virtualization", such
     * as Maintenance Mode and Error Page Handling, while yet allowing the developer to
     * handle and manage the flow in front of the server, having full control of everything.
     *
     * This function is simple enough but extremely helpful when you want to convert a basic
     * HTML/BOOTSTRAP template into a functional website, yet in pure HTML, but fully powered
     * by PHP and TWIG. Even when not using PHP ou TWIG (or COMPOSER) immediately, having the
     * ability to dismantle a template and re-implement it in a full flagged system that will
     * allow expansion of the site with no extra efforts is priceless. Besides, rarely any
     * website nowadays exist without a few features in PHP or other server-side language.
     *
     * Furthermore, you can always combine this function with checkVirtualPage() - or vice-versa -
     * and create amazing flows, even with the possibility to be unique and exclusive by page
     * or group of pages. From simple direct public access without requiring much security or
     * management to a massive path virtualization process, the sky is the limit.
     *
     * Regardless or how you will use this helper, it can be very powerful when combined
     * with the other features of the Boilerplate.
     *
     * @param  string   $loc   A simple String containing the equivalent to this concatenation:
     *                         $_SERVER['DOCUMENT_ROOT'] + $_SERVER['REQUEST_URI']
     * @return mixed  TRUE   if $loc represents a "file location + file" that can be loaded.
     *                FALSE  if $loc points to an inexistent "file location + file".
     *                NULL   if maintenance page is on (regardless if file exists or not)
     */
    public function checkPage($loc = "")
    {
        if (defined('MAINTENANCE') && MAINTENANCE) return null;

        /**
         * Check for empty user input
         */
        $request = (empty($loc))
                ? self::$template['access']['requested']['url']
                : $loc;

        $theURL = pathinfo($request);

        if (!isset($theURL['extension'])):
            $theURL['request'] = $this->fixSlashes( $request . DS . self::$template['access']['requested']['index'] );

        else:
            $theURL['request'] = $this->fixSlashes( $request );

        endif;

        return file_exists( $theURL['request'] );
    }


    /**
     * Helper function to check if the page from URL actually exist in the filesystem.
     *
     * This function operates under the condition there is an array predefining available
     * paths (locations) pages can be loaded from, while other locations, even if they
     * exists, aren't allowed. It is perfect for sites that require credentials to access
     * back-end or special contents, or if you want to have full control of your site's
     * navigation.
     *
     * @param  array    $loc  A simple Array containing the possible paths to be investigated.
     * @param  array    $__EXTension (Optional) A simple Array with extensions to be observed.
     * @return mixed    TRUE   if file is "index",
     *                  FALSE  if file does not exist,
     *                  NULL   if maintenance page is on (regardless if file exists or not),
     *                  STRING The exact path where the called file was identified.
     */
    public function checkVirtualPage($loc, $ext = [])
    {
        // global $site;

        /** If maintenance mode is active, return NULL */
        if (defined('MAINTENANCE') && MAINTENANCE) return NULL;
        /** If no location is define, then return FALSE as nothing exists to be checked */
        if (empty($loc)) return false;

        /** Merge provided $ext Array with default verifiable extensions. */
        $cnf_e = self::$config['settings']['extensions']['allowed']; // Allowed extensions
        $emp_e = self::$config['settings']['extensions']['empty'] ? [''] : []; // Can we accept empty extension for files? :/
        $ext_f = array_merge($cnf_e, $ext, $emp_e); // Merge everything
        $def_n = self::$config['settings']['extensions']['default']; // Set default extension in case no extension exists in the request

        /** Breakdown requested URI to find $request */
        $request = $unqueried = explode('?', $_SERVER['REQUEST_URI'])[0]; // Remove Query String from URL
        $request = trim($request, '/');
        $request = $middle = explode('/', $request);
        $request = array_pop($request);
        $requestType = pathinfo($request, PATHINFO_EXTENSION);
        $isExtension = in_array($requestType, $ext_f);
        $request = pathinfo($request, PATHINFO_FILENAME);  // Get value of $request without the extension

        /**
         * SUB-FOLDERS ARE NOT ALLOWED!
         * This code will force the analysis of the REQUEST to consider the URI location only (which is
         * basically the root of teh site). It is done purposefully because sub-paths must be defined
         * in the Array $loc, which can have a mix of different locations to be validated. This makes
         * this function smaller, faster, and much more flexible. */
        array_pop($middle);
        $middle = strtolower( DS . implode('/', $middle) . DS );
        $requri = strtolower( self::$uri );
        if (strlen($middle) > strlen($requri)) return false;

        /**
         * Safeguard to identify the index file in the root folder.
         * If the requested URL is the root of the site, simply return true.
         **/
        if (rtrim(self::$uri, '/') == rtrim($unqueried, '/'))  return true;
        if ($isExtension):
            $rootbase = explode('.', trim(self::$uri, '/'))[0];

            if (self::$uri . $request == self::$uri . $rootbase) return true;
            if (self::$uri . $request == self::$uri . "index")  return true;
        endif;

        /**
         * Check for the $request extension in the $ext_f Array.
         * Only look any further for a file if the extension is allowed.
         **/
        if ($isExtension):
            /** Loop through the $locs trying to find $request */
            foreach ($loc as $path):
                /** Produce valid extension, if needed */
                $extension = empty($requestType) ? ".$def_n" : ".$requestType";
                /** Check files based on $loc + $request + possible extensions */
                // $localFile = $site::$drive . $site::$uri . $path . '/' . $request . $extension;
                // $localFile = $site::$root . '/' . $path . '/' . $request . $extension;
                $localFile = $this->fixSlashes(self::$core['location']['root'] . '/' . $path . '/' . $request . $extension);
                /** Check if file truly exists. Return the full file path plus file name and extension if it does */
                if (file_exists($localFile)) return $localFile;
            endforeach;
        endif;

        /** If everything fails, return fail as file does not exist */
        return false;
    }


    /**
     * Get or Set TWIG Cache Folder
     *
     * This function will either respond with the current location of the
     * TWIG Cache Folder or set (change) the TWIG Cache Folder location.
     *
     * If the Cache Folder changes while cache is active and in use, the
     * contents of the old cache folder will not be moved, possibly resulting
     * in unpredictable conditions. Generally, this function should be only
     * called to set a Cache Folder Location before page rendering is called.
     *
     * If Cache function is disabled when Cache Folder Location is set, it
     * will automatically be enabled.
     *
     * @param   mixed  $folder  If empty string (default) will respond with the
     *                          current location of the Cache folder.
     *                          If string is NOT empty, it will be evaluated as
     *                          a RELATIVE path, and if it is valid, will point
     *                          the Cache Folder to it.
     * @return  mixed  Either the current location of the Cache Folder,
     *                 TRUE if succeeded, or FALSE if failed.
     */
    public function cacheFolder($folder = "")
    {
        /**
         * If $folder is empty, simply return the current Cache Folder
         * listed in the Boilerplate Configuration.
         */
        if (empty($folder)) return self::$core['settings']['cache']['folder'];

        /**
         * If $folder is a non-empty string, check if it is a valid local folder.
         * Return flagging an error if it is not.
         */
        if (!is_dir(self::$root . $folder)) return false;

        /**
         * If $folder is a valid local folder, update the Boilerplate Configuration
         * and enable the TWIG Cache functionality to use the new folder then
         * terminate returning true to flag operation success.
         */
        self::$core['settings']['cache']['folder']  = $folder;
        self::$core['settings']['cache']['enabled'] = true;
        self::$core['location']['cache'] = self::$root . $folder;
        self::$twig->setCache(self::$root . $folder);
        return true;
    }


    /**
     * Get or Set TWIG Cache Status
     *
     * This function will respond with the current status of the TWIG Cache
     * function, or will try to enable/disable the TWIG Cache function.
     *
     * Will check if self::$core['settings']['cache']['folder'] is valid
     * before enabling the Cache.
     *
     * @param   mixed  $status  NULL (default) will respond with the current
     *                          TWIG Cache status.
     *                          TRUE will try to enable the TWIG Cache assuming
     *                          the current folder in the Settings is valid.
     *                          FALSE will disable the TWIG Cache function.
     * @return  boolean If setting Cache Status, TRUE if succeeded, FALSE if failed,
     *                  otherwise, the Cache Status: TRUE = enabled, FALSE = disabled.
     */
    public function cacheStatus($status = null)
    {
        /** Is $status is not present (or NULL) simply return the current Cache Status */
        if ($status === null) return self::$core['settings']['cache']['enabled'];

        /**
         * If $status is TRUE, get current Cache Folder from Boilerplate configuration
         * and try to enable Caching functionality. Return result to caller.
         */
        $current_cache_folder = self::$core['settings']['cache']['folder'];
        if ($status === true) return $this->cacheFolder($current_cache_folder);

        /**
         * If $status is FALSE, simply set the Cache Status in the Boilerplate configuration
         * and disable the TWI Caching Feature. Keep Cache Folder info in the Boilerplate
         * configuration untouched. Return TRUE.
         */
        if ($status === false):
            self::$core['settings']['cache']['enabled'] = $status;
            self::$twig->setCache($status);
            return true;
        endif;

        /** If gets here, something failed. Flag and terminate. */
        return false;
    }


    /**
     * Function to do the one thing this amazing template system (TWIG) failed to implement: EMPTY THE CACHE FOLDER!
     *
     * This function already know where the CACHE FOLDER is located at, unless you entirely remove it from the
     * configuration file. In such case, I strongly recommend NEVER use this function because you can accidentally
     * delete all the files from your own server or site, assuming you have a wrong folder noted.
     *
     *           ************************************
     *           ***    USE IT A YOUR OWN RISK    ***
     *           ************************************
     *
     * It will be safer if you keep the structure of the Boilerplate CACHE FOLDER LOCATION intact.
     *
     * This function will delete ALL FOLDERS and FILES INSIDE THE FOLDER from the  CACHE FOLDER location.
     * It won't delete any FILES in the CACHE FOLDER itself (as TWIG keep its cache into sub-folder's structures).
     *
     * This function use PHP's internal Recursive Iterator, which is safe and highly efficient. It is able to
     * wipe clean a CACHE FOLDER with hundreds of thousands of sub-folders ad files in a fraction of a second.
     *
     * @param  mixed  $status  If not present (NULL), the function will simply clean the Cache Folder but will
     *                         keep the Status of the Cache Function untouched (either active or inactive).
     *                         TRUE will try to enable the TWIG Cache assuming the current folder in the
     *                         Settings is valid.
     *                         FALSE will disable the TWIG Cache function.
     * @return  boolean TRUE if succeeded, FALSE if failed.
     */
    public function emptyCache($status = null)
    {
        /** Get current Cache Folder from Boilerplate configuration */
        $cache_folder = self::$core['location']['cache'];

        /** If Cache Folder is not a valid local folder, terminate and flag error */
        if (!is_dir($cache_folder)) return false;

        /**
         * Recursively remove sub-folders only from $cache_folder.
         * Keep specified files in the folder
         */
        foreach( new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator( $cache_folder, \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS ),
            \RecursiveIteratorIterator::CHILD_FIRST ) as $item )  {
                if ($item->isFile()) {
                    /** Files in the root of the cache folder are kept unharmed */
                    if ($item->getPath() . DS !== $cache_folder) unlink( $item );
                } else {
                    /** After files are removed, remove dir */
                    rmdir( $item );
                }
        }

        /** If no new Status is requested, just terminate flagging success */
        if ($status === null) return true;

        /** Execute request to Cache Cache Status and return with operation result */
        return $this->cacheStatus($status);
    }

}


