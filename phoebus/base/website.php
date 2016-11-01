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

// == | funcGenerateStaticPage | ==============================================

function funcGenerateStaticPage($_arrayPage) {
    $_strContentBasePath = $GLOBALS['strContentBasePath'];
    $_strSkinBasePath = $GLOBALS['strSkinBasePath'];

    $_strHTMLTemplate = file_get_contents($_strSkinBasePath . 'template.xhtml');
    $_strHTMLStyle = file_get_contents($_strSkinBasePath . 'style.css');
    $_strPageMenu = file_get_contents($_strSkinBasePath . 'menubar.xhtml');
    
    if (file_exists($_array['content'])) {
        $_strHTMLContent = file_get_contents($_array['content']);
        $_strHTMLPage = $_strHTMLTemplate;

        $_arrayFilterSubstitute = array(
            '@PAGE_CONTENT@' => $_strHTMLContent,
            '@SITE_MENU@' => $_strPageMenu,
            '@SITE_STYLESHEET@' => $_strHTMLStyle,
            '@SITE_NAME@' => 'Pale Moon - Add-ons',
            '@PAGE_TITLE@' => $_array['title'],
            '@BASE_PATH@' => substr($_strSkinBasePath, 1),
        );
        
        foreach ($_arrayFilterSubstitute as $_key => $_value) {
            $_strHTMLPage = str_replace($_key, $_value, $_strHTMLPage);
        }
        
        funcSendHeader('html');
        print($_strHTMLPage);
        
        // We are done here...
        exit();
    }
    else {
        funcError('Could not find static content file ' . $_content);
    }
}

// ============================================================================

// == | funcSendHeader | ======================================================

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
        funcGenerateStaticPage($arrayStaticPages[$strRequestPath]);
    }
    else {
        funcSendHeader('404');
    }
}

// ============================================================================
?>