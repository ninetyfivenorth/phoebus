<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL

// == | Vars | ================================================================

$strApplicationSiteName = 'Pale Moon - Add-ons';
$strApplicationSkin = 'palemoon';
$strContentBasePath = './components/site/content/';
$strSkinBasePath = './skin/' . $strApplicationSkin . '/';
$strObjDirSmartyCachePath = $strObjDirPath . 'smarty/frontend/';

$strRequestSmartyDebug = funcHTTPGetValue('smartyDebug');

$arraySmartyPaths = array(
    'cache' => $strObjDirSmartyCachePath . 'cache',
    'compile' => $strObjDirSmartyCachePath . 'compile',
    'config' => $strObjDirSmartyCachePath . 'config',
    'plugins' => $strObjDirSmartyCachePath . 'plugins',
    'templates' => $strObjDirSmartyCachePath . 'templates',
);

$arrayStaticPages = array(
    '/' => array(
        'title' => 'Your browser, your way!',
        'contentTemplate' => $strContentBasePath . 'frontpage.xhtml.tpl',
    ),
    '/incompatible/' => array(
        'title' => 'Known Incompatible Add-ons',
        'contentTemplate' => $strContentBasePath . 'incompatible.xhtml.tpl',
    )
);

// ============================================================================

// == | funcGenAddonContent | =================================================

function funcGenAddonContent($_strAddonSlug) {
    $_arrayAddonMetadata = $GLOBALS['addonManifest']->getAddonBySlug($_strAddonSlug);

    if ($_arrayAddonMetadata != null) {       
        $arrayPage = array(
            'title' => $_arrayAddonMetadata['name'],
            'contentTemplate' => $GLOBALS['strSkinBasePath'] . 'single-addon.tpl',
            'contentData' => $_arrayAddonMetadata
        );
    }
    else {
        if ($GLOBALS['boolDebugMode'] == true) {
            funcError('The requested add-on has a problem');
        }
        else {
            funcSendHeader('404');
        }       
    }

    return $arrayPage;
}

// ============================================================================

// == | funcGenAllExtensions | ================================================

function funcGenAllExtensions() {
    $arrayCategory = $GLOBALS['addonManifest']->getAllExtensions();

    if ($arrayCategory != null) {
        $arrayPage = array(
            'title' => 'Extensions',
            'contentType' => 'cat-all-extensions',
            'contentTemplate' => $GLOBALS['strSkinBasePath'] . 'category-addons.tpl',
            'contentData' => $arrayCategory
        );
    }
    else {
        if ($GLOBALS['boolDebugMode'] == true) {
            funcError('The requested category has a problem');
        }
        else {
            funcSendHeader('404');
        }       
    }

    return $arrayPage;
}

// ============================================================================

// == | funcGenCategoryContent | ==============================================

function funcGenCategoryContent($_categorySlug, $_pageTitle) {
    $arrayCategory = $GLOBALS['addonManifest']->getCategory($_categorySlug);

    if ($arrayCategory != null) {
        if ($_categorySlug == 'themes') {
            $arrayPage = array(
                'title' => 'Themes',
                'contentTemplate' => $GLOBALS['strSkinBasePath'] . 'category-addons.tpl',
                'contentType' => 'cat-themes',
                'contentData' => $arrayCategory
            );
        }
        else {
            $arrayPage = array(
                'title' => $_pageTitle,
                'contentTemplate' => $GLOBALS['strSkinBasePath'] . 'category-addons.tpl',
                'contentType' => 'cat-extensions',
                'contentData' => $arrayCategory
            );
        }
    }
    else {
        if ($GLOBALS['boolDebugMode'] == true) {
            funcError('The requested category has a problem');
        }
        else {
            funcSendHeader('404');
        }       
    }

    return $arrayPage;
}

function funcGenCategoryOtherContent($_type, $_array) {
    $arrayCategory = array();
    $_strDatastoreBasePath = $GLOBALS['strApplicationDatastore'] . 'addons/';
    
    foreach ($_array as $_key => $_value) {
        if ($_type === 'language-pack') {
            foreach($_array as $_key3 => $_value3) {
                $arrayCategory[$_value3['name']] = $_value3;
            }
        }
        elseif ($_type === 'search-plugin') {
            $_arrayAddonMetadata = simplexml_load_file('./datastore/searchplugins/' . $_value);
            $arrayCategory[(string)$_arrayAddonMetadata->ShortName]['type'] = 'search-plugin';
            $arrayCategory[(string)$_arrayAddonMetadata->ShortName]['id'] = $_key;
            $arrayCategory[(string)$_arrayAddonMetadata->ShortName]['name'] = (string)$_arrayAddonMetadata->ShortName;
            $arrayCategory[(string)$_arrayAddonMetadata->ShortName]['slug'] = substr($_value, 0, -4);
            $arrayCategory[(string)$_arrayAddonMetadata->ShortName]['icon'] = (string)$_arrayAddonMetadata->Image;
            unset($_arrayAddonMetadata);
        }
    }
    ksort($arrayCategory, SORT_NATURAL | SORT_FLAG_CASE);
    
    if ($_type == 'language-pack') {
        $arrayPage = array(
            'title' => 'Language Packs',
            'contentTemplate' => $GLOBALS['strSkinBasePath'] . 'category-other.tpl',
            'contentType' => 'cat-language-packs',
            'contentData' => $arrayCategory
        );
    }
    elseif ($_type == 'search-plugin') {
        $arrayPage = array(
            'title' => 'Search Plugins',
            'contentTemplate' => $GLOBALS['strSkinBasePath'] . 'category-other.tpl',
            'contentType' => 'cat-search-plugins',
            'contentData' => $arrayCategory
        );
    }
    
    return $arrayPage;
}

// ============================================================================

// == | funcGenSearchResults | ================================================

function funcGenSearchResults($_searchTerms) {
        //$_searchTermsRegEx = str_replace(' ', '|', $_searchTerms);
        $arrayResults = $GLOBALS['addonManifest']->getSearchResults($_searchTerms);

    if ($_searchTerms == null || $arrayResults == null) {
        $arrayPage = array(
            'title' => 'No Results',
            'contentType' => 'search',
            'contentTemplate' => $GLOBALS['strSkinBasePath'] . 'category-addons.tpl',
            'contentData' => null
        );
    }
    else {
        $arrayPage = array(
            'title' => 'Search Results for "' . $_searchTerms . '"',
            'contentType' => 'search',
            'contentTemplate' => $GLOBALS['strSkinBasePath'] . 'category-addons.tpl',
            'contentData' => $arrayResults
        );
    }

    return $arrayPage;
}

// ============================================================================

// == | funcGeneratePage | ====================================================

function funcGeneratePage($_array) {
    // Get the required template files
    $_strSiteTemplate = file_get_contents($GLOBALS['strSkinBasePath'] . 'template.tpl');
    $_strStyleSheet = file_get_contents($GLOBALS['strSkinBasePath'] . 'stylesheet.tpl');
    $_strContentTemplate = file_get_contents($_array['contentTemplate']);

    // Merge the stylesheet and the content template into the site template
    $_strSiteTemplate = str_replace('{%PAGE_CONTENT}', $_strContentTemplate, $_strSiteTemplate);
    $_strSiteTemplate = str_replace('{%SITE_STYLESHEET}', $_strStyleSheet, $_strSiteTemplate);
    unset($_strStyleSheet);
    unset($_strContentTemplate);

    // Load Smarty
    require_once($GLOBALS['arrayModules']['smarty']);
    $libSmarty = new Smarty();
    
    // Configure Smarty
    $libSmarty->caching = 0;
    $libSmarty->setCacheDir($GLOBALS['arraySmartyPaths']['cache'])
        ->setCompileDir($GLOBALS['arraySmartyPaths']['compile'])
        ->setConfigDir($GLOBALS['arraySmartyPaths']['config'])
        ->addPluginsDir($GLOBALS['arraySmartyPaths']['plugins'])
        ->setTemplateDir($GLOBALS['arraySmartyPaths']['templates']);

    // Smarty Debug
    if ($GLOBALS['strRequestSmartyDebug']) {
        $libSmarty->debugging = $GLOBALS['boolDebugMode'];
    }
    else {
        $libSmarty->debugging = false;
    }
    
    // Assign data to Smarty
    $libSmarty->assign('APPLICATION_DEBUG', $GLOBALS['boolDebugMode']);
    $libSmarty->assign('SITE_NAME', $GLOBALS['strApplicationSiteName']);
    $libSmarty->assign('SITE_DOMAIN', '//' . $GLOBALS['strApplicationURL']);
    $libSmarty->assign('PAGE_TITLE', $_array['title']);
    $libSmarty->assign('PAGE_PATH', $GLOBALS['strRequestPath']);
    $libSmarty->assign('BASE_PATH', substr($GLOBALS['strSkinBasePath'], 1));
    $libSmarty->assign('PHOEBUS_VERSION', $GLOBALS['strApplicationVersion']);
    
    if (array_key_exists('contentData', $_array)) {
        $libSmarty->assign('PAGE_DATA', $_array['contentData']);
    }
    
    if (array_key_exists('contentType', $_array)) {
        $libSmarty->assign('PAGE_TYPE', $_array['contentType']);
    }
    
    // Send html header and pass the final template to Smarty
    funcSendHeader('html');
    $libSmarty->display('string:' . $_strSiteTemplate, null, str_replace('/', '_', $GLOBALS['strRequestPath']));

    // We are done here...
    exit();
}

// ============================================================================

// == | Main | ================================================================

// Debug Conditions
if ($boolDebugMode == true) {
    // Do not allow public access to the site component when on addons-dev
    require_once($arrayModules['ftpAuth']);
    $FTPAuth = new classFTPAuth;
    //$isAuthorized = $FTPAuth->doAuth(true);

    // Git stuff
    if (file_exists('./.git/HEAD')) {
        $_strGitHead = file_get_contents('./.git/HEAD');
        $_strGitSHA1 = file_get_contents('./.git/' . substr($_strGitHead, 5, -1));
        $_strGitBranch = substr($_strGitHead, 16, -1);
        $strApplicationSiteName = 'Phoebus Development - Version: ' . $strApplicationVersion . ' - ' .
            'Branch: ' . $_strGitBranch . ' - ' .
            'Commit: ' . substr($_strGitSHA1, 0, 7);
    }
    else {
        $strApplicationSiteName = 'Phoebus Development - Version: ' . $strApplicationVersion;
    }    
}

require_once($arrayModules['readManifest']);
$addonManifest = new classReadManifest();

if (startsWith($strRequestPath, '/addon/')) {
    $strStrippedPath = str_replace('/', '', str_replace('/addon/', '', $strRequestPath));    
    funcGeneratePage(funcGenAddonContent($strStrippedPath));
}
elseif (startsWith($strRequestPath, '/extensions/') || startsWith($strRequestPath, '/themes/')) {    
    if ($strRequestPath == '/extensions/') {
        funcGeneratePage(funcGenAllExtensions());
    }
    elseif (startsWith($strRequestPath, '/extensions/')) {
        $strStrippedPath = str_replace('/', '', str_replace('/extensions/', '', $strRequestPath));

        $arrayCategoriesDB = array(
            'alerts-and-updates' => 'Alerts & Updates',
            'appearance' => 'Appearance',
            'download-management' => 'Download Management',
            'feeds-news-and-blogging' => 'Feeds, News, & Blogging',
            'privacy-and-security' => 'Privacy & Security',
            'search-tools' => 'Search Tools',
            'social-and-communication' => 'Social & Communication',
            'tools-and-utilities' => 'Tools & Utilities',
            'web-development' => 'Web Development',
            'other' => 'Other',
            'bookmarks-and-tabs' => 'Bookmarks & Tabs',
        );
        
        if (array_key_exists($strStrippedPath, $arrayCategoriesDB) && $strStrippedPath != 'themes')  {
            funcGeneratePage(funcGenCategoryContent($strStrippedPath, $arrayCategoriesDB[$strStrippedPath]));
        }
        else {
            funcSendHeader('404');
        }
    }
    elseif ($strRequestPath == '/themes/') {
        funcGeneratePage(funcGenCategoryContent('themes', null));
    }
    else {
        funcSendHeader('404');
    }
}
elseif ($strRequestPath == '/language-packs/') {
    require_once($arrayModules['langPacks']);
    $langPacks = new classLangPacks;
    $arrayLangPackDB = $langPacks->funcGetLanguagePacks();
    
    funcGeneratePage(funcGenCategoryOtherContent('language-pack', $arrayLangPackDB));
}
elseif ($strRequestPath == '/search-plugins/') {
    require_once($arrayModules['dbSearchPlugins']);
    
    funcGeneratePage(funcGenCategoryOtherContent('search-plugin', $arraySearchPluginsDB));
}
elseif ($strRequestPath == '/search/') {
    $strRequestSearchTerms = funcHTTPGetValue('terms');
    
    funcGeneratePage(funcGenSearchResults($strRequestSearchTerms));
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