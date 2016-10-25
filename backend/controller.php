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

$stringPaleMoonID = '{8de7fcbb-c55c-4fbe-bfc5-fc555c87dbc4}';
$stringFirefoxID = '{ec8030f7-c20a-464f-9b0e-13a3a9e97384}';
$sringFirefoxVersion = '24.9';

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

// == | Main | ================================================================

if (isset($_GET['function'])) {
    if (array_key_exists($_GET['function'], $arrayIncludes)) {
        if ($_GET['function'] == 'database' || $_GET['function'] == 'vc') {
            funcError('Unauthorized controller function');
        }
        else {
            include_once($arrayIncludes[$_GET['function']]);
        }
    }
    else {
        funcError($_GET['function'] . ' is an unknown controller function');
    }
}
else {
    funcError('You did not specify a controller function');
}

// ============================================================================
?>

