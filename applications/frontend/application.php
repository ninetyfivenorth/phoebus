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
$strApplicationVersion = '1.7.0a1';
$strApplicationSkin = 'palemoon';
$boolDebugMode = false;

// Define application paths
$strApplicationPath = $strRootPath . '/applications/frontend/';
$strComponentsPath = $strApplicationPath . 'components/';
$strModulesPath = $strApplicationPath . 'modules/';

// Define Components
$arrayComponents = array(
    'site' => $strComponentsPath . 'site/site.php',
    'discover' => $strComponentsPath . 'discover/discover.php',
    'download' => $strComponentsPath . 'download.php',
    'license' => $strComponentsPath . 'license.php'
);

// Define Modules
$arrayModules = array(
    'readManifest' => $strModulesPath . 'funcReadManifest.php'
);

// Smarty Debug
$strRequestSmartyDebug = funcHTTPGetValue('smartyDebug');

// ============================================================================

// == | Main | ================================================================

// Merge Platform Components and Modules into Application Components and Modules
// and unset
$arrayComponents = array_merge($arrayComponents, $arrayPlatformComponents);
$arrayModules = array_merge($arrayModules, $arrayPlatformModules);
unset($arrayPlatformComponents);
unset($arrayPlatformModules);

// Array merging results in numerical string keys being converted to indexes
$arrayComponents['43893'] = $strComponentsPath . 'special/special.php';

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

if ($strRequestComponent != 'site' && $strRequestPath != null) {
    funcSendHeader('404');
    exit();
}

// Load component based on strRequestComponent
if ($strRequestComponent != null) {
    if (array_key_exists($strRequestComponent, $arrayComponents)) {
        require_once($arrayComponents[$strRequestComponent]);
    }
    else {
        if ($boolDebugMode == true) {
            funcError($strRequestComponent . ' is an unknown component');
        }
        else {
            funcSendHeader('404');
        }
    }
}
else {
    if ($boolDebugMode == true) {
        funcError('You did not specify a component');
    }
    else {
        funcSendHeader('404');
    }
}

// ============================================================================
?>