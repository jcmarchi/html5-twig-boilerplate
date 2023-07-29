<?php
if (!defined('BOILERPLATE')) die("<b>Error 4001</b>: BOILERPLATE not initialized.");
$page = "welcome";
$page = isset($_GET['clean']) ? "_clean-example" : $page;
$page = isset($_GET['plain']) ? "_page-example"  : $page;
$result = renderPage( $page );

/**
 * Enable the line below if you want a message to be displayed when everything is disabled.
 * You can use this "feature" and modify this file for your needs.
 */
if (!$result) echo "Welcome to the Boilerplate Website.";
?>

