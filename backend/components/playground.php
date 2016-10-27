<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

if (isController != true) {
    die(notController);
}

// == | Vars | ================================================================

$strRequestMode = funcHTTPGetValue('mode');

// ============================================================================

// ============================================================================

if ($strRequestMode == null) {
    funcError('Mode is null.. Dumbass');
}

if ($strRequestMode == 'manifest') {
    var_dump(funcReadAddonManifest('extension', 'adblock-latitude', 1));
}

// ============================================================================
?>