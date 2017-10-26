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
$strApplicationSiteName = 'Pale Moon - Add-ons';
$strApplicationSkin = 'palemoon';

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

// Git stuff on debug
if ($boolDebugMode == true) {
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
}

// Set the root entry point and ensure insanity isn't happening
if ($_SERVER['REQUEST_URI'] == '/') {
    $strRequestComponent = 'site';
    $strRequestPath = '/';
}
elseif ($strRequestComponent != 'site' && $strRequestPath != null) {
    funcSendHeader('404');
}

// ============================================================================
?>