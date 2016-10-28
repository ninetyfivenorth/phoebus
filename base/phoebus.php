<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL

// == | Vars | ================================================================

$strPaleMoonID = '{8de7fcbb-c55c-4fbe-bfc5-fc555c87dbc4}';
$strFirefoxID = '{ec8030f7-c20a-464f-9b0e-13a3a9e97384}';
$strFirefoxVersion = '28.9';

$arrayComponents = array(
    'site' => './components/site/site.php',
    'aus' => './components/aus/aus.php',
    'download' => './components/download.php',
    'integration' => './components/integration.php',
    'metadata' => './components/metadata.php',
    'discover' => './components/discover/discover.php',
    'playground' => './components/playground.php'
);

$arrayModules = array(
    'dbExtensions' => './modules/dbExtensions.php',
    'dbThemes' => './modules/dbThemes.php',
    'dbLangPacks' => './modules/dbLangPacks.php',
    'dbSearchPlugins' => './modules/dbSearchPlugins.php',
    'dbExternals' => './modules/dbExternals.php',
    'readManifest' => './modules/funcReadManifest.php',
    'vc' => './modules/nsIVersionComparator.php'
);

$strRequestComponent = funcHTTPGetValue('component');
$strRequestPath = funcHTTPGetValue('path');

// ============================================================================

// == | Main | ================================================================

var_dump(array_search("component",array_keys($_GET)));

if ($_SERVER['REQUEST_URI'] == '/') {
    $strRequestComponent = 'site';
    $strRequestPath = '/';
}
elseif ($strRequestComponent != 'site' && $strRequestPath != null) {
    header("HTTP/1.0 404 Not Found");
    exit();
}

// Load component based on strRequestComponent
if ($strRequestComponent != null) {
    if (array_key_exists($strRequestComponent, $arrayComponents)) {
        include_once($arrayComponents[$strRequestComponent]);
    }
    else {
        funcError($strRequestComponent . ' is an unknown component');
    }
}
else {
    funcError('You did not specify a component');
}

// ============================================================================
?>