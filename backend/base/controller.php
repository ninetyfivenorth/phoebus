<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

// == | Vars | ================================================================

$arrayComponents = array(
    'aus' => './components/aus/aus.php',
    'download' => './components/download.php',
    'integration' => './components/integration.php',
    'metadata' => './components/metadata.php',
    'discover' => './components/discover/discover.php',
);

$arrayModules = array(
    'dbExtensions' => './modules/dbExtensions.php',
    'dbThemes' => './modules/dbThemes.php',
    'dbLangPacks' => './modules/dbLangPacks.php',
    'dbSearchPlugins' => './modules/dbSearchPlugins.php',
    'dbExternals' => './modules/dbExternals.php',
    'vc' => './modules/nsIVersionComparator.php'
);

$strRequestComponent = funcHTTPGetValue('component');

// ============================================================================

// == | Main | ================================================================

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