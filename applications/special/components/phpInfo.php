<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL

// == | Vars | ================================================================

$strRequest = funcHTTPGetValue('type');

// ============================================================================

// == | Main | ================================================================

if ($strRequest == null) {
    funcError('Incorrect number of arguments');
}

if ($strRequest == 'all') {
    phpinfo();
}
elseif ($strRequest == 'vars') {
    phpinfo(32);
}
else {
    funcError('Unknown type');
}

// ============================================================================
?>