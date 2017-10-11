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
$strApplicationLiveURL = 'addons.palemoon.org';
$strApplicationDevURL = 'addons-dev.palemoon.org';
$strApplicationURL = $strApplicationLiveURL;
$strApplicationSiteName = 'Pale Moon - Add-ons';
$strApplicationVersion = '1.6.1';
$strApplicationSkin = 'palemoon';
$boolDebugMode = false;

// Define application paths
$strRootPath = $_SERVER['DOCUMENT_ROOT'];
$strGlobalLibPath = $strRootPath . '/lib/';
$strObjDirPath = $strRootPath . '/.obj/';
$strApplicationDatastore = './datastore/';
$strApplicationPath = $strRootPath . '/frontend/';
$strComponentsPath = $strApplicationPath . 'components/';
$strModulesPath = $strApplicationPath . 'modules/';
$strDatabasesPath = $strRootPath . '/db/';

// Define Libs
$arrayLibs = array(
    'vc' => $strGlobalLibPath . 'nsIVersionComparator.php',
    'smarty' => $strGlobalLibPath . 'smarty/Smarty.class.php',
    'rdf' => $strGlobalLibPath . 'rdf/RdfComponent.php',
);

// Define Database Arrays
$arrayDatabases = array(
    'dbAddons' => $strDatabasesPath . 'addons.php',
    'dbLangPacks' => $strDatabasesPath . 'langPacks.php',
    'dbSearchPlugins' => $strDatabasesPath . 'searchPlugins.php',
    'dbAUSExternals' => $strDatabasesPath . 'ausExternals.php',
    'dbCategories' => $strDatabasesPath . 'categories.php'
);

// Define Components
$arrayComponents = array(
    'site' => $strComponentsPath . 'site/site.php',
    'aus' => $strComponentsPath . 'aus.php',
    'discover' => $strComponentsPath . 'discover/discover.php',
    'download' => $strComponentsPath . 'download.php',
    'integration' => $strComponentsPath . 'integration.php',
    'license' => $strComponentsPath . 'license.php',
    '43893' => $strComponentsPath . 'special/special.php'
);

// Define Modules
$arrayModules = array(
    'readManifest' => $strModulesPath . 'funcReadManifest.php'
);

// Known Client GUIDs
$strPaleMoonID = '{8de7fcbb-c55c-4fbe-bfc5-fc555c87dbc4}';
$strFossaMailID = '{3550f703-e582-4d05-9a08-453d09bdfdc6}';
$strFirefoxID = '{ec8030f7-c20a-464f-9b0e-13a3a9e97384}';
$strThunderbirdID = $strFossaMailID; // {3550f703-e582-4d05-9a08-453d09bdfdc6}
$strSeaMonkeyID = '{92650c4d-4b8e-4d2a-b7eb-24ecf4f6b63a}';
$strClientID = $strPaleMoonID;

// XXX: Pale Moon only backwards compatiblity with "Independence Era"
$strMinimumApplicationVersion = '27.0.0';
$strFirefoxVersion = '27.9';
$strFirefoxOldVersion = '24.9';

// $_GET and Path Magic
$strRequestComponent = funcHTTPGetValue('component');
$arrayArgsComponent = preg_grep('/^component=(.*)/', explode('&', parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY)));
$strRequestPath = funcHTTPGetValue('path');
$strRequestSmartyDebug = funcHTTPGetValue('smartyDebug');

// ============================================================================

// == | Main | ================================================================

// Merge Libs and Databases into Modules then unset
$arrayModules = array_merge($arrayModules, $arrayLibs);
unset($arrayLibs);

$arrayModules = array_merge($arrayModules, $arrayDatabases);
unset($arrayDatabases);

// Define a Debug/Developer Mode
// XXX: This should REALLY be a function
if ($_SERVER['SERVER_NAME'] == $strApplicationDevURL) {
    $boolDebugMode = true;
    $strApplicationURL = $strApplicationDevURL;
    if (file_exists('./.git/HEAD')) {
        $_strGitHead = file_get_contents('./.git/HEAD');
        $_strGitSHA1 = file_get_contents('./.git/' . substr($_strGitHead, 5, -1));
        $_strGitBranch = substr($_strGitHead, 16, -1);
        $strApplicationSiteName = 'Phoebus Development - Version: ' . $strApplicationVersion . ' - ' .
            'Branch: ' . $_strGitBranch . ' - ' .
            'Commit: ' . substr($_strGitSHA1, 0, 7);
    }
    else {
        $strApplicationSiteName = 'Phoebus Development - Version: ' . $strApplicationVersion;
    }
    
    error_reporting(E_ALL);
    ini_set("display_errors", "on");
}

// Set the root entry point and ensure insanity isn't happening
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