<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

// == | Basic Functions | =====================================================

require_once('./platform/modules/basicFunctions.php');

// ============================================================================

// == | Vars | ================================================================

// Define global paths
$strRootPath = $_SERVER['DOCUMENT_ROOT'];
$strObjDirPath = $strRootPath . '/.obj/';
$strApplicationDatastore = './datastore/';
$strDatabasesPath = $strRootPath . '/db/';
$strLibPath = $strRootPath . '/lib/';

// Define Platform Paths
$strPlatformPath = $strRootPath . '/platform/';
$strPlatformComponentsPath = $strPlatformPath . 'components/';
$strPlatformModulesPath = $strPlatformPath . 'modules/';

// Define Libs
$arrayLibs = array(
    'vc' => $strLibPath . 'nsIVersionComparator.php',
    'smarty' => $strLibPath . 'smarty/Smarty.class.php',
    'rdf' => $strLibPath . 'rdf/RdfComponent.php',
);

// Define Database Arrays
$arrayDatabases = array(
    'dbAddons' => $strDatabasesPath . 'addons.php',
    'dbLangPacks' => $strDatabasesPath . 'langPacks.php',
    'dbSearchPlugins' => $strDatabasesPath . 'searchPlugins.php',
    'dbAUSExternals' => $strDatabasesPath . 'ausExternals.php',
    'dbCategories' => $strDatabasesPath . 'categories.php'
);

$arrayPlatformComponents = array();

$arrayPlatformModules = array(
    'addonManifest' => $strPlatformModulesPath . 'classAddonManifest.php'
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
$strRequestPath = funcHTTPGetValue('path');

// ============================================================================

// == | Main | ================================================================

// Merge Libs and Databases into Platform Modules then unset
$arrayPlatformModules = array_merge($arrayPlatformModules, $arrayLibs);
$arrayPlatformModules = array_merge($arrayPlatformModules, $arrayDatabases);
unset($arrayLibs);
unset($arrayDatabases);

// Set the root entry point and ensure insanity isn't happening
if ($_SERVER['REQUEST_URI'] == '/') {
    $strRequestComponent = 'site';
    $strRequestPath = '/';
}

// Decide which application we should load by URL
if ($strRequestPath == '/services/') {
    // Load the services application
    require_once('./applications/services/application.php');
}
else {
    // Load the frontend application
    require_once('./applications/frontend/application.php');
}

// ============================================================================
?>