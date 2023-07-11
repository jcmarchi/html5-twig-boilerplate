<?php
/**
 * THE MULTI-PURPOSE WEBSITE BOILERPLATE WITH TWIG SUPPORT
 * Boilerplate INDEX.PHP
 *
 * This file simply loads the 'vendor/boilerplate/boilerplate.start.php'.
 *
 * This file can be modified as needed, and the above listed file can be included
 * at any point, depending on project needs.
 *
 * @category  Starter
 * @version   1.1.0-beta 2
 * @see       docs/TOC.md
 *
 * PHP version 8+ (may work with PHP 7+ and 5.4+ versions. Warnings may be issued!).
 *
 * Created & Developed by Quantum Leap Innovations, LLC | QLi: qlitp.com
 * In association with Global COMPEL International, LLC | GCi: gcompel.com
 * Proudly Maintained by MSX Holding & Enterprises, LLC | MSX: msxholding.com
 *
 * @copyright Quantum Leap Innovations, LLC | QLi: quantumleap-innovations.com
 * @copyright Marchi & Associates, LLC      | M&A: marchiandassociates.com
 *
 * @see       MOODFIRED is the QLi Official Brand for Technology Products & Projects.
 * @link      moodfired.com | moodfired.net | moodfired.org
 *
 * @author    Julio Marchi   <contact@juliomarchi.com> | Twitter: @MrMarchi
 * @support   Ralph Wolfgang <mr.skanner@gmail.com>    | Twitter: @mrSkanner
 * @thanksto  Special Thanks to Eliazer Kosciuk [KLAX] | Twitter: @klaxmsx
 *
 * LICENSED UNDER THE MIT LICENSE.
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software  and  associated  documentation  files (the "Software"), to
 * deal in the Software  without  restriction, including without limitation the
 * rights  to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell  copies of the Software, and to permit  persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 * @license    https://opensource.org/licenses/MIT
 */


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
