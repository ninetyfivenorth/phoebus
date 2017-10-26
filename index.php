<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

// == | Function: funcError | =================================================

function funcError($_value) {
    header('Content-Type: text/plain');
    die('Error: ' . $_value);
    
    // We are done here
    exit();
}

// ============================================================================

// == | Function: funcHTTPGetValue |===========================================

function funcHTTPGetValue($_value) {
    if (!isset($_GET[$_value]) || $_GET[$_value] === '' || $_GET[$_value] === null || empty($_GET[$_value])) {
        return null;
    }
    else {    
        $_finalValue = preg_replace('/[^-a-zA-Z0-9_\-\/\{\}\@\.]\%/', '', $_GET[$_value]);
        return $_finalValue;
    }
}

// ============================================================================

// == | Function: funcCheckVar | ==============================================

function funcCheckVar($_value) {
    if ($_value === '' || $_value === 'none' || $_value === null || empty($_value)) {
        return null;
    }
    else {
        return $_value;
    }
}

// ============================================================================

// == | funcSendHeader | ======================================================

function funcSendHeader($_value) {
    $_arrayHeaders = array(
        '404' => 'HTTP/1.0 404 Not Found',
        '501' => 'HTTP/1.0 501 Not Implemented',
        'html' => 'Content-Type: text/html',
        'text' => 'Content-Type: text/plain',
        'xml' => 'Content-Type: text/xml',
        'css' => 'Content-Type: text/css',
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

// == | Function: funcRedirect |===============================================

function funcRedirect($_strURL) {
	header('Location: ' . $_strURL , true, 302);
    
    // We are done here
    exit();
}

// ============================================================================

// == | Functions: startsWith & endsWith |=====================================

function startsWith($haystack, $needle) {
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle) {
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

// ============================================================================

// == | Function: funcCheckUserAgent |=========================================

function funcCheckUserAgent() {
    if (startsWith(strtolower($_SERVER['HTTP_USER_AGENT']), 'wget/') ||
        startsWith(strtolower($_SERVER['HTTP_USER_AGENT']), 'curl/')) {
        funcSendHeader('404');
    }
}

// ============================================================================

// == | Application Entry Point | =============================================

require_once('./frontend/application.php');

// ============================================================================
?>