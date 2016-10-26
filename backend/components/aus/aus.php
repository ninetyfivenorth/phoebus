<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

// == | Vars | ================================================================



// ============================================================================

// == | funcReadAddonManifest | ===============================================

function funcReadAddonManifest($_addonType, $_addonSlug, $_isAUS) {
    $_addonManifest = parse_ini_file('../datastore/' . $_addonType . '/' . $_addonSlug . '/manifest.ini');
    
    if ($_addonManifest == false) {
        funcError('Unable to read manifest ini file');
    }
    else {
        if ($_isAUS = true) {
            unset($_addonManifest['id']);
            unset($_addonManifest['compat']);
            unset($_addonManifest['name']);
            unset($_addonManifest['author']);
            unset($_addonManifest['description']);

            $_addonManifest["hash"] = hash_file('sha256', '../datastore/' . $_addonType . 's/' . $_addonSlug . '/' . $_addonManifest["xpi"]);

            if ($_SERVER["HTTP_X_FORWARDED_HOST"] == 'addons.palemoon.org') {
                $_addonManifest["baseurl"] = 'https://addons.palemoon.org/phoebus/datastore/' . $_addonType . 's/' . $_addonSlug . '/';
            }
            else {
                $_addonManifest["baseurl"] = 'https://addons.palemoon.org/datastore/' . $_addonType . 's/' . $_addonSlug . '/';
            }
        }

        return $_addonManifest;
    }
}


// ============================================================================

// == | funcGenerateUpdateXML | ===============================================

function funcGenerateUpdateXML($_addonManifest) {
    $strUpdateXMLHead = '<?xml version="1.0" encoding="UTF-8"?>\n<RDF:RDF xmlns:RDF="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:em="http://www.mozilla.org/2004/em-rdf#">';
    $strUpdateXMLTail = '</RDF:RDF>';

    header('Content-Type: text/xml');

    print($strUpdateXMLHead);

    if ($_addonManifest != null) {
        $strUpdateXMLBody = file_get_contents('./components/aus/update-body.xml');
        str_replace('@ADDON_TYPE@', $$_addonManifest["type"], $strUpdateXMLBody);
        str_replace('@ADDON_ID@', $$_addonManifest["guid"], $strUpdateXMLBody);
        str_replace('@PALEMOON_ID@', $GLOBALS['strPaleMoonID'], $strUpdateXMLBody);
        str_replace('@ADDON_MINVERSION@', $_addonManifest["minVer"], $strUpdateXMLBody);
        str_replace('@ADDON_MAXVERSION@', $_addonManifest["maxVer"], $strUpdateXMLBody);
        str_replace('@ADDON_XPI@', $_addonManifest["baseurl"] . $_addonManifest["xpi"], $strUpdateXMLBody);
        str_replace('@ADDON_HASH@', $_addonManifest["hash"], $strUpdateXMLBody);
        
        print($strUpdateXMLBody);
    }
    
    print($strUpdateXMLTail);
    
    // We are done here...
    exit();
}

// ============================================================================

// == | Main | ================================================================

funcGenerateUpdateXML(funcReadAddonManifest('extension', 'adblock-latitude', true));

// ============================================================================
?>