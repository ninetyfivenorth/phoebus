<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL

// == | Vars | ================================================================

$arraySections = array(
    'pages' => './phoebus/base/content/pages.php',
    'addons' => './phoebus/base/content/addons.php',
);

// ============================================================================

// == | funcHeader | ==========================================================

function funcSendHeader($_value) {
    if ($_value == '404') {
        header("HTTP/1.0 404 Not Found");
        exit();
    }
    elseif ($_value == 'html') {
        header('Content-Type: text/html');
    }
    elseif ($_value == 'text') {
        header('Content-Type: text/plain');
    }
    elseif ($_value == 'xml') {
        header('Content-Type: text/xml');
    }
}

// ============================================================================

// == | Main | ================================================================

if (startsWith($strRequestPath, '/extensions/') == true ||
    startsWith($strRequestPath, '/themes/') == true ||
    startsWith($strRequestPath, '/searchplugins/') == true) {
    include_once($arraySections['addons']);
}
else {
    include_once($arraySections['pages']);
}

// ============================================================================
?>