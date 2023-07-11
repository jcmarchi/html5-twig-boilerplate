<?php
/**
 * THE MULTI-PURPOSE WEBSITE BOILERPLATE WITH TWIG SUPPORT
 * Boilerplate Page Constructor :: DEBUGGER
 *
 * This file is a simple example of a DEBUGGER that can display valuable
 * information about the Boilerplate constants and variables.
 *
 * This file is only loaded if the constant DEBUG is set to "true".
 *
 * For a more advanced and comprehensive debugging and inspector experience
 * install the MOODFIRED Module Insight: https://github.com/jcmarchi/Insight.
 *
 * Coming Soon: OmniLOG, byb MOODFIRED.
 *
 * For complete description and further information, @see MANUAL at docs/TOC.md.
 *
 * @since      July, 2023.
 * @category   Debugger
 * @version    1.0.0-beta 1
 *
 * @author     Julio Marchi <contact@juliomarchi.com> | Twitter: @MrMarchi
 * @copyright  See Full Header Comment Blocks at "dist/index.php"
 * @license    https://opensource.org/licenses/MIT
 */


/**
 * Quick Debug
 * : Example :
 */
if (DEBUG):
    echo "<pre><hr>DEBUGGING EXAMPLE INITIATED!<hr>";

    echo 'DS = ' . DS . "<br>";
    echo 'APP = ' . APP . "<br>";
    echo 'ROOT = ' . ROOT . "<br>";
    echo 'DEBUG = ' . DEBUG . "<br>";
    echo 'CONFIG = ' . CONFIG . "<br>";
    echo 'AUTORENDER = ' . AUTORENDER . "<br>";

    echo 'ENVIRONMENT = ' . ENVIRONMENT . "<br>";
    echo 'COMPOSER = ' . COMPOSER . "<br>";
    echo 'TWIG = ' . TWIG . "<br>";
    echo 'MAINTENANCE = ' . MAINTENANCE . "<br>";
    echo 'BOILERPLATE = ' . BOILERPLATE . "<br>";

    echo 'siteClass::$config = ';
    print_r(siteClass::$config);

    echo "<hr>DEBUGGING COMPLETED!<hr></pre>";
endif;