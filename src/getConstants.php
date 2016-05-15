<?php
/**
 * Send PHP-Constant definitions to JavaScript, so they are available there for usage too
 */


require_once "config.inc.php";
require_once "../vendor/autoload.php";


$constats = get_defined_constants( $categorize = true );
$userConstants = $constats['user'];

// return answer to JavaScript
header("Content-Type: application/json");
die( "var rsc = " . json_encode($userConstants) . ";" );
