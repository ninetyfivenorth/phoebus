<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL

// == | Vars | ================================================================

$strContentBasePath = './phoebus/base/content/';
$strSkinBasePath = './phoebus/skin/palemoon/';

$arrayAddonPaths = array(
    '/extensions/',
    '/themes/',
    '/search-plugins/'
);

$arrayStaticPages = array(
    '/' => array(
        'title' => 'Your browser, your way!',
        'content' => $strContentBasePath . 'pages/frontpage.xhtml',
    ),
    '/search/' => array(
        'title' => 'Search',
        'content' => $strContentBasePath . 'pages/search.xhtml',
    ),
    '/incompatible/' => array(
        'title' => 'Known Incompatible Add-ons',
        'content' => $strContentBasePath . 'pages/incompatible.xhtml',
    ),
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
    startsWith($strRequestPath, '/search-plugins/') == true) {
    include_once('./phoebus/base/addons.php');
}
else {
    if (array_key_exists($strRequestPath, $arrayStaticPages)) {
        funcSendHeader('html');
        print('<html><head><title>Pale Moon - Add-ons - ' . $arrayStaticPages[$strRequestPath]['title'] . '</title></head><body>');
        readfile($strContentBasePath . 'site-menu.xhtml');
        readfile($arrayStaticPages[$strRequestPath]['content']);
        print('</body></html>');
        exit();
    }
    else {
        funcSendHeader('404');
    }
}

// ============================================================================
?>