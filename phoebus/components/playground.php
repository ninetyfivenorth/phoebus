<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL

// == | Vars | ================================================================

$strRequestMode = funcHTTPGetValue('mode');

// ============================================================================

// ============================================================================

include_once($arrayModules['readManifest']);

if ($strRequestMode == null) {
    funcError('Mode is null.. Dumbass');
}

if ($strRequestMode == 'manifest') {
    header('Content-Type: text/plain');
    
    var_dump(funcReadManifest('extension', 'adblock-latitude', 0, true));
}
elseif ($strRequestMode == 'convert') {
    header('Content-Type: text/plain');
    
    $addonManifest = funcReadManifest('extension', 'adblock-latitude', 0, false);
    var_dump($addonManifest);
    print("\n\n\n");
    $addonNewManifest = array(
        'addon' => array(
                    'type' => $addonManifest['type'],
                    'id' => $addonManifest['guid'],
                    'release' => $addonManifest['xpi'],
                    'unstable' => 'none')
        'metadata' => array(
                    'name' => $addonManifest['name'],
                    'slug' => 'unknown',
                    'author' => ,
                    'shortDescription' => 'none',
                    'licence' => 'none',
                    'homepageURL' => 'none',
                    'supportURL' => 'none')
        'xpi' => array(
                    $addonManifest['xpi'] => array(
                        'version' => $addonManifest['version'],
                        'minAppVersion' => $addonManifest['minVer'],
                        'maxAppVersion' => $addonManifest['minVer'])),
    );
    print("\n\n\n");
    
}
else {
    funcError('Invalid Mode');
}

// ============================================================================
?>