<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL

// == | Vars | ================================================================

$arrayIncludes = array(
    $arrayModules['dbExtensions'],
    $arrayModules['dbThemes'],
    $arrayModules['dbLangPacks'],
    $arrayModules['readManifest'],
);

$arrayPermaXPI = array(
    'abl' => '{016acf6d-e5c0-4768-9376-3763d1ad1978}',
    'devtools' => 'devtools@addons.palemoon.org'
);

$strRequestAddonID = funcHTTPGetValue('id');

// ============================================================================

// == | funcDownloadXPI | ===============================================

function funcDownloadXPI($_addonManifest) {
    if ($_addonManifest['isNewManifest'] == false) {
        $_addonFile = $_addonManifest['basepath'] . $_addonManifest['xpi'];
        
        if (file_exists($_addonFile)) {
            header('Content-Type: application/x-xpinstall');
            header('Content-Disposition: inline; filename="' . $_addonManifest['xpi'] .'"');
            header('Content-Length: ' . filesize($_addonFile));
            header('Cache-Control: no-cache');
            
            readfile($_addonFile);
        }
        else {
            funcError('XPI file not found');
        }

        // We are done here
        exit();
    }
}

// ============================================================================

// == | funcDownloadSearchPlugin | ============================================

function funcDownloadSearchPlugin($_searchPluginName) {
    $_SearchPluginFile = './datastore/searchplugins/' . $_searchPluginName . '.xml';
    
    if (file_exists($_SearchPluginFile)) {
        header('Content-Type: text/xml');
        header('Cache-Control: no-cache');
        
        readfile($_SearchPluginFile);
    }
    else {
        funcError('Search Plugin XML file not found');
    }
    
    // We are done here
    exit();
}

// ============================================================================

// == | Main | ================================================================

// Sanity
if ($strRequestAddonID == null) {
    funcError('Missing minimum required arguments.');
}

// Includes
foreach($arrayIncludes as $_value) {
    include_once($_value);
}
unset($arrayIncludes);

// Special case for PermaXPI links
// Override $strRequestAddonID
if (array_key_exists($strRequestAddonID, $arrayPermaXPI)) {
    $strRequestAddonID = $arrayPermaXPI[$strRequestAddonID];
}

// Search for add-ons in our databases
// Extensions
if (array_key_exists($strRequestAddonID, $arrayExtensionsDB)) {
    funcDownloadXPI(funcReadManifest('extension', $arrayExtensionsDB[$strRequestAddonID], 2, false));
}
// Themes
elseif (array_key_exists($strRequestAddonID, $arrayThemesDB)) {
    funcDownloadXPI(funcReadManifest('theme', $arrayThemesDB[$strRequestAddonID], 2, false));
}
// Search Plugins
elseif (array_key_exists($strRequestAddonID, $arraySearchPlugins)) {
    funcDownloadSearchPlugin($arraySearchPlugins[$strRequestAddonID]);
}
else {
    funcError('Add-on could not be found in our database');
}

// ============================================================================
?>