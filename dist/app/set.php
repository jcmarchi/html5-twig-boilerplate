<?php
/**
 * THE MULTI-PURPOSE WEBSITE BOILERPLATE WITH TWIG SUPPORT
 * Boilerplate Module :: APPLICATION SETTINGS
 *
 * This file is the perfect location for developers to add their initialization
 * settings for their application, load modules, classes, instantiate objects,
 * etc... It is loaded BEFORE the Boilerplate is initialized, before COMPOSER,
 * and even before TWIG.
 *
 * After loading this file, the system get all initialized and the MAINTENANCE
 * settings in check, and if the maintenance is set, the maintenance page will
 * be loaded instead of the Application itself.
 *
 * It giver opportunity to have some pre-initialization even when maintenance is
 * set, so information can be collected or displayed. Always remember the maintenance
 * page (PHP and HTML/TWIG) is fully customizable, and can be overridden if needed.
 *
 * Then, if it is all ready to go, the Application itself is loaded, which is where
 * developers should check for session status, define traffic flow, and load requested
 * pages when the request is valid. This
 *
 * For complete description and further information, @see MANUAL at docs/TOC.md.
 *
 * @since      January, 2021.
 * @category   Application Pre-Loading Settings and Initializations File
 * @version    1.4.0-beta 2
 *
 * @author     Julio Marchi <contact@juliomarchi.com> | Twitter: @MrMarchi
 * @copyright  See Full Header Comment Blocks at "dist/index.php"
 * @license    https://opensource.org/licenses/MIT
 */

/**
 * Create an Array with the allowed paths and files.
 * This Array is just an example. Developers can design it as they
 * please, as the final resolution is all about how they decide to
 * handle the requests, which is done in "app.php".
 */

/**
 * Define my list of locations I'd allow the system to look for files matching the request
 */
$access['locations'] = ['app/pages', 'app/pages/common', 'app/pages/public', 'app/pages/static'];

/**
 * We are listing here the service pages we want the system to use in
 * case of one of these events occur. We don't need to set it here and
 * we don't even need to use an Array for it. This is just an example.
 * Once you understand the process, you can do it as you want.
 */
$access['direct']['welcome']     = 'app/pages/public/welcome.php';
$access['direct']['error']       = 'app/pages/common/error.php';
$access['direct']['maintenance'] = 'app/pages/common/maintenance.php';

/**
 * There are static pages I can allow the system to fallback in case nothing
 * is set (COMPOSER, TWIG, etc.). It is just a convenience and it can be
 * entirely ignored.
 */
$access['static']['welcome']     = 'app/pages/static/welcome.html';
$access['static']['error']       = 'app/pages/static/error.html';
$access['static']['maintenance'] = 'app/pages/static/maintenance.html';

