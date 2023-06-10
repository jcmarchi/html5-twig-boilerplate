<?php

/**
 * Checks if the string is an e-mail
 *
 * @param  String   $email  A variable to be checked if it has an email
 *
 * @return Boolean          Returns TRUE for if email is found, FALSE otherwise.
 */
function is_email(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}


/**
 * Checks if the received variable represents a possible boolean value.
 *
 * The function is case insensitive.
 *
 * @param  Mixed    $variable  Can be anything (string, bol, integer, etc.)
 *
 * @return Boolean             Returns TRUE  for "1", "true", "on" and "yes"
 *                             Returns FALSE for "0", "false", "off" and "no"
 *                             Returns NULL otherwise.
 */
function is_enabled($variable): bool
{
    if (!isset($variable)) return null;
    return filter_var($variable, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
}


/**
 * Search array items based on multiple array items.
 *
 * @param  Array  $array      The subject Array that will be searched.
 * @param  Array  $search     An Array with the items being searched.
 * @param  Bool   $caseCheck  Case Sensitiveness. Default = false.
 *
 * @return Array              Returns an Array with the KEYS of the items found.
 */
function multi_array_search(array $array, array $search, bool $caseCheck = false): array
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
 * @param  Array  $array     Array subject to be modified
 * @param  Array  $function  Function to be applied to each item of the Array
 *
 * @return Array             Modified Array

 */
function multi_array_change(array $array, string $function = 'trim'): array
{
    return array_map($function, $array);
}


/**
 * Finds the value of a specific [$index] of an array based on requested search.
 *
 * @param  Mixed   $needle    The string to be searched.
 * @param  Array   $haystack  The Array to be searched at.
 * @param  Mixed   $index     The [$index] where $needle must be searched for.
 * @param  String  $content   Define the result. TRUE: returns content, FALSE: returns index. Default: false.
 *
 * @return Mixed              Returns either content or value of ['id'] based on $content. Returns NULL if nothing is found.
 */
function find($needle, array $haystack, $index, bool $content = false)
{
    $find = multi_array_search($haystack, [ $index => $needle ] );

    if (empty($find)) return NULL;

    return $content ? $haystack[ $find[0] ][$index] : $haystack[ $find[0] ]['id'];
}


/**
 * Gets client's IP address
 *
 * @return  String  Identified Client's Real IP Address
 */
function get_ip()
{
    // Check for shared Internet/ISP IP
    if (!empty($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }

    // Check for IP addresses passing through proxies
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {

        // Check if multiple IP addresses exist in var
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

    // Return unreliable IP address since all else failed
    return $_SERVER['REMOTE_ADDR'];
}


/**
 * Check if given IP is equal or belongs to the referenced group.
 * @param  String  $ip     The IP to be checked
 * @param  String  $range  The "object" IP must validate against, which can be:
 *                            - Single IP in a string format
 *                            - Classless Inter-Domain Routing (CIDR) format
 *                              I.e.: 192.168.1.1-192.168.1.50/32
 *                            - A IP range separated by DASH (-).
 *                              I.e.: 192.168.1.1-192.168.1.50
 *
 * @return Boolean         True if IP is in the range. False if it isn't.
 */
function check_ip($ip, $range)
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
 * @param  Float   $millisecond the value to be converted.
 * @param  Integer $precision   defines the desired precision for numeric values in the response. Default = 5.
 *
 * @return Array with several possible strings to be used in different situations ort combined:
 *
 *              [msec] => The "millisecond" submitted with "ms" added:
 *                        I.e.: 7654329.458934 ms
 *              [time] => The human readable time (only):
 *                        I.e.: 2126 hours, 12 minutes and 9 seconds
 *              [days] => If millisecond are more than a day, return numbers of days.
 *                        I.e.: 88 days
 *              [full] => Pre-composed string (human readable + days).
 *                        I.e.: 2126 hours, 12 minutes and 9 seconds (more than 88 days)
 *              [precise] => Pre-composed string (millisecond + human readable + days).
 *                           I.e.: 7654329.458934 ms. = 2126 hours, 12 minutes and 9 seconds (more than 88 days)
 */
function format_time($millisecond, $precision = 5)
{
    /**
     * Calculate all the required time values based on received "$millisecond"
     */
    $day     = floor($millisecond / 86400);
    $hours   = floor($millisecond / 3600);
    $minutes = ($millisecond / 60) % 60;
    $seconds = $millisecond % 60;

    /**
     * Format string pieces based on previous calceulated time values
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
 * Format a given value in bytes to human readable format
 * @param  Integer  $size  The number of bytes
 * @param  Boolean  $iec   If TRUE, use 1998 "International Electrotechnical Commission" (IEC) ISO/IEC 80000 unit of measurement format, based
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
 * @return String          The human readable formatted unit
 */
function format_bytes($size, $iec = true)
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
 * @param String $fileName      Old File Name.
 * @param String $fileLocation  The location of the file. If it is not present,
 *                              the system .temp folder will be used.
 *
 * @return Boolean The result of the uploading process can be:
 *                 TRUE  = Success
 *                 FALSE = Failed
 *                 NULL  = No Uploaded file exist or upload process failed
 */
function uploadFile($fileName, $fileLocation = false)
{
    if (empty($fileName)) return false;
    if (empty($_FILES)) return null;
    if (!isset($_FILES['upload']['tmp_name'][0])) return null;

    // NOTE: Must be retrofit for multiple files (loop needed)

    $fileLocation = !$fileLocation ? siteClass::$root . "/webaccess/.temp/" : $fileLocation ;
    $fileName = $fileLocation . $fileName;

    return move_uploaded_file($_FILES['upload']['tmp_name'][0], $fileName);
}

/**
 * Delete a file from the .temp folder or from the specified folder.
 *
 * @param String $fileName  Old File Name
 * @param String $fileLocation The location of the file.
 *                             If not present, the system .temp folder will be used.
 *
 * @return Boolean The result of the delete process. TRUE = Success, FALSE = Failed.
 */
function deleteFile($fileName, $fileLocation = false)
{
    if (empty($fileName)) return false;

    // NOTE: Must be retrofit to delete multiple files when $fileName is an array (loop needed)

    $fileLocation = !$fileLocation ? siteClass::$root . "/webaccess/.temp/" : $fileLocation ;

    gc_collect_cycles(); // Required to free the resource and allow file to be "deletable"
    return unlink($fileLocation . $fileName);
}

/**
 * Rename a file in the .temp folder or in the specified folder.
 *
 * @param String $oldFileName  Old File Name
 * @param String $newFileName  New File Name
 * @param String $fileLocation The location of the file.
 *                             If not present, the system .temp folder will be used.
 *
 * @return Boolean The result of the renaming process. TRUE = Success, FALSE = Failed.
 */
function renameFile($oldFileName, $newFileName, $fileLocation = false)
{
    if (empty($oldFileName) || empty($newFileName)) return false;

    $fileLocation = !$fileLocation ? siteClass::$root . "/webaccess/.temp/" : $fileLocation ;

    gc_collect_cycles(); // Required to free the resources and allow file to be "renameable"
    return rename($fileLocation . $oldFileName , $fileLocation . $newFileName);
}

/**
 * Creates an Encrypted Hash or Decrypts a previously Encrypted Hash
 *
 * @param String $string The value to be Encrypted or previously Encrypted Hash Decrypted
 * @param String $action The action of encrypt or decrypt. Default = encrypt
 *
 * @return String Encrypted or Decrypted Hash depending on call
 */

function hashfy($string, $action = 'encrypt')
{
    $encrypt_method = "AES-256-CBC";
    $secret_key = 'AA74CDCC2BBRT935136HH7B63C27'; // user define private key
    $secret_iv = '5fgf5HJ5g27'; // user define secret key
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16); // sha256 is hash_hmac_algo
    if ($action == 'encrypt') {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } else if ($action == 'decrypt') {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}


/**
 * Function that produces the correct AJAX response (JSON) and terminate the AJAX process
 * with a well defined response for caller. This function will use the Global variable $responseData.
 * The $responseData can be an Array or any other type of variable. If it is not an Array, this function
 * will merge its contents in the final JSON on the index ['response'].
 *
 * Note: this function will immediately TERMINATE the execution after response is sent.
 *
 * @param Integer $status The Status of the termination. I.e.: 0 = Success, 1 = Failure
 *
 * @return N/A This function terminates the execution after sending JSON to caller.
 */
function terminateAjax($status = 0) {
    global $responseData;

    $responseStatus['success'] = $status;
    if (is_array($responseData)):
        $finalResponse = array_merge($responseStatus, $responseData);
    else:
        $finalResponse = array_merge($responseStatus, [ 'response' => $responseData ]);
    endif;

    header('Content-type: application/json');
    print json_encode($finalResponse);
    exit;
}

/** Global Variable used by the terminateAjax function */
$responseData = [];

