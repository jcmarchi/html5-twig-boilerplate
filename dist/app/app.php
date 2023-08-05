<?php
/**
 * THE MULTI-PURPOSE WEBSITE BOILERPLATE WITH TWIG SUPPORT
 * Boilerplate Module :: THE APPLICATION
 *
 * This file is The Application. It does not seem much but it will use all the
 * "environment" and "request" knowledge produced by the Boilerplate initialization
 * process, plus all the initialized settings defined by "set.php" (or whatever new
 * setup file developer define via $SET_FILE and $SET) and route the execution of
 * the application to the proper page/module.
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

/**
 * APPLICATION LOGIC NOTES
 * -----------------------
 * As you can see, this file don't have the BOILERPLATE check and error handling
 * as the other example files (welcome.php, error.php, maintenance.php, etc.).
 * The reason is because this file acs as an intermediate between the BOILERPLATE
 * initialization and the final file that will call the page renderings. Mainly
 * this is the application code, the handler of the application logic, which is
 * only executed after the BOILERPLATE is properly initialized, and before the
 * final page is called to be rendered. Most like this file will call other files,
 * that will call other files, until the logic is satisfied and the final
 * rendering is requested.
 */


/**
 * ROUTING OPTION A - SIMPLE PAGE REQUEST LOGIC
 * --------------------------------------------
 * This is a simple check using the simplistic checkPage() function
 * to confirm the request exists.
 *
 * The checkPage() function responds with a simple boolean value or null,
 * in case the maintenance mode is active.
 *
 * The checkVirtualPage() below responds with a more complex status.
 *
 * Both can be combined aor can be worked separately.
 *
 * Try to comment/uncomment one, or the other, or both and check the results.
 */
// if (checkPage()):
//     switch ( strtolower(pathinfo($_BOILERPLATE['access']['requested']['url'])['extension']) ):
//         case "php" :
//             require $access['direct']['welcome'];
//             break;
//         case "html":
//         default:
//             $file = file_get_contents($_BOILERPLATE['access']['requested']['url']);
//             print($file);
//     endswitch;
// endif;


/**
 * ROUTING OPTION B - ADVANCED REQUEST LOGIC
 * -----------------------------------------
 * This is a more complex function that checks the status of the request based
 * on a set of pre-analysis plus it can use an Array to narrow the valid locations.
 *
 * The simplistic checkPage() function above only checks one string, either a full
 * URL sent to the function or the URL detected by the request. It will only confirm
 * if the location and file in the request (if any) exists. It falls back to the
 * pre-defined index file inj the config.json for cases when URL has no file in it.
 *
 * Both can be combined aor can be worked separately.
 *
 * Try to comment/uncomment one, or the other, or both and check the results.
 */

/** Check if page requested is in the Allowed Locations Array */
$page = $site->checkVirtualPage($access['locations']);

/** Check if TWIG is instantiated and operational */
if (TWIG) {

    /** Disable Cache */
    // $twig->setCache(false);

    /** Find what to load based on request and config, then load it */
    if ($page===null):
        require $access['direct']['maintenance'];
    elseif ($page===true):
        require $access['direct']['welcome'];
    elseif ($page===false):
        require $access['direct']['error'];
    else:
        require_once $page;
    endif;

} else {

    /**
     * If TWIG is offline or not initialized, this can fall back to HTML Static Pages.
     * If can be set in the config, or manually defined, then load it on-demand as needed. */
    if ($page===null):
        $html = file_get_contents(ROOT . $access['static']['maintenance']);
    elseif ($page===true):
        $html = file_get_contents(ROOT . $access['static']['welcome']);
    elseif ($page===false):
        $html = file_get_contents(ROOT . $access['static']['error']);
    else:
        $html = file_get_contents($page);
    endif;

    print($html);
}
