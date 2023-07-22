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
 * @version    1.0.2-beta 1
 *
 * @author     Julio Marchi <contact@juliomarchi.com> | Twitter: @MrMarchi
 * @copyright  See Full Header Comment Blocks at "dist/index.php"
 * @license    https://opensource.org/licenses/MIT
 */


/**
 * Quick Debug
 * : Example :
 */
if ($_['config']['debug']['enabled']):
    echo "<div style='margin:15px'><hr>DEBUGGING EXAMPLE INITIATED!<hr>";
    printf("<p style='font-size:x-small;'>Page loaded in %f seconds.</p><br>", $_['execution'] );
    if (defined('INSIGHT') && INSIGHT):
        insight($_);
        if (isset($_SESSION)) insight($_SESSION);
        insight($_SERVER);

    else:
        echo "<pre>";
        echo '<br>$_BOILERPLATE ($_) = ';
        print_r($_);
        if (isset($_SESSION)):
            echo '<br>$_SESSION = ';
            print_r($_SESSION);
        endif;
        echo '<br>$_SERVER = ';
        print_r($_SERVER);
        echo "</pre>";
    endif;
    echo "<hr>DEBUGGING COMPLETED!<hr>";
endif;
