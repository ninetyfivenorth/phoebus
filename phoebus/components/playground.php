<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL

// == | Vars | ================================================================

$strRequestMode = funcHTTPGetValue('mode');
$strRequestSlug = funcHTTPGetValue('slug');

// ============================================================================

// ============================================================================

if ($strRequestMode == null) {
    funcError('Mode is null.. Dumbass');
}

    include_once($arrayModules['readManifest']);

if ($strRequestMode == 'manifest') {
    if ($strRequestSlug == null) {
        funcError('Slug is null.. Dumbass');
    }

    header('Content-Type: text/plain');
    
    var_dump(funcReadManifest('extension', $strRequestSlug, true, true, true, true));
}
elseif ($strRequestMode == 'extensions') {
    include_once($arrayModules['dbExtensions']);
    
    foreach ($arrayExtensionsDB as $_key => $_value) {
        var_dump(funcReadManifest('extension', $strRequestSlug, true, true, true, true));
    }

}
else {
    funcError('Invalid Mode');
}

// ============================================================================
?>