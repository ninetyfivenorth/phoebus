<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL

// == | Vars | ================================================================

// ============================================================================

// == | funcGenThemesCategoryContent | ========================================

function funcGenThemesCategoryContent() {
    $strSearchPluginsContent = array();
    $strSearchPluginsContentCatList = file_get_contents($GLOBALS['strContentBasePath'] . 'addons/category-list-themes.xhtml');
    foreach ($GLOBALS['arraySearchPluginsDB'] as $_key => $_value) {
        $_arrayThemeMetadata = funcReadManifest('theme', $_value, true, false, false, false, false);
        $_strSearchPluginsContentCatList = $strSearchPluginsContentCatList;
        $_arrayFilterSubstitute = array(
            '@THEME_SLUG@' => $_arrayThemeMetadata['metadata']['slug'],
            '@THEME_NAME@' => $_arrayThemeMetadata['metadata']['name'],
            '@THEME_SHORTDESCRIPTION@' => $_arrayThemeMetadata['metadata']['shortDescription'],
        );
        
        foreach ($_arrayFilterSubstitute as $_fkey => $_fvalue) {
            $_strSearchPluginsContentCatList = str_replace($_fkey, $_fvalue, $_strSearchPluginsContentCatList);
        }
        array_push($strSearchPluginsContent, $_strSearchPluginsContentCatList);
    }
    $strSearchPluginsContent = implode($strSearchPluginsContent);
    return $strSearchPluginsContent;
}

// ============================================================================

// == | funcGenSearchPluginsCategoryContent | =================================

function funcGenSearchPluginsCategoryContent() {
    $strSearchPluginsContent = array();
    $strSearchPluginsContentCatList = file_get_contents($GLOBALS['strContentBasePath'] . 'addons/category-list-search-plugins.xhtml');
    foreach ($GLOBALS['arraySearchPluginsDB'] as $_key => $_value) {
        $_strSearchPluginsContentCatList = $strSearchPluginsContentCatList;
        $_arrayFilterSubstitute = array(
            '@SEARCH_ID@' => $_key,
            '@SEARCH_SLUG@' => $_value['slug'],
            '@SEARCH_TITLE@' => $_value['name'],
        );
        
        foreach ($_arrayFilterSubstitute as $_fkey => $_fvalue) {
            $_strSearchPluginsContentCatList = str_replace($_fkey, $_fvalue, $_strSearchPluginsContentCatList);
        }
        array_push($strSearchPluginsContent, $_strSearchPluginsContentCatList);
    }
    $strSearchPluginsContent = implode($strSearchPluginsContent);
    return $strSearchPluginsContent;
}

// ============================================================================

// == | Main | ================================================================

include_once($arrayModules['readManifest']);

if (startsWith($strRequestPath, '/extensions/')) {
    include_once($arrayModules['dbExtensions']);
    if ($strRequestPath == '/extensions/') {
        funcSendHeader('text');
        print('extensions main page');
    }
    elseif ($strRequestPath == '/extensions/all/') {
        funcSendHeader('text');
        foreach ($arrayExtensionsDB as $_key => $_value) {
            var_dump(funcReadManifest('extension', $_value, true, false, false, false, false));
        }
    }
    else {
        $strStrippedPath = str_replace('/', '', str_replace('/extensions/', '', $strRequestPath));
        $ArrayDBFlip = array_flip($arrayExtensionsDB);

        if (array_key_exists($strStrippedPath,$ArrayDBFlip)) {
            header('Content-Type: text/plain');
            var_dump(funcReadManifest('extension', $strStrippedPath, true, true, false, false, false));
        }
        else {
            header("HTTP/1.0 404 Not Found");
        }
    }
}
elseif (startsWith($strRequestPath, '/themes/')) {
    include_once($arrayModules['dbThemes']);
    if ($strRequestPath == '/themes/') {
        $arrayPage = array(
            'title' => 'Themes',
            'content' => $strContentBasePath . 'addons/category-page-themes.xhtml',
            'subContent' => funcGenThemesCategoryContent(),
        );
        
        funcGeneratePage($arrayPage);
    }
    else {
        $strStrippedPath = str_replace('/', '', str_replace('/themes/', '', $strRequestPath));
        $ArrayDBFlip = array_flip($arrayThemesDB);

        if (array_key_exists($strStrippedPath,$ArrayDBFlip)) {
           funcSendHeader('text');
            var_dump(funcReadManifest('theme', $strStrippedPath, true, true, false, false, false));
        }
        else {
            header("HTTP/1.0 404 Not Found");
        }
    }
}
elseif ($strRequestPath == '/search-plugins/') {
    include_once($arrayModules['dbSearchPlugins']);
    funcSendHeader('html');
    asort($arraySearchPluginsDB);
   
    $arrayPage = array(
        'title' => 'Search Plugins',
        'content' => $strContentBasePath . 'addons/category-page-search-plugins.xhtml',
        'subContent' => funcGenSearchPluginsCategoryContent(),
    );
    
    funcGeneratePage($arrayPage);
}
else {
    funcSendHeader('404');
}

// ============================================================================
?>