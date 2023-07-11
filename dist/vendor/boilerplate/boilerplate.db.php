<?php
/**
 * THE MULTI-PURPOSE WEBSITE BOILERPLATE WITH TWIG SUPPORT
 * Boilerplate Module :: DATABASE
 *
 * This file holds a collection of supporting functions for a multitude of
 * Database Operations and Data Handling / Manipulation needs.
 *
 * For complete description and further information, @see MANUAL at docs/TOC.md.
 *
 * @since      July, 2022.
 * @category   Database (MySQL : PDO) Functions
 * @version    1.1.2-beta 1
 *
 * @author     Julio Marchi <contact@juliomarchi.com> | Twitter: @MrMarchi
 * @copyright  See Full Header Comment Blocks at "dist/index.php"
 * @license    https://opensource.org/licenses/MIT
 */

/**
 * Connections Global Options
 */
$pdo_charset = 'utf8mb4';
$pdo_options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];



/**
 * Build DSN
 */
function buildDSN($host, $db, $charset = false)
{
    global $pdo_charset;

    /** Validate Parameters */
    if ( empty($host) || !is_string($host) ) return false;
    if ( empty($db)   || !is_string($db)   ) return false;

    if ( !$charset || !is_string($charset) ) $charset = $pdo_charset;

    return "mysql:host=$host;dbname=$db;charset=$charset";
}


/**
 * Connect to DB
 */
function connectDB($dsn, $usr, $pwd, $options = false)
{
    global $pdo_options;

    /** Validate SQL Query is a non-empty string */
    if ( empty($dsn) || !is_string($dsn) ) return false;
    if ( empty($usr) || !is_string($usr) ) return false;
    if ( empty($pwd) || !is_string($pwd) ) return false;

    if ( !$options || !is_array($options) ) $options = $pdo_options;

    try {
        return new PDO($dsn, $usr, $pwd, $options);
    } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }

    return false;
}



/**
 * HELPER FUNCTION
 * Simply executes a call to the  Database
 *
 * @param  String   $sql  The SQL Query to be executed against the Database;
 * @param  Boolean  $raw  If TRUE, return the Database Response Object
 *                        If FALSE, return a parsed Array from the Database Response Object
 *                        Default = FALSE
 *
 * @return  Mixed         Either returns an Array with the result of the SQL Query execution
 *                        or FALSE if something failed.
 */
function queryDB($db, $sql, $raw = false)
{
    // global $database_db;

    /** Validate SQL Query is a non-empty string */
    if ( empty($sql) || !is_string($sql) ) return false;

    /** Prepare and execute the SQL Call */
    $request  = $db->prepare($sql);
    $response = $request->execute();

    /** If $raw is null, return query execution response only, not data */
    if ($raw===null) return $response;

    /** If Database Response Object is NOT requested, fetch result to Array */
    if (!$raw) $response = $request->fetchAll();

    /** Return SQL Query result */
    return $response;
}

/**
 * Fix and format value as a proper BOOLEAN value to be used in a SQL Query String.
 *
 * @param Integer $var   The variable to be formatted. Anything other than false, "FALSE", or 0 will
 *                       be considered TRUE and set as 1. Otherwise, it will assume and return 0.
 * @param Mixed   $comma String to be added at the end of the formatted value.
 *                       It is commonly set as ", " for a field separator, but it can be set to
 *                       any other string or separator format, or set to FALSE, 0, or "" for none.
 *
 * @return String The proper formatted and quoted String to be concatenated/used in a SQL Query String.
 */
function makeBol($var, $comma = ', ')
{
    return (int)is_enabled($var) . ( $comma ?? '');
}

/**
 * Fix and format value as a proper STRING value to be used in a SQL Query String.
 *
 * @param Integer $var   The variable to be formatted. Function will run values against addslashes().
 * @param Mixed   $comma String to be added at the end of the formatted value.
 *                       It is commonly set as ", " for a field separator, but it can be set to
 *                       any other string or separator format, or set to FALSE, 0, or "" for none.
 *
 * @return String The proper formatted and quoted String to be concatenated/used in a SQL Query String.
 */function makeStr($var, $comma = ', ')
{
    $var = addslashes($var);
    return "'$var'" . ( $comma ?? '');
}

/**
 * Fix and format value as a proper INTEGER value to be used in a SQL Query String.
 *
 * @param Integer $var   The variable to be formatted
 * @param Mixed   $comma String to be added at the end of the formatted value.
 *                       It is commonly set as ", " for a field separator, but it can be set to
 *                       any other string or separator format, or set to FALSE, 0, or "" for none.
 *
 * @return String The proper formatted and quoted String to be concatenated/used in a SQL Query String.
 */
function makeInt($var, $comma = ', ')
{
    return "'" . (int)$var . "'" . ( $comma ?? '');
}

/**
 * Fix and format value as a proper FLOAT value to be used in a SQL Query String.
 *
 * @param Integer $var   The variable to be formatted
 * @param Mixed   $comma String to be added at the end of the formatted value.
 *                       It is commonly set as ", " for a field separator, but it can be set to
 *                       any other string or separator format, or set to FALSE, 0, or "" for none.
 *
 * @return String The proper formatted and quoted String to be concatenated/used in a SQL Query String.
 */
function makeFlo($var, $comma = ', ')
{
    return "'" . (float)$var . "'" . ( $comma ?? '');
}

/**
 * Fix and format value as a proper NULL value to be used in a SQL Query String.
 *
 * @param Mixed  $var   The variable to be formatted. If not null (or a string representing "NULL"),
 *                      actual value will be used instead.
 * @param Mixed  $comma String to be added at the end of the formatted value.
 *                      It is commonly set as ", " for a field separator, but it can be set to
 *                      any other string or separator format, or set to FALSE, 0, or "" for none.
 *
 * @return String The proper formatted and quoted String to be concatenated/used in a SQL Query String.
 */function makeNul($var, $comma = ', ')
{
    // $var = $var === null ? 'NULL' : $var;
    return ($var ?? "NULL") . ( $comma ?? '');
}

/**
 * Fix and format value as a proper SERIALIZED String to be used in a SQL Query String.
 *
 * @param Integer $var   The variable to be formatted
 * @param Mixed   $comma String to be added at the end of the formatted value.
 *                       It is commonly set as ", " for a field separator, but it can be set to
 *                       any other string or separator format, or set to FALSE, 0, or "" for none.
 *
 * @return String The proper formatted and quoted String to be concatenated/used in a SQL Query String.
 */function makeSer($var, $comma = ', ')
{
    $var = is_array($var) ?? $var[] = $var;
    return "'" . serialize($var) . "'" .( $comma ?? '');
}