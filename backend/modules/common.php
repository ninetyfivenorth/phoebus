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
    
    if ($_addonManifest == false) {
        funcError('Unable to read manifest ini file');
    }
    else {
        // $_mode 0 = undefined (future for fe) 1 = aus, 2 = download
        if ($_mode == 1) {
            unset($_addonManifest['id']);
            unset($_addonManifest['compat']);
            unset($_addonManifest['name']);
            unset($_addonManifest['author']);
            unset($_addonManifest['description']);

            $_addonManifest["hash"] = hash_file('sha256', '../datastore/' . $_addonType . 's/' . $_addonSlug . '/' . $_addonManifest["xpi"]);

            if ($_SERVER["HTTP_X_FORWARDED_HOST"] == 'dev.addons.palemoon.org') {
                $_addonManifest["baseurl"] = 'https://dev.addons.palemoon.org/datastore/' . $_addonType . 's/' . $_addonSlug . '/';
            }
            else {
                $_addonManifest["baseurl"] = 'https://addons.palemoon.org/phoebus/datastore/' . $_addonType . 's/' . $_addonSlug . '/';
            }
        }
        elseif ($_mode == 2) {
            unset($_addonManifest['id']);
            unset($_addonManifest['type']);
            unset($_addonManifest['compat']);
            unset($_addonManifest['minVer']);
            unset($_addonManifest['maxVer']);
            unset($_addonManifest['name']);
            unset($_addonManifest['author']);
            unset($_addonManifest['description']);
            
            $_addonManifest['basepath'] = '../datastore/' . $_addonType . 's/' . $_addonSlug . '/';
        }

        return $_addonManifest;
    }
}

// ============================================================================
?>