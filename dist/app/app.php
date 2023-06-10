<?php

/** Quick Debug */
if (DEBUG):
    echo '<pre>';
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

    print_r(siteClass::$config);

    echo "<br><br>COMPLETE!<br>";
endif;

/** Check if page requested is in the Allowed Locations Array */
$page = checkVirtualPage($locations);

/** Find what to load based on request and config, then load it */
if ($page===null):
    require $maintenance;
elseif ($page===true):
    require $welcome;
elseif ($page===false):
    require $error;
else:
    require_once $page;
endif;