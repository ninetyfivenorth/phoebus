<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

if (isController != true): die(notController);

// == | Vars | ================================================================

$boolAMOKillSwitch = false;
$boolAMOWhiteList = false;

$arrayDatabases = array(
    'dbExtensions' => './modules/dbExtensions.php',
    'dbThemes' => './modules/dbThemes.php',
    'dbLangPacks' => './modules/dbLangPacks.php',
    'dbExternals' => './modules/dbExternals.php'
);

$strRequestAddonID = funcHTTPGetValue('id');
$strRequestAddonVersion = funcHTTPGetValue('version');
$strRequestAppID = funcHTTPGetValue('appID');
$strRequestAppVersion = funcHTTPGetValue('appVersion');
$strRequestCompatMode = funcHTTPGetValue('compatMode');

// ============================================================================

// == | funcGenerateUpdateXML | ===============================================

function funcGenerateUpdateXML($_addonManifest) {
    $_strUpdateXMLHead = '<?xml version="1.0"?>' . "\n" . '<RDF:RDF xmlns:RDF="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:em="http://www.mozilla.org/2004/em-rdf#">';
    $_strUpdateXMLTail = '</RDF:RDF>';

    header('Content-Type: text/xml');

    print($_strUpdateXMLHead);

    if ($_addonManifest != null) {
        print("\n");
        
        $_strUpdateXMLBody = file_get_contents('./components/aus/update-body.xml');

        $_arrayFilterSubstitute = array(
            '@ADDON_TYPE@' => $_addonManifest["type"],
            '@ADDON_ID@' => $_addonManifest["guid"],
            '@ADDON_VERSION@' => $_addonManifest["version"],
            '@PALEMOON_ID@' => $GLOBALS['strPaleMoonID'],
            '@ADDON_MINVERSION@' => $_addonManifest["minVer"],
            '@ADDON_MAXVERSION@' => $_addonManifest["maxVer"],
            '@ADDON_XPI@' => $_addonManifest["baseurl"] . $_addonManifest['xpi'],
            '@ADDON_HASH@' => $_addonManifest["hash"]
        );
        
        foreach ($_arrayFilterSubstitute as $_key => $_value) {
            $_strUpdateXMLBody = str_replace($_key, $_value, $_strUpdateXMLBody);
        }
        
        print("\n");
        print($_strUpdateXMLBody);
    }
    
    print($_strUpdateXMLTail);
    
    // We are done here...
    exit();
}

// ============================================================================

// == | Main | ================================================================

// funcGenerateUpdateXML(funcReadAddonManifest('extension', 'adblock-latitude', 1));

// Sanity
if ($strRequestAddonID == null || $strRequestAddonVersion == null ||
    $strRequestAppID == null || $strRequestAppVersion == null ||
    $strRequestCompatMode == null) {
    funcError('Missing minimum required arguments.');
}

if ($strRequestAppID != $strPaleMoonID) {
    funcError('Invalid Application ID');
}

// Include the database arrays
foreach($arrayDatabases as $_key => $_value) {
    include_once($_value);
}

// Search for add-ons in our databases
// Extensions
if (array_key_exists($strRequestAddonID, $arrayExtensionsDB)) {
    funcGenerateUpdateXML(funcReadAddonManifest('extension', $arrayExtensionsDB[$strRequestAddonID], 1));
}
elseif(array_key_exists($strRequestAddonID, $arrayExtensionsOverrideDB)) {
    funcGenerateUpdateXML(funcReadAddonManifest('extension', $arrayExtensionsOverrideDB[$strRequestAddonID], 1));
}
// Themes
elseif (array_key_exists($strRequestAddonID, $arrayThemesDB)) {
    funcGenerateUpdateXML(funcReadAddonManifest('theme', $arrayThemesDB[$strRequestAddonID], 1));
}
// Language Packs
elseif (array_key_exists($strRequestAddonID, $arrayLangPackDB)) {
    $arrayLangPack = array(
        'type' => 'item',
        'guid' => $strRequestAddonID,
        'xpi' => $arrayLangPackDB[$strRequestAddonID]['locale'] . '.xpi',
        'version' => $arrayLangPackDB[$strRequestAddonID]['version'],
        'minVer' => '26.0.0a1',
        'maxVer' => '26.*',
        'baseurl' => 'http://relmirror.palemoon.org/langpacks/26.x/',
        'hash' => $arrayLangPackDB[$strRequestAddonID]['hash']
    );
    
    funcGenerateUpdateXML($arrayLangPack);
}
// Externals
elseif (array_key_exists($strRequestAddonID, $arrayExternalsDB)) {
    funcRedirect($strRequestAddonID);
}
// Unknown - Send to AMO or to 'bad' update xml
else {
    if ($boolAMOKillSwitch == false) {
        include_once('./modules/nsIVersionComparator.php');
        $intVcResult = ToolkitVersionComparator::compare($strRequestAppVersion, '27.0.0a1');
        $_strFirefoxVersion = $strFirefoxVersion;
        
        if ($intVcResult < 0) {
            $_strFirefoxVersion = '24.9';
        }
        
        $strAMOLink = 'https://versioncheck.addons.mozilla.org/update/VersionCheck.php?reqVersion=2' .
        '&id=' . $strRequestAddonID .
        '&version=' . $strRequestAddonVersion .
        '&appID=' . $strFirefoxID .
        '&appVersion=' . $_strFirefoxVersion .
        '&compatMode=' . $strRequestCompatMode;
        
        funcRedirect($strAMOLink);
    }
    else {
        funcGenerateUpdateXML(null);
    }
}

// ============================================================================
?>