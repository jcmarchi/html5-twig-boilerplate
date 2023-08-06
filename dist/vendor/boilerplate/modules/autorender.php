<?php
/**
 * THE MULTI-PURPOSE WEBSITE BOILERPLATE WITH TWIG SUPPORT
 * Boilerplate Module :: AUTO RENDER
 *
 * This file carries a piece of code that wil try to auto-load either the default
 * Welcome page, Error page or Maintenance page. It is only used if the constant
 * "AUTORENDER" exists and is set to true.
 *
 * For complete description and further information, @see MANUAL at docs/TOC.md.
 *
 * @since      April, 2019.
 * @category   Operational Support
 * @version    1.8.21-beta 3
 *
 * @author     Julio Marchi <contact@juliomarchi.com> | Twitter: @MrMarchi
 * @copyright  See Full Header Comment Blocks at "dist/index.php"
 * @license    https://opensource.org/licenses/MIT
 */


/**
 * If BOILERPLATE not initialized, fail and abort with error message.
 * We strongly suggest developers to use this same method to detect if
 * BOILERPLATE is correctly initialized, but the response can be a
 * redirection to another page, a custom error handling, or anything
 * else your software requires in terms of operational flow. This file
 * is never overwritten in cases of BOILERPLATE Updates.
 */
if (!defined('BOILERPLATE')) \boilerplate\installer::error(2);

/**
 * Assuming Boilerplate Class has been instantiated into the variable $site as suggested,
 * calls to methods in the class can be done simply by referencing the variable. I.e.:
 *
 *   $result = $site->renderPage( $page );
 *
 * However, as we allow customization of the instantiable variable, if developers decide to
 * used a different variable and define it using DEFAULT_BOILERPLATE_OBJECT, the name of the
 * instantiated variable will be stored in the object itself, in the static variable "$me".
 * As such, it can be retrieved and used as the example below (which is safe to use):
 *
 *   $theInstanceName = \boilerplate\boilerplate::$me;
 *   $result = $$theInstanceName->renderPage( $page );
 *
 * Notice the DOUBLE $$ in the method call. It is because we are calling the instance by the
 * variable "contents" (which is a string with he name of the instantiated object).
 *
 * In the sense of exploiting on of the most interesting PHP features PHP, the "variable variable",
 * one can always shorthand its usage as:
 *
 *   $result = ${\boilerplate\boilerplate::$me}->renderPage( $page );
 *
 * Either way, your code should comply with PHP and work flawlessly. Just chose your poison and
 * use it consistently across your program.
 */
$page = ${\boilerplate\boilerplate::$me}->checkPage();

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
    \boilerplate\installer::error(5);

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
    require_once(boilerplate::$BOILERPLATE['location']['app'] . "pages". DS . $folder . DS . boilerplate::$config['settings']['twig'][$index]);

elseif (isset(boilerplate::$config['static'][$index]) && file_exists(boilerplate::$BOILERPLATE['location']['app'] . "pages" .  DS . "static" . DS . boilerplate::$config['static'][$index])):
    echo file_get_contents(boilerplate::$BOILERPLATE['location']['app'] . "pages" . DS . "static" . DS . boilerplate::$config['static'][$index]);

else:
    echo "<h1>Fallback $string Page!</h1>";
    echo "<h3>This $string Page is being rendered by the \"AUTO RENDER\" System.</h3>";
    echo "<p>This is an issue! You <i>configuration file</i> may be missing, or the <b>Twig</b> system is not correctly set.</p>";
    echo "<p></p>";
    \boilerplate\installer::error(4);

endif;
