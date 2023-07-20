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
 * Helper function to quickly call $twig->render
 * with pre-set global variables from siteClass::$template
 *
 * @param  String  $page  A string with the name of the TWIG file to render (no extension required)
 * @param  Array   $vars  Any additional set of variables a page may need
 */
function renderPage($page, $vars = [])
{
    global $app, $twig;

    if (TWIG):
        /** Bring the contents of the Array siteClass::$config to $twig_vars['config'] */
        $app_config['config'] = siteClass::$config;
        /** Merge Dynamic Paths Array with Config Data Array and custom sent $vars Array/variable */
        $twig_vars = array_merge(siteClass::$template['path'], $app_config, $vars);
        /** Check if $page has been sent with an extension (I.e. 'page.twig') */
        $twig_fext = pathinfo($page, PATHINFO_EXTENSION);
        /** If no extension is defined, add it to the filename */
        $twig_file = ($twig_fext == "" || $twig_fext == NULL) ? $page . ".twig" : $page;
        /** Render requested page with merged variables added */
        print siteClass::$twig->render($twig_file, $twig_vars);
    endif;

    return TWIG;
}

/**
 * Helper function to check if the page from URL actually exist in the filesystem.
 *
 * @return Boolean  true   if file exist
 *                  false  if file does not exist
 *                  null   if maintenance page is on (regardless if file exists or not)
 */
function checkPage()
{
    global $app;

    if (defined('MAINTENANCE') && MAINTENANCE) return NULL;

    $localFile = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI']);

    if (file_exists($localFile)) return true;

    return false;
}

/**
 * Helper function to check if the page from URL actually exist in the filesystem.
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
    global $app;

    /** If maintenance mode is active, return NULL */
    if (defined('MAINTENANCE') && MAINTENANCE) return NULL;
    /** If no location is define, then return FALSE as nothing exists to be checked */
    if (empty($loc)) return false;

    /** Merge provided $ext Array with default verifiable extensions. */
    $cnf_e  = siteClass::$config['extensions']['allowed']; // Allowed extensions
    $emp_e = siteClass::$config['extensions']['empty'] ? [''] : []; // Can we accept empty extension for files?
    $ext_f = array_merge($cnf_e, $ext, $emp_e); // Merge everything
    $def_n = siteClass::$config['extensions']['default']; // Set default extension in case no extension exists in the request

    /** Breakdown requested URI to find $request */
    $request = $unqueried = explode('?', $_SERVER['REQUEST_URI'])[0]; // Remove Query String from URL
    $request = trim($request, '/');
    $request = $middle = explode('/', $request);
    $request = array_pop($request);
    $requestType = pathinfo($request, PATHINFO_EXTENSION);
    $isExtension = in_array($requestType, $ext_f);
    $request = pathinfo($request, PATHINFO_FILENAME);  // Get value of $request without the extension

    /** SubPaths are NOT allowed */
    array_shift($middle);
    array_pop($middle);
    $middle = implode('/', $middle);
    if (!empty($middle)) return false;

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
            // $localFile = $app::$drive . $app::$uri . $path . '/' . $request . $extension;
            $localFile = $app::$root . '/' . $path . '/' . $request . $extension;
            /** Check if file truly exists. Return the full file path plus file name and extension if it does */
            if (file_exists($localFile)) return $localFile;
        endforeach;
    endif;

    /** If everything fails, return fail as file does not exist */
    return false;
}


?>