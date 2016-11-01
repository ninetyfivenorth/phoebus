<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL

// == | Vars | ================================================================

$strPageBasePath = './phoebus/base/content/pages/';
$arrayPages = array(
    '/' => array(
        'title' => 'Your browser, your way!',
        'content' => $strPageBasePath . 'frontpage.xhtml',
    ),
    '/search/' => array(
        'title' => 'Search',
        'content' => $strPageBasePath . 'search.xhtml',
    ),
    '/resources/incompatible/' => array(
        'title' => 'Known Incompatible Add-ons',
        'content' => $strPageBasePath . 'incompatible.xhtml',
    ),
);

// ============================================================================

// == | funcReadManifest | ===============================================

// ============================================================================

// == | Main | ================================================================

if (array_key_exists($strRequestPath, $arrayPages)) {
    funcSendHeader('html');
    print('<html><head><title>Pale Moon - Add-ons - ' . $arrayPages[$strRequestPath]['title'] . '</title></head><body>');
    readfile($arrayPages[$strRequestPath]['content']);
    print('</body></html>');
    exit();
}
else {
    funcSendHeader('404');
}

// ============================================================================
?>