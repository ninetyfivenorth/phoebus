<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL

// == | funcGenAddonContent | =================================================

function funcGenAddonContent($_isTheme, $_arrayAddonMetadata) {

    if ($_isTheme == true) {
        $strAddonContent = file_get_contents($GLOBALS['strContentBasePath'] . 'addons/single-page-theme.xhtml');
    }
    else {
        $strAddonContent = file_get_contents($GLOBALS['strContentBasePath'] . 'addons/single-page-extension.xhtml');
    }
    
    $_arrayFilterSubstitute = array(
        '@ADDON_TYPE@' => $_arrayAddonMetadata['addon']['type'],
        '@ADDON_ID@' => $_arrayAddonMetadata['addon']['id'],
        '@ADDON_SLUG@' => $_arrayAddonMetadata['metadata']['slug'],
        '@ADDON_NAME@' => $_arrayAddonMetadata['metadata']['name'],
        '@ADDON_AUTHOR@' => $_arrayAddonMetadata['metadata']['author'],
        '@ADDON_DESCRIPTION@' => $_arrayAddonMetadata['metadata']['longDescription'],
        '@ADDON_BASEPATH@' => substr($_arrayAddonMetadata['addon']['basePath'], 1),
        '@ADDON_XPI_FILE@' => $_arrayAddonMetadata['addon']['release'],
        '@ADDON_XPI_VERSION@' => $_arrayAddonMetadata['xpi'][$_arrayAddonMetadata['addon']['release']]['version'],
        '@ADDON_XPI_MINVERSION@' => $_arrayAddonMetadata['xpi'][$_arrayAddonMetadata['addon']['release']]['minAppVersion'],
        '@ADDON_XPI_MAXVERSION@' => $_arrayAddonMetadata['xpi'][$_arrayAddonMetadata['addon']['release']]['maxAppVersion'],
    );
    
    foreach ($_arrayFilterSubstitute as $_fkey => $_fvalue) {
        $strAddonContent = str_replace($_fkey, $_fvalue, $strAddonContent);
    }
    
    $arrayPage = array(
        'title' => $_arrayAddonMetadata['metadata']['name'],
        'content' => $strAddonContent,
    );
    
    return $arrayPage;
}

// ============================================================================

// == | funcGenThemesCategoryContent | ========================================

function funcGenThemesCategoryContent() {
    $strThemeContent = array();
    $strThemeContentCatList = file_get_contents($GLOBALS['strContentBasePath'] . 'addons/category-list-themes.xhtml');
    foreach ($GLOBALS['arrayThemesDB'] as $_key => $_value) {
        $_arrayThemeMetadata = funcReadManifest('theme', $_value, true, false, false, false, false);
        $_strThemeContentCatList = $strThemeContentCatList;
        $_arrayFilterSubstitute = array(
            '@THEME_SLUG@' => $_arrayThemeMetadata['metadata']['slug'],
            '@THEME_NAME@' => $_arrayThemeMetadata['metadata']['name'],
            '@THEME_AUTHOR@' => $_arrayThemeMetadata['metadata']['author'],
            '@THEME_SHORTDESCRIPTION@' => $_arrayThemeMetadata['metadata']['shortDescription'],
        );
        
        foreach ($_arrayFilterSubstitute as $_fkey => $_fvalue) {
            $_strThemeContentCatList = str_replace($_fkey, $_fvalue, $_strThemeContentCatList);
        }
        array_push($strThemeContent, $_strThemeContentCatList);
    }
    $strThemeContent = implode($strThemeContent);
    
    
    return $strThemeContent;
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
            funcSendHeader('404');
        }
    }
}
elseif (startsWith($strRequestPath, '/themes/')) {
    include_once($arrayModules['dbThemes']);
    if ($strRequestPath == '/themes/') {
        asort($arrayThemesDB);
        $arrayPage = array(
            'title' => 'Themes',
            'contentFile' => $strContentBasePath . 'addons/category-page-themes.xhtml',
            'subContent' => funcGenThemesCategoryContent(),
        );
        
        funcGeneratePage($arrayPage);
    }
    else {
        $strStrippedPath = str_replace('/', '', str_replace('/themes/', '', $strRequestPath));
        $ArrayDBFlip = array_flip($arrayThemesDB);

        if (array_key_exists($strStrippedPath,$ArrayDBFlip)) {
            funcGeneratePage(funcGenAddonContent(true, funcReadManifest('theme', $strStrippedPath, true, true, false, false, false)));
        }
        else {
            funcSendHeader('404');
        }
    }
}
elseif ($strRequestPath == '/search-plugins/') {
    include_once($arrayModules['dbSearchPlugins']);
    funcSendHeader('html');
    asort($arraySearchPluginsDB);
   
    $arrayPage = array(
        'title' => 'Search Plugins',
        'contentFile' => $strContentBasePath . 'addons/category-page-search-plugins.xhtml',
        'subContent' => funcGenSearchPluginsCategoryContent(),
    );
    
    funcGeneratePage($arrayPage);
}
else {
    funcSendHeader('404');
}

// ============================================================================
?>