<?php
/**
 * THE MULTI-PURPOSE WEBSITE BOILERPLATE WITH TWIG SUPPORT
 * Boilerplate Module :: THE APPLICATION
 *
 * This file is The Application. It does not seem much but it will use all the
 * "environment" and "request" knowledge produced by the Boilerplate initialization
 * process, plus all the initialized settings defined by "set.php" (or $_set, if
 * defined) and route the execution of the application to the proper page/module.
 *
 * If MOODFIRED Enrouter Module is present and initialized, developers can implement
 * all routing and security extra functionalities directly to this file. See MOODFIRED
 * ENROUTER documentation for more details (coming soon).
 *
 * Simply expand this file as needed by your Application. Add Sessions, Data Handlers,
 * extra modules, etc... This is YOUR APP, ready to go!
 *
 * For complete description and further information, @see MANUAL at docs/TOC.md.
 *
 * @since      November, 2020.
 * @category   The Application
 * @version    2.4.1-beta 2
 *
 * @author     Julio Marchi <contact@juliomarchi.com> | Twitter: @MrMarchi
 * @copyright  See Full Header Comment Blocks at "dist/index.php"
 * @license    https://opensource.org/licenses/MIT
 */


/** Check if page requested is in the Allowed Locations Array */
$page = checkVirtualPage($locations);

/** Find what to load based on request and config, then load it */
if ($page===null):
    require $maintenance;
elseif ($page===true):
    require $welcome;
elseif ($page===false):
    require $error;
else:
    require_once $page;
endif;


/** Quick Debug */
if (DEBUG) require_once $_['location']['app'] . "helpers" . DS . "debugger.php";