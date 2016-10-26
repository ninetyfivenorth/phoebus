<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

// == | Function: funcError |==================================================

function funcError($_value) {
    die('Error: ' . $_value);
}

// ============================================================================

// == | Function: funcHTTPGetValue |===========================================

function funcHTTPGetValue($_value) {
    if (!isset($_GET[$_value]) || $_GET[$_value] === '' || $_GET[$_value] === null || empty($_GET[$_value])) {
        return null;
    }
    else {    
        return $_GET[$_value];
    }
}

// ============================================================================

// == | Function: funcRedirect |===============================================

function funcRedirect($varURL) {
	header('Location: ' . $varURL , true, 302);
}

// ============================================================================


?>

