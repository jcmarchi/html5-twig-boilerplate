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
 * @version    1.0.0-beta 1
 *
 * @author     Julio Marchi <contact@juliomarchi.com> | Twitter: @MrMarchi
 * @copyright  See Full Header Comment Blocks at "dist/index.php"
 * @license    https://opensource.org/licenses/MIT
 */


/**
 * Helper function to quickly call $_TWIG->render
 * with pre-set global variables from siteClass::$template
 *
 * @param  String  $page  A string with the name of the TWIG file to render (no extension required)
 * @param  Array   $vars  Any additional set of variables a page may need
 */
function renderPage($page, $vars = [])
{
    global $_APP, $_TWIG;

    if (TWIG):
        /** Bring the contents of the Array siteClass::$settings to $_TWIG_vars['boilerplate'] */
        $vars['boilerplate'] = siteClass::$settings;
        /** Bring the contents of the Array $_SERVER to $_TWIG_vars['server'] */
        $vars['boilerplate']['server'] = !empty($_SERVER) ? $_SERVER : [];
        /** Bring the contents of the Array $_SESSION (if it exists) to $_TWIG_vars['session'] */
        $vars['boilerplate']['session'] = !empty($_SESSION) ? $_SESSION : [];
        /** Merge Dynamic Paths Array with Config Data Array and custom sent $vars Array/variable */
        $_TWIG_vars = array_merge(siteClass::$template['path'], $vars);
        /** Check if $page has been sent with an extension (I.e. 'page.twig') */
        $_TWIG_fext = pathinfo($page, PATHINFO_EXTENSION);
        /** If no extension is defined, add it to the filename */
        $_TWIG_file = ($_TWIG_fext == "" || $_TWIG_fext == NULL) ? $page . ".twig" : $page;
        /** Render requested page with merged variables added */
        print siteClass::$twig->render($_TWIG_file, $_TWIG_vars);
    endif;

    return TWIG;
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
 * @param  String   $loc   A simple String containing the equivalent to this concatenation:
 *                         $_SERVER['DOCUMENT_ROOT'] + $_SERVER['REQUEST_URI']
 * @return Boolean  true   if $loc represents a "file location + file" that can be loaded.
 *                  false  if $loc points to an inexistent "file location + file".
 *                  null   if maintenance page is on (regardless if file exists or not)
 */
function checkPage($loc = "")
{
    if (defined('MAINTENANCE') && MAINTENANCE) return NULL;

    /**
     * Check for empty user input
     */
    $request = (empty($loc))
             ? siteClass::$template['access']['requested']['url']
             : $loc;

    $theURL = pathinfo($request);

    if (!isset($theURL['extension'])):
        // $theURL['index']   = empty($theURL['basename']) ? "index.php" : $theURL['basename'];
        $theURL['request'] = fixSlashes( $request . DS . siteClass::$template['access']['requested']['index'] );
    else:
        // $theURL['index']   = empty($theURL['basename']) ? "index.php" : $theURL['basename'];
        $theURL['request'] = fixSlashes( $request );
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
 * @param  Array    $loc  A simple Array containing the possible paths to be investigated
 * @param  Array    $__EXTension (Optional) A simple Array with extensions to be observed.
 * @return Boolean  true   if file is "index"
 *                  false  if file does not exist
 *                  null   if maintenance page is on (regardless if file exists or not)
 *                  String The exact path where the called file was identified.
 */
function checkVirtualPage($loc, $ext = [])
{
    global $_APP;

    /** If maintenance mode is active, return NULL */
    if (defined('MAINTENANCE') && MAINTENANCE) return NULL;
    /** If no location is define, then return FALSE as nothing exists to be checked */
    if (empty($loc)) return false;

    /** Merge provided $ext Array with default verifiable extensions. */
    $cnf_e  = siteClass::$config['config']['extensions']['allowed']; // Allowed extensions
    $emp_e = siteClass::$config['config']['extensions']['empty'] ? [''] : []; // Can we accept empty extension for files? :/
    $ext_f = array_merge($cnf_e, $ext, $emp_e); // Merge everything
    $def_n = siteClass::$config['config']['extensions']['default']; // Set default extension in case no extension exists in the request

    /** Breakdown requested URI to find $request */
    $request = $unqueried = explode('?', $_SERVER['REQUEST_URI'])[0]; // Remove Query String from URL
    $request = trim($request, '/');
    $request = $middle = explode('/', $request);
    $request = array_pop($request);
    $requestType = pathinfo($request, PATHINFO_EXTENSION);
    $isExtension = in_array($requestType, $ext_f);
    $request = pathinfo($request, PATHINFO_FILENAME);  // Get value of $request without the extension

    /** SubPaths are NOT allowed ( why not? :/ ) */
    // array_shift($middle);
    // array_pop($middle);
    // $middle = implode('/', $middle);
    // if (!empty($middle)) return false;

    /**
     * Safeguard to identify the index file in the root folder.
     * If the requested URL is the root of the site, simply return true.
     **/
    if (rtrim(siteClass::$uri, '/') == rtrim($unqueried, '/'))  return true;
    if ($isExtension):
        $rootbase = explode('.', trim(siteClass::$uri, '/'))[0];

        if (siteClass::$uri . $request == siteClass::$uri . $rootbase) return true;
        if (siteClass::$uri . $request == siteClass::$uri . "index")  return true;
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
            // $localFile = $_APP::$drive . $_APP::$uri . $path . '/' . $request . $extension;
            // $localFile = $_APP::$root . '/' . $path . '/' . $request . $extension;
            $localFile = fixSlashes(siteClass::$settings['location']['app'] . '/' . $path . '/' . $request . $extension);
            /** Check if file truly exists. Return the full file path plus file name and extension if it does */
            if (file_exists($localFile)) return $localFile;
        endforeach;
    endif;

    /** If everything fails, return fail as file does not exist */
    return false;
}


?>