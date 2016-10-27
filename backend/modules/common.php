<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL

// == | Function: funcError |==================================================

function funcError($_value) {
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
        return $_GET[$_value];
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

// == | funcReadAddonManifest | ===============================================

function funcReadAddonManifest($_addonType, $_addonSlug, $_mode) {
    $_addonManifest = parse_ini_file('../datastore/' . $_addonType . 's/' . $_addonSlug . '/manifest.ini');
    
    if ($_addonManifest != false) {
        // $_mode 0 = (future for fe) 1 = aus, 2 = download
        switch ($_mode) {
            case 0:
                $_arrayUnsetKeys = null;
                break;
            case 1:
                $_arrayUnsetKeys = array('id', 'compat', 'name', 'author', 'description');
                $_addonManifest["hash"] = hash_file('sha256', '../datastore/' . $_addonType . 's/' . $_addonSlug . '/' . $_addonManifest["xpi"]);
                switch($_SERVER["HTTP_X_FORWARDED_HOST"]) {
                    case 'dev.addons.palemoon.org':
                        $_addonManifest["baseurl"] = 'https://dev.addons.palemoon.org/datastore/' . $_addonType . 's/' . $_addonSlug . '/';
                        break;
                    default:
                        $_addonManifest["baseurl"] = 'https://addons.palemoon.org/phoebus/datastore/' . $_addonType . 's/' . $_addonSlug . '/';
                        break;
                }
                break;
            case 2:
                $_arrayUnsetKeys = array('id', 'compat', 'minVer', 'maxVer', 'name', 'author', 'description');
                $_addonManifest['basepath'] = '../datastore/' . $_addonType . 's/' . $_addonSlug . '/';
                break;
        }

        if ($_arrayUnsetKeys != null) {
            foreach ($_arrayUnsetKeys as $_value) {
                unset($_addonManifest[$_value]);
            }
        }

        return $_addonManifest;
    }
    else {
        funcError('Unable to read manifest ini file');
    }
}

// ============================================================================
?>