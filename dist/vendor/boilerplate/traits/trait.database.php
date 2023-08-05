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

namespace boilerplate;

trait database {

    /** Connections Options */
    protected $pdo_charset = 'utf8mb4';
    protected $pdo_options = [
        \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES   => false,
    ];


    /**
     * Setter for the PDO Charset Option
     *
     * @param   string   $newCharset  A string defining the new charset to be set for PDO.
     * @return  boolean  TRUE if $newCharset is a valid string and the charset is updated,
     *                   FALSE otherwise.
     */
    public function setPDO_Charset($newCharset = false)
    {
        if (!$newCharset || !is_string($newCharset) || empty($newCharset)) return false;
        $this->pdo_charset = $newCharset;
        return true;
    }


    /**
     * Getter for the PDO Charset Option
     *
     * @return  string  The PDO current Charset.
     */
    public function getPDO_Charset()
    {
        return $this->pdo_charset;
    }


    /**
     * Setter for the PDO Options
     *
     * @param   array   $newOptions  An Array with the PDO options to be set.
     *                               Notice the function will NOT validate the options.
     *                               Notice the function will OVERRIDE the current Options.
     * @return  boolean  TRUE if $newCharset is a valid Array and the PDO options is updated,
     *                   FALSE otherwise.
     */
    public function setPDO_Options($newOptions = false)
    {
        if (!$newOptions || !is_Array($newOptions) || empty($newOptions)) return false;
        $this->pdo_options = $newOptions;
        return true;
    }

    /**
     * Getter for the PDO Options
     *
     * @return  array  The PDO current Options.
     */
    public function getPDO_Options() {
        return $this->pdo_options;
    }


    /**
     * Build PDO's Data Source Name (DSN).
     * DSN is a data structure containing information about a specific
     * database to which PDO driver needs to connect.
     *
     * @param  string  $host     The host name or IP address of the database server.
     * @param  string  $db       The database name.
     * @param  string  $charset  The Charset to use (optional). Default = false.
     *                           If not set, function will use the contents of $this->pdo_charset.
     * @return mixed   STRING if all parameters are correct, formatted as a DNS for PDO,
     *                 FALSE otherwise.
     */
    public function buildDSN($host, $db, $charset = false)
    {
        /** Validate Parameters */
        if ( empty($host) || !is_string($host) ) return false;
        if ( empty($db)   || !is_string($db)   ) return false;
        /** Obtain Charset */
        if ( !$charset || !is_string($charset) ) $charset = $this->pdo_charset;
        /** Compose and return DSN String */
        return "mysql:host=$host;dbname=$db;charset=$charset";
    }


    /**
     * Connect to a Database via PDO
     *
     * This function will establish a functional connection the a MySQL Database
     * via PDO and maintain the connection active until the connection is closed.
     *
     * @param  string  $dsn      The Data Source Name (DSN)/
     * @param  string  $usr      The User Name.
     * @param  string  $pwd      The User Password.
     * @param  array   $options  The PDO Options Array (optional). Default = false.
     *                           If not set, function will use the contents of $this->pdo_options.
     * @return mixed   A PDO Database Object to be queried or closed assuming all parameters are correct,
     *                 FAIL WITH AN ERROR otherwise.
     */
    public function connectDB($dsn, $usr, $pwd, $options = false)
    {
        /** Validate SQL Query is a non-empty string */
        if ( empty($dsn) || !is_string($dsn) ) return false;
        if ( empty($usr) || !is_string($usr) ) return false;
        if ( empty($pwd) || !is_string($pwd) ) return false;

        /** Validate Options */
        if ( !$options || !is_array($options) ) $options = $this->pdo_options;

        /**
         * Try to execute PDO connection, or
         * FAil with ERROR MESSAGE AND CODE. */
        try {
            return new \PDO($dsn, $usr, $pwd, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }


    /**
     * HELPER FUNCTION
     * Simply executes a call to the  Database
     *
     * @param  string   $sql  The SQL Query to be executed against the Database;
     * @param  boolean  $raw  If TRUE, return the Database Response Object
     *                        If FALSE, return a parsed Array from the Database Response Object
     *                        Default = FALSE
     *
     * @return  mixed   Either returns an Array with the result of the SQL Query execution
     *                  or FALSE if something failed.
     */
    public function queryDB($db, $sql, $raw = false)
    {
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
     * @param  integer $var   The variable to be formatted. Anything other than false, "FALSE", or 0 will
     *                        be considered TRUE and set as 1. Otherwise, it will assume and return 0.
     * @param  mixed   $comma String to be added at the end of the formatted value.
     *                        It is commonly set as ", " for a field separator, but it can be set to
     *                        any other string or separator format, or set to FALSE, 0, or "" for none.
     *
     * @return string  The proper formatted and quoted String to be concatenated/used in a SQL Query String.
     */
    public function makeBol($var, $comma = ', ')
    {
        return (int)$this->is_enabled($var) . ( $comma ?? '');
    }


    /**
     * Fix and format value as a proper STRING value to be used in a SQL Query String.
     *
     * @param  integer  $var   The variable to be formatted. Function will run values against addslashes().
     * @param  mixed    $comma String to be added at the end of the formatted value.
     *                         It is commonly set as ", " for a field separator, but it can be set to
     *                         any other string or separator format, or set to FALSE, 0, or "" for none.
     *
     * @return string   The proper formatted and quoted String to be concatenated/used in a SQL Query String.
     */
    public function makeStr($var, $comma = ', ')
    {
        $var = addslashes($var);
        return "'$var'" . ( $comma ?? '');
    }


    /**
     * Fix and format value as a proper INTEGER value to be used in a SQL Query String.
     *
     * @param  integer  $var   The variable to be formatted
     * @param  mixed    $comma String to be added at the end of the formatted value.
     *                         It is commonly set as ", " for a field separator, but it can be set to
     *                         any other string or separator format, or set to FALSE, 0, or "" for none.
     *
     * @return string   The proper formatted and quoted String to be concatenated/used in a SQL Query String.
     */
    public function makeInt($var, $comma = ', ')
    {
        return "'" . (int)$var . "'" . ( $comma ?? '');
    }


    /**
     * Fix and format value as a proper FLOAT value to be used in a SQL Query String.
     *
     * @param  integer  $var   The variable to be formatted
     * @param  mixed    $comma String to be added at the end of the formatted value.
     *                         It is commonly set as ", " for a field separator, but it can be set to
     *                         any other string or separator format, or set to FALSE, 0, or "" for none.
     *
     * @return string   The proper formatted and quoted String to be concatenated/used in a SQL Query String.
     */
    public function makeFlo($var, $comma = ', ')
    {
        return "'" . (float)$var . "'" . ( $comma ?? '');
    }


    /**
     * Fix and format value as a proper NULL value to be used in a SQL Query String.
     *
     * @param  mixed  $var   The variable to be formatted. If not null (or a string representing "NULL"),
     *                       actual value will be used instead.
     * @param  mixed  $comma String to be added at the end of the formatted value.
     *                       It is commonly set as ", " for a field separator, but it can be set to
     *                       any other string or separator format, or set to FALSE, 0, or "" for none.
     *
     * @return string The proper formatted and quoted String to be concatenated/used in a SQL Query String.
     */
    public function makeNul($var, $comma = ', ')
    {
        // $var = $var === null ? 'NULL' : $var;
        return ($var ?? "NULL") . ( $comma ?? '');
    }


    /**
     * Fix and format value as a proper SERIALIZED String to be used in a SQL Query String.
     *
     * @param  mixed  $var    The variable to be formatted
     * @param  mixed  $comma  String to be added at the end of the formatted value.
     *                        It is commonly set as ", " for a field separator, but it can be set to
     *                        any other string or separator format, or set to FALSE, 0, or "" for none.
     *
     * @return string  The proper formatted and quoted String to be concatenated/used in a SQL Query String.
     */

    public function makeSer($var, $comma = ', ')
    {
        $var = is_array($var) ?? $var[] = $var;
        return "'" . serialize($var) . "'" .( $comma ?? '');
    }

}