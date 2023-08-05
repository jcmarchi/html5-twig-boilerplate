<?php
/**
 * THE MULTI-PURPOSE WEBSITE BOILERPLATE WITH TWIG SUPPORT
 * Boilerplate Gentelella Dashboard Page Example :: APP
 *
 * This page is part of the APPLICATION that uses the BOILERPLATE and not a
 * direct part of the BOILERPLATE itself. It is present as an example of how
 * to handle the requests and flow of the application logic, including
 * best practices of how to proper use the BOILERPLATE methods and variables,
 * and important annotation for a project kickstart.
 *
 * For complete description and further information, @see MANUAL at docs/TOC.md.
 *
 * @since      August, 2023.
 * @category   Class
 * @version    1.1.1-beta 1
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

$page = "index";
$page = isset($_GET['page']) ? $_GET['page'] : $page;
$twig->addGlobal('page', $page);
$site->cacheStatus(false);
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
$result = ${\boilerplate\boilerplate::$me}->renderPage( "adminlte/page-samples/$page.twig" );

/**
 * Enable the line below if you want a message to be displayed when everything is disabled.
 * Developers can expand on this "feature" by modifying this file, redirecting the request
 * to another page, handling it as an error, or anything else your software requires in terms
 * of operational flow. This file is not overwritten in cases of BOILERPLATE Updates.
 */
if (!$result) echo "Welcome to the Boilerplate Website.";
