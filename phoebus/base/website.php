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

function funcGeneratePage($_arrayPage) {
    $_strContentBasePath = $GLOBALS['strContentBasePath'];
    $_strSkinBasePath = $GLOBALS['strSkinBasePath'];

    $_strHTMLTemplate = file_get_contents($_strSkinBasePath . 'template.xhtml');
    $_strHTMLStyle = file_get_contents($_strSkinBasePath . 'style.css');
    $_strPageMenu = file_get_contents($_strSkinBasePath . 'menubar.xhtml');
    
    if (file_exists($_arrayPage['content']) || is_array($_arrayPage['content'])) {
        $_strHTMLContent = file_get_contents($_arrayPage['content']);

        $_strHTMLPage = $_strHTMLTemplate;

        $_arrayFilterSubstitute = array(
            '@PAGE_CONTENT@' => $_strHTMLContent,
            '@SITE_MENU@' => $_strPageMenu,
            '@SITE_STYLESHEET@' => $_strHTMLStyle,
            '@SITE_NAME@' => 'Pale Moon - Add-ons',
            '@PAGE_TITLE@' => $_arrayPage['title'],
            '@BASE_PATH@' => substr($_strSkinBasePath, 1),
        );
        
        foreach ($_arrayFilterSubstitute as $_key => $_value) {
            $_strHTMLPage = str_replace($_key, $_value, $_strHTMLPage);
        }
        
        if (array_key_exists('subContent', $_arrayPage) {
            $_strHTMLPage = str_replace('@SUB_CONTENT@', $_arrayPage['subContent'], $_strHTMLPage);
        }
        
        funcSendHeader('html');
        print($_strHTMLPage);
        
        // We are done here...
        exit();
    }
    else {
        funcError('Could not read content ' . $_arrayPage['content']);
    }
}

// ============================================================================

// == | funcSendHeader | ======================================================

function funcSendHeader($_value) {
    $_arrayHeaders = array(
        '404' => 'HTTP/1.0 404 Not Found',
        'html' => 'Content-Type: text/html',
        'text' => 'Content-Type: text/plain',
        'xml' => 'Content-Type: text/xml',
        'phoebus' => 'X-Phoebus: https://github.com/Pale-Moon-Addons-Team/phoebus/',
    );
    
    if (array_key_exists($_value, $_arrayHeaders)) {
        header($_arrayHeaders['phoebus']);
        header($_arrayHeaders[$_value]);
        
        if ($_value == '404') {
            // We are done here
            exit();
        }
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
        funcGeneratePage($arrayStaticPages[$strRequestPath]);
    }
    else {
        funcSendHeader('404');
    }
}

// ============================================================================
?>