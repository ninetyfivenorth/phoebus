<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL

// == | Vars | ================================================================

$strRequestMode = funcHTTPGetValue('mode');
$strRequestMode = funcHTTPGetValue('slug');

// ============================================================================

function funcConvertAddon($_type, $_slug) {
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
    $addonContent = html_entity_decode($addonContent);
    $addonContent = str_replace('\'', '"', $addonContent);
    $addonContent = str_replace('<p>', '', $addonContent);
    $addonContent = str_replace('<br />', "\n", $addonContent);
    $addonContent = str_replace('<br>', "\n", $addonContent);
    $addonContent = str_replace('</p>', "\n", $addonContent);
    $addonContent = str_replace('<ul>', "[ul]", $addonContent);
    $addonContent = str_replace('</ul>', "[/ul]", $addonContent);
    $addonContent = str_replace('<li>', "[li]", $addonContent);
    $addonContent = str_replace('</li>', "[/li]", $addonContent);
    $addonContent = str_replace('<h3>', "[section]", $addonContent);
    $addonContent = str_replace('</h3>', "[/section]", $addonContent);
    $addonContent = str_replace('<h3>', "[/h3]", $addonContent);
    $addonContent = str_replace('<b>', "[b]", $addonContent);
    $addonContent = str_replace('</b>', "[/b]", $addonContent);
    $addonContent = str_replace('<strong>', "[/b]", $addonContent);
    $addonContent = str_replace('<i>', "[i]", $addonContent);
    $addonContent = str_replace('</i>', "[/i]", $addonContent);
    $addonContent = str_replace('<em>', "[i]", $addonContent);
    $addonContent = str_replace('</em>', "[/i]", $addonContent);
    $addonContent = str_replace('<u>', "[/u]", $addonContent);
    $addonContent = str_replace('</u>', "[/u]", $addonContent);
    $addonContent = str_replace('</a>', "[/url]", $addonContent);
    $addonContent = str_replace(' target=_blank', '', $addonContent);
    $addonContent = preg_replace('/<a href=(.*)>/Ui', '[url=$1]', $addonContent);
    $addonContent = preg_replace('/\[url=\"(.*)\"\]/Ui', '[url=$1]', $addonContent);
    $addonContent = str_replace("\"", "'", $addonContent);
    return array($addonNewManifestINI, $addonContent);

}

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
    
    var_dump(funcReadManifest('extension', $strRequestSlug, 0, true));
}
else {
    funcError('Invalid Mode');
}

// ============================================================================
?>