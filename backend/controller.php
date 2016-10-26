<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

// == | Debug | ===============================================================

// Uncomment to enable
error_reporting(E_ALL);
ini_set("display_errors", "on");

// ============================================================================

// == | Vars | ================================================================

$strPaleMoonID = '{8de7fcbb-c55c-4fbe-bfc5-fc555c87dbc4}';
$strFirefoxID = '{ec8030f7-c20a-464f-9b0e-13a3a9e97384}';
$strFirefoxVersion = '24.9';

$arrayIncludes = array(
    'aus' => './modules/aus.php',
    'download' => './modules/download.php',
    'integration' => './modules/integration.php',
    'metadata' => './modules/metadata.php',
    'discover' => './modules/discover.php',
    'database' => './modules/database.php',
    'vc' => '../lib/vc/nsIVersionComparator.php'
);

// ============================================================================

// == | Function: funcError |==================================================

function funcError($_value) {
    die('Error: ' . $_value);
}

// ============================================================================

// == | Function: funcHTTPGetValue |===============================================

function funcHTTPGetValue($_value) {
    if (!isset($_GET[$_value]) || $_GET[$_value] === '' || $_GET[$_value] === null || empty($_GET[$_value])) {
        return null;
    }
    else {    
        return $_GET[$_value];
    }
}

// ============================================================================

// == | Main | ================================================================

$strRequestFunction = funcHTTPGetValue('function');

if (!$strRequestFunction) {
    if ((array_key_exists($strRequestFunction, $arrayIncludes)) && ($strRequestFunction != 'database' || $strRequestFunction != 'vc')) {
        include_once($arrayIncludes[$strRequestFunction]);
    }
    else {
        funcError($strRequestFunction . ' is an unknown controller function');
    }
}
else {
    funcError('You did not specify a controller function');
}

// ============================================================================
?>

