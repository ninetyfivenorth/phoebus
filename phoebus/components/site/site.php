<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL

// == | Vars | ================================================================

$arraySections = array(
    'pages' => './phoebus/components/site/pages.php',
    'extensions' => './phoebus/components/site/extensions.php',
    'themes' => './phoebus/components/site/themes.php',
    'searchplugins' => './phoebus/components/site/searchplugins.php'
);

// ============================================================================

// == | Main | ================================================================

if ($strRequestPath == '/') {
    header('Content-Type: text/plain');
    print('homepage');
}
elseif (startsWith($strRequestPath, '/extensions/')) {
    header('Content-Type: text/plain');
    print('extensions');
}
elseif (startsWith($strRequestPath, '/themes/')) {
    header('Content-Type: text/plain');
    print('themes');
}
elseif (startsWith($strRequestPath, '/searchplugins/')) {
    header('Content-Type: text/plain');
    print('searchplugins');
}
else {
    header("HTTP/1.0 404 Not Found");
}

// ============================================================================
?>