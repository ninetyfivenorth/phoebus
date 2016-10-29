<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL

// == | Vars | ================================================================

$strRequestMode = funcHTTPGetValue('mode');

// ============================================================================

function fumcConvertAddon($_type, $_slug) {
    $addonManifest = funcReadManifest($_type, $_slug, 0, false);
    
    $addonNewManifestINI = '[addon]
type="' . $addonManifest['type'] . '"
id="' . $addonManifest['guid'] . '"
release="' . $addonManifest['xpi'] . '"
unstable="none"

[metadata]
name="' . $addonManifest['name'] . '"
slug="' . $_slug . '"
author="' . $addonManifest['author'] . '"
shortDescription="' . 'unknown' . '"
licence="none"
homepageURL="none"
supportURL="none"

[' . $addonManifest['xpi'] . ']
version="' . $addonManifest['version'] . '"
minAppVersion="' . $addonManifest['minVer'] . '"
maxAppVersion="' . $addonManifest['maxVer'] . '"';

    $addonContent = $addonManifest["description"];
    $addonContent = str_replace('<p>', '', $addonContent);
    $addonContent = str_replace('<br />', "\n", $addonContent);
    $addonContent = str_replace('</p>', "\n", $addonContent);
    
    var_dump(array($addonNewManifestINI, $addonContent));

}

// ============================================================================

include_once($arrayModules['readManifest']);

if ($strRequestMode == null) {
    funcError('Mode is null.. Dumbass');
}

if ($strRequestMode == 'manifest') {
    header('Content-Type: text/plain');
    funcConvertAddon('extension', 'adblock-latitude');

}
elseif ($strRequestMode == 'convert') {
    header('Content-Type: text/plain');
    

}
else {
    funcError('Invalid Mode');
}

// ============================================================================
?>