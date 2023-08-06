<?php
/**
 * THE MULTI-PURPOSE WEBSITE BOILERPLATE WITH TWIG SUPPORT
 * Boilerplate Module :: FUNCTIONS
 *
 * This file holds a collection of supporting functions for a multitude of usages.
 *
 * For complete description and further information, @see MANUAL at docs/TOC.md.
 *
 * @since      May, 2022.
 * @category   Supporting Functions
 * @version    2.4.2-beta 2
 *
 * @author     Julio Marchi <contact@juliomarchi.com> | Twitter: @MrMarchi
 * @copyright  See Full Header Comment Blocks at "dist/index.php"
 * @license    https://opensource.org/licenses/MIT
 */

namespace boilerplate;

trait functions {

    /**
     * Search array items based on multiple array items.
     *
     * @param  array    $array      The subject Array that will be searched.
     * @param  array    $search     An Array with the items being searched.
     * @param  boolean  $caseCheck  Case Sensitiveness. Default = false.
     *
     * @return array    Returns an Array with the KEYS of the items found.
     */
    public function multi_array_search(array $array, array $search, bool $caseCheck = false): array
    {
        $result = [];

        /** Iterate over each array element */
        foreach ($array as $key => $value) {
            /** Iterate over each search condition */
            foreach ($search as $k => $v) {
                /** Force Case Insensitive if requested */
                if ($caseCheck) {
                    $value[$k] = strtolower($value[$k]);
                    $v = strtolower($v);
                }
                /** If the array element does not meet the search condition then continue to the next element */
                if (!isset($value[$k]) || $value[$k] != $v) continue 2;
            }
            /** Add the array element's key to the result array */
            $result[] = $key;
        }
        return $result;
    }


    /**
     * Change items inside an array using defined function
     *
     * @param  array  $array     Array subject to be modified
     * @param  array  $function  Function to be applied to each item of the Array
     *
     * @return array  Modified Array

    */
    public function multi_array_change(array $array, string $function = 'trim'): array
    {
        return array_map($function, $array);
    }


    /**
     * Finds the value of a specific [$index] of an array based on requested search.
     *
     * @param  mixed   $needle    The string to be searched.
     * @param  array   $haystack  The Array to be searched at.
     * @param  mixed   $index     The [$index] where $needle must be searched for.
     * @param  string  $content   Define the result. TRUE: returns content, FALSE: returns index. Default: false.
     *
     * @return mixed   Returns either content or value of ['id'] based on $content. Returns NULL if nothing is found.
     */
    public function find($needle, array $haystack, $index, bool $content = false)
    {
        $find = $this->multi_array_search($haystack, [ $index => $needle ] );

        if (empty($find)) return NULL;

        return $content ? $haystack[ $find[0] ][$index] : $haystack[ $find[0] ]['id'];
    }


    /**
     * Gets client's IP address.
     *
     * @return  string  Identified Client's Real IP Address.
     */
    public function get_ip()
    {
        /** Check for shared Internet/ISP IP */
        if (!empty($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }

        /** Check for IP addresses passing through proxies */
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {

            /** Check if multiple IP addresses exist in var */
            if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
                $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                foreach ($iplist as $ip) {
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
                        return $ip;
                }
            }
            else {
                if (filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
                    return $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED']) && filter_var($_SERVER['HTTP_X_FORWARDED'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
            return $_SERVER['HTTP_X_FORWARDED'];
        if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && filter_var($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
            return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
            return $_SERVER['HTTP_FORWARDED_FOR'];
        if (!empty($_SERVER['HTTP_FORWARDED']) && filter_var($_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
            return $_SERVER['HTTP_FORWARDED'];

        /** Return unreliable IP address since all else failed */
        return $_SERVER['REMOTE_ADDR'];
    }


    /**
     * Check if given IP is equal or belongs to the referenced group.
     *
     * @param  string  $ip     The IP to be checked
     * @param  string  $range  The "object" IP must validate against, which can be:
     *                            - Single IP in a string format
     *                            - Classless Inter-Domain Routing (CIDR) format
     *                              I.e.: 192.168.1.1-192.168.1.50/32
     *                            - A IP range separated by DASH (-).
     *                              I.e.: 192.168.1.1-192.168.1.50
     *
     * @return boolean  True if IP is in the range. False if it isn't.
     */
    public function check_ip($ip, $range)
    {
        /**
         * If $range has the character "/" in it, then it is in CIDR Format.
         * In this case, convert CIDR to RANGE and let the $range validator
         * (below) do the validation job.
         */
        if (strpos($range, '/') !== FALSE):
            $cidr  = explode('/', $range);
            $range = long2ip((ip2long($cidr[0])) & ((-1 << (32 - (int)$cidr[1]))))
                    . "-" .
                    long2ip((ip2long($cidr[0])) + pow(2, (32 - (int)$cidr[1])) - 1);
        endif;

        /**
         * If $range has the character "-" in it, then it is in RANGE Format.
         * In this case, simply check if given $ip is IN the $range.
         */
        if (strpos($range, '-') !== FALSE):
            $ip_lower  = ip2long(explode('-', $range)[0]);
            $ip_higher = ip2long(explode('-', $range)[1]);
            return (ip2long($ip) <= $ip_higher && $ip_lower <= ip2long($ip));

        /**
         * If $range is a single IP, then validate $ip via simple comparison with $range.
         */
        else:
            return ( ip2long($ip) == ip2long($range) );

        endif;
    }


    /**
     * Build several possible strings in "human readable format" for the provided microtime.
     *
     * This function is just a convenience to support the debug information, but as it can also be used
     * by the programmer for any purpose, it is present on the "functions.php" file (Moodfired Global Functions).
     *
     * @param  float   $millisecond the value to be converted.
     * @param  integer $precision   defines the desired precision for numeric values in the response. Default = 5.
     *
     * @return array with several possible strings to be used in different situations ort combined:
     *
     *         [msec]    => The "millisecond" submitted with "ms" added:
     *                      I.e.: 7654329.458934 ms
     *         [time]    => The human readable time (only):
     *                      I.e.: 2126 hours, 12 minutes and 9 seconds
     *         [days]    => If millisecond are more than a day, return numbers of days.
     *                      I.e.: 88 days
     *         [full]    => Pre-composed string (human readable + days).
     *                      I.e.: 2126 hours, 12 minutes and 9 seconds (more than 88 days)
     *         [precise] => Pre-composed string (millisecond + human readable + days).
     *                      I.e.: 7654329.458934 ms. = 2126 hours, 12 minutes and 9 seconds (more than 88 days)
     */
    public function format_time($millisecond, $precision = 5)
    {
        /**
         * Calculate all the required time values based on received "$millisecond"
         */
        $day     = floor($millisecond / 86400);
        $hours   = floor($millisecond / 3600);
        $minutes = ($millisecond / 60) % 60;
        $seconds = $millisecond % 60;

        /**
         * Format string pieces based on previous calculated time values
         * and combine them in one result (also respecting plurals whenever applicable)
         */
        $timestring  = ($hours) ? "$hours hour" . (($hours > 1) ? "s" : "") : "";
        $timestring .= ($hours && $minutes && $seconds) ? ", " : ( ($hours && $minutes && (!$seconds)) ? " and " : "" );
        $timestring .= ($minutes) ? "$minutes minute" . (($minutes > 1) ? "s" : "") : "";
        $timestring .= (($hours || $minutes) && ($seconds)) ? " and " : ( ((!$hours) && (!$minutes)) ? "" : ( (!$seconds) ? "" : ", ") );
        $timestring .= ($seconds) ? "$seconds second" . (($seconds > 1) ? "s" : "") : "";

        /**
         * Compose the final Array option/items
         */
        $ms = number_format( (float)$millisecond * 60 , $precision, '.', '') . " ms";

        $response['msec'] = $ms;
        $response['time'] = ($timestring) ? $timestring : (( (int)($millisecond * 60) < 1) ? "less than a millisecond" : "less than a second" ) ;
        $response['days'] = ($day) ? "$day day" . (($day > 1) ? "s" : "") : "less than a day";
        $response['full'] = (!$timestring) ? (( (int)($millisecond * 60) < 1) ? "less than a millisecond" : "less than a second" ) . " (" . $response['msec'] . ")" : $timestring . " " . $response['msec'] . " (more than " . $response['days'] . ")";
        $response['precise'] = (!$timestring) ? $ms : $timestring . " ($ms)";

        /**
         * Return final composed Array with all listed formats included.
         */
        return $response;
    }


    /**
     * Format a given value in bytes to human readable format.
     *
     * @param  integer  $size  The number of bytes
     * @param  boolean  $iec   If TRUE, use 1998 "International Electrotechnical Commission" (IEC) ISO/IEC 80000 unit of measurement format, based
     *                         on accepted Bruce Barrow proposal defining unambiguous prefixes for binary multiples (KiB, MiB, GiB etc.), reserving
     *                         kB, MB, GB and so on for their decimal sense. Bruce Barrow published "A Lesson in Megabytes" in 1997 at EEE Standards
     *                         Bearer proposing kibi (symbol Ki), mebi (Mi), gibi (Gi) and tebi (Ti) as binary prefixes for the first four integer
     *                         powers of 1024.
     *                         See: http://members.optus.net/alexey/prefBin.xhtml
     *
     *                         If FALSE, use standard JESD-79-3d (JEDEC	means "Joint Electron Device Engineering Council", now part of
     *                         the "JEDEC Solid State Technology Association").
     *                         See: https://en.wikipedia.org/wiki/JEDEC_memory_standards
     *
     *                         More about unit of measurement formats at: https://en.wikipedia.org/wiki/Timeline_of_binary_prefixes
     *
     * @return string  The human readable formatted unit
     */
    public function format_bytes($size, $iec = true)
    {
        $units = ($iec) ? ['KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'] : ['KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $OrigSize = $size;
        $currUnit = '';
        while (count($units) > 0  &&  $size > 1024) {
            $currUnit = array_shift($units);
            $size /= 1024;
        }
        $Res_Bytes  = number_format( $OrigSize, 0, null, '.'  ) . ' bytes';
        $Res_Larger = number_format( ($size | 0), 0, null, '.') . $currUnit;
        return ($OrigSize > 1024) ? "$Res_Larger ($Res_Bytes)" : $Res_Bytes;
    }


    /**
     * Uploads a single file sent via AJAX and save it in the .temp folder
     * and save it in the specified folder.
     *
     * @param  string  $fileName      Old File Name.
     * @param  string  $fileLocation  The location of the file. If it is not present,
     *                                the system .temp folder will be used.
     *
     * @return  mixed  The result of the uploading process can be:
     *                 TRUE  = Success
     *                 FALSE = Failed
     *                 NULL  = No Uploaded file exist or upload process failed
     */
    public function uploadFile($fileName, $fileLocation = false)
    {
        if (empty($fileName)) return false;
        if (empty($_FILES)) return null;
        if (!isset($_FILES['upload']['tmp_name'][0])) return null;

        // NOTE & TODO: Must be retrofit for multiple files (loop needed)

        $fileLocation = !$fileLocation ? self::$root . DS . self::$core['settings']['temp']['folder']  : $fileLocation ;
        $fileName = $fileLocation . $fileName;

        return move_uploaded_file($_FILES['upload']['tmp_name'][0], $fileName);
    }


    /**
     * Delete a file from the .temp folder or from the specified folder.
     *
     * @param string $fileName     Old File Name
     * @param string $fileLocation The location of the file.
     *                             If not present, the system .temp folder will be used.
     *
     * @return boolean The result of the delete process. TRUE = Success, FALSE = Failed.
     */
    public function deleteFile($fileName, $fileLocation = false)
    {
        if (empty($fileName)) return false;

        // NOTE & TODO: Must be retrofit to delete multiple files when $fileName is an array (loop needed)

        $fileLocation = !$fileLocation ? self::$root . DS . self::$core['settings']['temp']['folder']  : $fileLocation ;

        gc_collect_cycles(); // Required to free the resource and allow file to be "deletable"
        return unlink($fileLocation . $fileName);
    }


    /**
     * Rename a file in the .temp folder or in the specified folder.
     *
     * @param string $oldFileName  Old File Name
     * @param string $newFileName  New File Name
     * @param string $fileLocation The location of the file.
     *                             If not present, the system .temp folder will be used.
     *
     * @return boolean The result of the renaming process. TRUE = Success, FALSE = Failed.
     */
    public function renameFile($oldFileName, $newFileName, $fileLocation = false)
    {
        if (empty($oldFileName) || empty($newFileName)) return false;

        $fileLocation = !$fileLocation ? self::$root . DS . self::$core['settings']['temp']['folder'] : $fileLocation ;

        gc_collect_cycles(); // Required to free the resources and allow file to be "renameable"
        return rename($fileLocation . $oldFileName , $fileLocation . $newFileName);
    }


    /**
     * Creates an Encrypted Hash or Decrypts a previously Encrypted Hash
     *
     * @param  string  $string  The value to be Encrypted or previously Encrypted Hash Decrypted
     * @param  string  $action  The action of encrypt or decrypt. Default = encrypt
     *
     * @return mixed  Encrypted or Decrypted Hash depending on call
     *                FALSE if failed or if operation is not defined.
     */
    public function hashfy($string, $action = 'encrypt')
    {
        /** Default Values */
        $encrypt_mt = isset(self::$core['encryption']['method']) ? self::$core['encryption']['method'] : "AES-256-CBC";
        $secret_usr = isset(self::$core['encryption']['secret']) ? self::$core['encryption']['secret'] : "5fgf5HJ5g27";
        $secret_key = isset(self::$core['encryption']['key'])    ? self::$core['encryption']['key']    : "AA74CDCC2BBRT935136HH7B63C27";
        $sha = isset(self::$core['encryption']['sha']) ? self::$core['encryption']['sha'] : "sha256";
        $usr = substr(hash($sha, $secret_usr), 0, 16);
        $key = hash($sha, $secret_key);

        /** Process Request */
        switch ($action):
            case 'encrypt':
                $output = openssl_encrypt($string, $encrypt_mt, $key, 0, $usr);
                $output = base64_encode($output);
                break;

            case 'decrypt':
                $output = openssl_decrypt(base64_decode($string), $encrypt_mt, $key, 0, $usr);
                break;

            default:
                $output = false;

        endswitch;

        /** Return Result */
        return $output;
    }


    /**
     * Function that produces the correct AJAX response (JSON) and terminate the AJAX process
     * with a well defined response for caller. This function will use the Global variable $response.
     * The $response can be an Array or any other type of variable. If it is not an Array, this function
     * will merge its contents in the final JSON on the index ['response'].
     *
     * Note: this function will immediately TERMINATE the execution after response is sent.
     *
     * @param  mixed    $response  Can be an Array with the response Data or a simple string (even empty).
     * @param  integer  $status    The Status of the termination. I.e.: 0 = Success, 1 = Failure.
     */
    public function terminateAjax($response, $status = 0)
    {
        $response_Status['success'] = $status;
        if (is_array($response)):
            $response_Data = array_merge($response_Status, $response);
        else:
            $response_Data = array_merge($response_Status, [ 'response' => $response ]);
        endif;

        header('Content-type: application/json');
        print json_encode($response_Data);
        exit;
    }


    /**
     * This is a WRAPPER for the same name function present in the \boilerplate\installer Class
     * ----------------------------------------------------------------------------------------
     *
     * Quick HELPER function to fix slashes in crazy or misformed paths.
     *
     * This function will replace backslashes (\) by normal slashes (/),
     * then it will search for duplicates (in any quantity) and make it
     * only one.
     *
     * We use "/" instead of DIRECTORY_SEPARATOR because the current standard
     * for slashes across systems is for web address and path separation is
     * now widely accepted as slash (/), even in system that previously rely
     * on back-slashes (\). Besides, as for PHP, it can use slashes for local
     * paths AND for web addresses without distinction independent of platforms
     * (Linux/Windows/Mac), so the DIRECTORY_SEPARATOR has become irrelevant.
     *
     * @param   string  $str  The String with possible "slashes" to be corrected.
     * @return  string  With correct set of "slashes" to satisfy web-addresses and
     *                  local paths.
     */
    public function fixSlashes($str) {
        return installer::fixSlashes($str);
    }

}
