<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.
 
// == | Vars | ================================================================

// Main Entry Points
$strRequestFunction = funcHTTPGetValue('function');

 // ============================================================================

// == | Main | ================================================================

// Sanity
if ($strRequestFunction == null) {
    funcError('Missing function request');
}

if ($strRequestFunction == 'phpVars') {
    funcSendHeader('html');
    phpinfo(32);
}
elseif ($strRequestFunction == 'checkAddons') {
    $GLOBALS['boolDebugMode'] = false;
    require_once($arrayModules['dbAddons']);
    require_once($arrayModules['readManifest']);
    
    funcSendHeader('html');
    foreach ($arrayAddonsDB as $_key => $_value) {
        $_addonManifest = funcReadManifest($_value, true);
        
        if ($_addonManifest != null) {
            print($_value . ':<span style="color: green">&#09;&#09;<strong>[  OK  ]</strong></span><br />');
        }
        else {
            print($_value . ':<span style="color: red">&#09;&#09;<strong>[ FAIL ]</strong></span><br />');
        }
    }
}
else {
    funcError('Incorrect function request');
}
?>