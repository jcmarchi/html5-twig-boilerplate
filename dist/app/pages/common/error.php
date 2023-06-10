<?php
if (!defined('BOILERPLATE')) die("<b>Error 4001</b>: BOILERPLATE not initialized.");
$result = renderPage('error', ['ErrorCode' => '404']);

/**
 * Enable the line below if you want a message to be displayed when everything is disabled.
 * You can use this "feature" and modify this file for your needs.
 */
if (!$result) echo "Page not found or an unrecoverable error has occurred. Aborting.";
?>