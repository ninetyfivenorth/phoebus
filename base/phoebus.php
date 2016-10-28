<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL

// == | Vars | ================================================================

$strPaleMoonID = '{8de7fcbb-c55c-4fbe-bfc5-fc555c87dbc4}';
$strFirefoxID = '{ec8030f7-c20a-464f-9b0e-13a3a9e97384}';
$strFirefoxVersion = '28.9';

$arrayComponents = array(
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
$strRequestPage = funcHTTPGetValue('page');

// ============================================================================

// == | Main | ================================================================

// Load component based on strRequestComponent
if ($strRequestPage == null) {
    if ($strRequestComponent != null) {
        if (array_key_exists($strRequestComponent, $arrayComponents)) {
            include_once($arrayComponents[$strRequestComponent]);
        }
        else {
            funcError($strRequestComponent . ' is an unknown component');
        }
    }
    else {
        funcError('You did not specify a page request or component');
    }
}
else {
    header('Content-Type: text/plain');
    print($_SERVER['REQUEST_URI'] . "\n" . $strRequestPage . "\n");
    $exploded = explode('/', implode('/', array_filter(explode('/', $strRequestPage))));
    if ($exploded == '' || $exploded == null) {
        print('And I think to myself, what a wonderful world.. WHY ARE YOU HERE?!' . "\n");
    }
    var_dump($exploded);
}
// ============================================================================
?>