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
 * Notice some basic environment variables are pre-set in this file. Those are required
 * for the basic functions of the Boilerplate. If you opt to use a different Settings
 * File (via $_set), make sure to properly address those minimum requirements.
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

/** Create Array of Allowed Locations */
$locations = [
    'pages',
    'pages/common',
    'pages/public',
    'pages/static'
];

/** Define default service pages */
$welcome     = 'pages/public/welcome.php';
$maintenance = 'pages/common/maintenance.php';
$error       = 'pages/common/error.php';
