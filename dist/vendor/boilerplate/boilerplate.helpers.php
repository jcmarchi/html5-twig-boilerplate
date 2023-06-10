<?php
/**
 * THE MULTI-PURPOSE WEBSITE BOILERPLATE - SITEHELPERS.PHP : Boilerplate Helpers.
 * @since      July, 2019.
 * @category   Starter
 * @version    1.0.01-RC.1+Alpha:1
 * PHP version 5.6+ (preferable 7.x)
 *
 * This file holds some quick functions that can be accessed from the global scope
 * to facilitate the utilization of the Boilerplate installed resources.
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
        $twig_vars = array_merge(siteClass::$template['vars'], $vars);
        $twig_fext = pathinfo($page, PATHINFO_EXTENSION);
        $twig_file = ($twig_fext == "" || $twig_fext == NULL) ? $page . ".twig" : $page;

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