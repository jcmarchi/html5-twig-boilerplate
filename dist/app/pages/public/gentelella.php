<?php
if (!defined('BOILERPLATE')) die("<b>Error 4001</b>: BOILERPLATE not initialized.");

$page = "index";
$page = isset($_GET['page']) ? $_GET['page'] : $page;
$_TWIG->addGlobal('page', $page);
// $result = renderPage( $page );

$result = renderPage( "gentelella/page-samples/$page.twig" );

/**
 * Enable the line below if you want a message to be displayed when everything is disabled.
 * You can use this "feature" and modify this file for your needs.
 */
if (!$result) echo "Welcome to the Boilerplate Website.";
?>

