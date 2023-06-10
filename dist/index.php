<?php
/**
 * Capture execution time started
 */
$s = microtime(true);

/**
 * Initialize Boilerplate (REQUIRED)
 */
require "vendor/boilerplate/boilerplate.start.php";

/**
 * After initialized, the execution of the program will be
 * transferred to the /app/app.php file.
 *
 * Eventually, execution may return to this file, assuming no
 * loop or early termination is triggered.
 *
 * If using this file for additional tasks, make sure the
 * execution will resume here. Otherwise, use the /app/app.php instead.
 */

 /**
 * Capture execution time ended and print complete execution time.
 */
$e = microtime(true);
printf("<span style='font-size:xx-small; margin-left: 20px; color:#8a8a8a;'>Page loaded in %f seconds.</span>", $e-$s);
