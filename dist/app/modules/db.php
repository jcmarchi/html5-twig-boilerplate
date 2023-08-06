<?php
/**
 * THE MULTI-PURPOSE WEBSITE BOILERPLATE WITH TWIG SUPPORT
 * Boilerplate Module :: DB ACCESS CONTROLLER (EXAMPLE)
 *
 * This file is an empty example file. Read the implementation below for more details.
 *
 * For complete description and further information, @see MANUAL at docs/TOC.md.
 *
 * @since      July, 2023.
 * @category   Database Accessa Module (example)
 * @version    1.0.0-beta 1
 *
 * @author     Julio Marchi <contact@juliomarchi.com> | Twitter: @MrMarchi
 * @copyright  See Full Header Comment Blocks at "dist/index.php"
 * @license    https://opensource.org/licenses/MIT
 *
 */

//  insight($_BOILERPLATE);

/**
 * All are the same Array:
 *    boilerplate::$core['databases']
 *    $_BOILERPLATE['databases']
 *    $_['databases']
 */
$db_Server   = $_BOILERPLATE['databases']['main']['server'];
$db_database = $_BOILERPLATE['databases']['main']['database'];
$db_username = $_BOILERPLATE['databases']['main']['username'];
$db_password = $_BOILERPLATE['databases']['main']['password'];

// Using the function from the Boilerplate Database Helper (boilerplate.db.php) to connect with the DB via PDO:
$dsn = $site->buildDSN($db_Server, $db_database);
$db  = $site->connectDB($dsn, $db_username, $db_password);
