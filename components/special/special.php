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

// Define application paths
$strSpecialComponentsPath = $strRootPath . '/components/special/';

// Define Components
$arraySpecialComponents = array(
    'phpInfo' => $strSpecialComponentsPath . 'phpInfo.php',
    'addonStatusReport' => $strSpecialComponentsPath . 'addonStatusReport.php'
);

// ============================================================================

// == | Main | ================================================================

// URL to Component assignment
if ($strRequestPath == '/special/addon-status/') {
    require_once($arraySpecialComponents['addonStatusReport']);
}
else {
    funcSendHeader('404');
}

// ============================================================================
?>