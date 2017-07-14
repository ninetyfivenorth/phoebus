<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL

// == | application.php | =====================================================

// This file defines the general entry point of the application. It should not
// contain more than the basic operational logic for defining universal
// variables, components, and modules as well the default component

// ============================================================================

// == | Vars | ================================================================

// Basic Application defines
$strPhoebusLiveURL = 'addons.palemoon.org';
$strPhoebusDevURL = 'dev.addons.palemoon.org';
$strPhoebusURL = $strPhoebusLiveURL;
$strPhoebusSiteName = 'Pale Moon - Add-ons';
$strPhoebusVersion = '1.6.0a1';
$strPhoebusDatastore = './datastore/';
$boolDebugMode = false;

// Known Client GUIDs
$strPaleMoonID = '{8de7fcbb-c55c-4fbe-bfc5-fc555c87dbc4}';
$strFossaMailID = '{3550f703-e582-4d05-9a08-453d09bdfdc6}';
$strFirefoxID = '{ec8030f7-c20a-464f-9b0e-13a3a9e97384}';
$strThunderbirdID = $strFossaMailID; // {3550f703-e582-4d05-9a08-453d09bdfdc6}
$strSeaMonkeyID = '{92650c4d-4b8e-4d2a-b7eb-24ecf4f6b63a}';
$strApplicationID = $strPaleMoonID;

// XXX: Pale Moon only backwards compatiblity with "Independence Era"
$strMinimumApplicationVersion = '27.0.0';
$strFirefoxVersion = '27.9';
$strFirefoxOldVersion = '24.9';

// Pale Moon Language Packs BASE URL
// XXX: Should move this to the language pack db
$strLangPackBaseURL = 'http://rm-eu.palemoon.org/langpacks/27.x/';

// $_GET and Path Magic
$strRequestComponent = funcHTTPGetValue('component');
$arrayArgsComponent = preg_grep('/^component=(.*)/', explode('&', parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY)));
$strRequestPath = funcHTTPGetValue('path');

// Define application paths
$strApplicationPath = $_SERVER['DOCUMENT_ROOT'] . '/phoebus/';
$strComponentsPath = $strApplicationPath . 'components/';
$strModulesPath = $strApplicationPath . 'modules/';
$strGlobalLibPath = $_SERVER['DOCUMENT_ROOT'] . '/lib/';

// Define Components
$arrayComponents = array(
    'site' => $strComponentsPath . 'site/site.php',
    'aus' => $strComponentsPath . 'aus/aus.php',
    'download' => $strComponentsPath . 'download/download.php',
    'integration' => $strComponentsPath . 'integration/integration.php',
    'discover' => $strComponentsPath . 'discover/discover.php',
    'license' => $strComponentsPath . 'license/license.php',
    '43893' => $strComponentsPath . 'special/special.php'
);

// Define Modules
$arrayModules = array(
    'basicFunctions' => $strModulesPath . 'basicFunctions.php',
    'readManifest' => $strModulesPath . 'funcReadManifest.php',
    'processContent' => $strModulesPath . 'funcProcessContent.php',
    'vc' => $strGlobalLibPath . 'nsIVersionComparator.php',
    'smarty' => $strGlobalLibPath . 'smarty/Smarty.class.php'
);

// Define Database Arrays
// XXX: These will be merged into arrayModules until they go away with SQL
$arrayDatabases = array(
    'dbAddons' => $strModulesPath . 'db/' . 'addons.php',
    'dbLangPacks' => $strModulesPath . 'db/' . 'langPacks.php',
    'dbSearchPlugins' => $strModulesPath . 'db/' . 'searchPlugins.php',
    'dbAUSExternals' => $strModulesPath . 'db/' . 'ausExternals.php',
    'dbCategories' => $strModulesPath . 'db/' . 'categories.php'
);

// ============================================================================

// == | Main | ================================================================

// Merge the databases and modules
$arrayModules = array_merge($arrayModules, $arrayDatabases);
unset($arrayDatabases);

// Include basic functions
require_once($arrayModules['basicFunctions'];

// Define a Debug/Developer Mode
// XXX: This should REALLY be a function
if ($_SERVER['SERVER_NAME'] == $strPhoebusDevURL) {
    $boolDebugMode = true;
    $strPhoebusURL = $strPhoebusDevURL;
    if (file_exists('./.git/HEAD')) {
        $_strGitHead = file_get_contents('./.git/HEAD');
        $_strGitSHA1 = file_get_contents('./.git/' . substr($_strGitHead, 5, -1));
        $_strGitBranch = substr($_strGitHead, 16, -1);
        $strPhoebusSiteName = 'Phoebus Development - Version: ' . $strPhoebusVersion . ' - ' .
            'Branch: ' . $_strGitBranch . ' - ' .
            'Commit: ' . substr($_strGitSHA1, 0, 7);
    }
    else {
        $strPhoebusSiteName = 'Phoebus Development - Version: ' . $strPhoebusVersion;
    }
    error_reporting(E_ALL);
    ini_set("display_errors", "on");
}

// Deal with unwanted entry points
if ($_SERVER['REQUEST_URI'] == '/') {
    $strRequestComponent = 'site';
    $strRequestPath = '/';
}
elseif ((count($arrayArgsComponent) > 1) || ($strRequestComponent != 'site' && $strRequestPath != null)) {
    funcSendHeader('404');
    exit();
}

// Load component based on strRequestComponent
if ($strRequestComponent != null) {
    if (array_key_exists($strRequestComponent, $arrayComponents)) {
        require_once($arrayComponents[$strRequestComponent]);
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