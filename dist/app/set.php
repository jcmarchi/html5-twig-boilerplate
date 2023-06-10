<?php

/** Set Initialization Variables */
$DEBUG = false;

/** Create Array of Allowed Locations */
$locations = [
    'pages',
    'pages/common',
    'pages/public',
    'pages/static'
];

/** Define default service pages */
$welcome     = 'pages/public/welcome.php';
$maintenance = 'pages/common/maintenance.php';
$error       = 'pages/common/error.php';
