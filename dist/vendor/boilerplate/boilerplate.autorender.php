<?php
/**
 * THE MULTI-PURPOSE WEBSITE BOILERPLATE - SITEAUTORENDER.PHP : Boilerplate Module.
 * @since      July, 2019.
 * @category   Starter
 * @version    1.0.01-RC.1+Alpha:1
 * PHP version 5.6+ (preferable 7.x)
 *
 * This file carries a piece of code that wil try to auto-load either the default
 * Welcome page, Error page or Maintenance page. It is only used if the constant
 * AUTORENDER exists and is set to true.
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

/** Check if page from requested URL exists */
$page = checkPage();

/** Check result of checkPage() */
if ($page === true):
    $index  = 'welcome';
    $string = "Welcome";
    $folder = "public";  // Only used for TWIG templates

elseif ($page === false):
    $index  = 'error';
    $string = "Error";
    $folder = "common";  // Only used for TWIG templates

elseif($page === NULL):
    $index  = 'maintenance';
    $string = "Maintenance";
    $folder = "common";  // Only used for TWIG templates

else:
    die("<b>ERROR 4000</b>: Unrecoverable condition encountered. Aborted.");

endif;

/**
* Load the Welcome Page based on the site's configuration.
* If Twig is enabled, try loading the Welcome Page PHP file that renders a template.
* If Twig is disabled, try loading the static HTML based Welcome Page.
* If all fails, display a PHP self-generated Welcome Page (shown below).
*
* If possible, try not to change the logic here presented.
* You may, of course, adjust the HTML code below to reflect your specific needs.
* Just keep in mind that if you are correctly using this Boilerplate, setting the proper
* values in the configuration file, and pointing the resources correct files, the
* message below should NEVER be displayed. It only exists as a safeguard.
*/
if (defined('TWIG') && TWIG):
    require_once(siteClass::$env['path']['app'] . "pages". DS . $folder . DS . siteClass::$config['twig'][$index]);

elseif (isset(siteClass::$config['static'][$index]) && file_exists(siteClass::$env['path']['app'] . "pages" .  DS . "static" . DS . siteClass::$config['static'][$index])):
    echo file_get_contents(siteClass::$env['path']['app'] . "pages" . DS . "static" . DS . siteClass::$config['static'][$index]);

else:
    echo "<h1>Fallback $string Page!</h1>";
    echo "<h3>This $string Page is being rendered by the AUTORENDER System.</h3>";
    echo "<p>This is an issue! You <i>configuration file</i> may be missing, or the <b>Twig</b> system is not correctly set.</p>";
    echo "<p>Please, refer to the documentation for <b>ERROR 4004</b> and how to fix it.</p>";
endif;
?>