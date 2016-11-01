<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL

// == | Vars | ================================================================

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
        funcSendHeader('text');
        foreach ($arrayThemesDB as $_key => $_value) {
            var_dump(funcReadManifest('theme', $_value, true, false, false, false, false));
        }
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
    funcSendHeader('text');
    asort($arraySearchPluginsDB);
    $strSearchPluginsContent = array();
    $strSearchPluginsContentCatList = file_get_contents($strContentBasePath . 'addons/category-list-search-plugins.xhtml');
    foreach ($arraySearchPluginsDB as $_key => $_value) {
        $_strSearchPluginsContentCatList = $strSearchPluginsContentCatList;
        $_arrayFilterSubstitute = array(
            '@SEARCH_ID@' => $_value['slug'],
            '@SEARCH_TITLE@' => $_value['name'],
        );
        
        foreach ($_arrayFilterSubstitute as $_fkey => $_fvalue) {
            print( $_value['name']);
            //$_strHTMLPage = str_replace($_fkey, $_fvalue, $_strSearchPluginsContentCatList);
        }
        //print($_strSearchPluginsContentCatList);
    }
    
}
else {
    funcSendHeader('404');
}

// ============================================================================
?>