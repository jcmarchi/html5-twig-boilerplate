<?php
/**
 * THE MULTI-PURPOSE WEBSITE BOILERPLATE WITH TWIG SUPPORT
 * Boilerplate TRAIT :: VALIDATORS
 *
 * This file holds a collection of supporting validation functions.
 *
 * For complete description and further information, @see MANUAL at docs/TOC.md.
 *
 * @since      August, 2023.
 * @category   Validation Functions
 * @version    1.8.0-beta 1
 *
 * @author     Julio Marchi <contact@juliomarchi.com> | Twitter: @MrMarchi
 * @copyright  See Full Header Comment Blocks at "dist/index.php"
 * @license    https://opensource.org/licenses/MIT
 */

namespace boilerplate;

trait Validators {

    /**
     * Checks if the contents of the provided string is a valid e-mail.
     *
     * @param  string   $email  A variable to be checked if it has an email.
     * @return boolean          TRUE for a valid email, FALSE otherwise.
     */
    public function is_email(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Quick function to validate if the content of a string is a valid JSON statement.
     *
     * @param  string   $json  String to have the contents validated as JSON.
     * @return boolean         TRUE if it is JSON, FALSE otherwise.
     */
    public function is_json($json)
    {
        return  is_string($json) &&
                is_array(json_decode($json, true)) &&
                (json_last_error() == JSON_ERROR_NONE)
                ? true
                : false;
    }

    /**
     * Checks if the received variable represents a possible boolean value.
     * This function is case insensitive.
     *
     * @param  mixed    $variable  Can be anything (string, bol, integer, etc.)
     * @return mixed               TRUE  for "1", "true", "on" and "yes",
     *                             FALSE for "0", "false", "off" and "no",
     *                             NULL otherwise.
     */
    public function is_enabled($variable)
    {
        if (!isset($variable)) return null;
        return filter_var($variable, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }

    /**
     * This is a WRAPPER for the same name function present in the \boilerplate\installer Class
     * ----------------------------------------------------------------------------------------
     *
     * Simply check if the given string can be used to name a property or a function.
     * The evaluation is made based on PHP naming convention standards found at
     * https://www.php.net/manual/en/language.variables.basics.php
     *
     * @param   string  $name  The string to be evaluated.
     * @return  mixed   TRUE if the string can be used as a name for a variable or a function,
     *                  FALSE otherwise.
     *                  NULL if variable is NOT a string.
     */
    public function is_valid_object_name($name) {
        return installer::is_valid_object_name($name);
    }
}
