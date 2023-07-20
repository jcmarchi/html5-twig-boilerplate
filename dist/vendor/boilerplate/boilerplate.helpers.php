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
 * This function uses a "default model" to confirm the page in the physical URL is
 * present in the file system. It does not consider "virtualization" of the file system.
 *
 * This function is ideal to be used when called pages are publicly available (no login,
 * no security) or when you simply want to display a non-twig page (PHP or HTML).
 *
 * @return Boolean  true   if file exist
 *                  false  if file does not exist
 *                  null   if maintenance page is on (regardless if file exists or not)
 */
function checkPage()
{
    if (defined('MAINTENANCE') && MAINTENANCE) return NULL;

    $localFile = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI']);

    if (file_exists($localFile)) return true;

    return false;
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

    /** SubPaths are NOT allowed */
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
            // insight();
            // insight($localFile);
            /** Check if file truly exists. Return the full file path plus file name and extension if it does */
            if (file_exists($localFile)) return $localFile;
        endforeach;
    endif;

    /** If everything fails, return fail as file does not exist */
    return false;
}


?>