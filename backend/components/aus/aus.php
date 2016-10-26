<?php
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this
// file, You can obtain one at http://mozilla.org/MPL/2.0/.

// == | Vars | ================================================================

// ============================================================================

// == | funcGenerateUpdateXML | ===============================================

function funcGenerateUpdateXML($_addonManifest) {
    $_strUpdateXMLHead = '<?xml version="1.0"?>' . "\n" . '<RDF:RDF xmlns:RDF="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:em="http://www.mozilla.org/2004/em-rdf#">';
    $_strUpdateXMLTail = '</RDF:RDF>';

    header('Content-Type: text/xml');

    print($_strUpdateXMLHead);

    if ($_addonManifest != null) {
        print("\n");
        
        $_strUpdateXMLBody = file_get_contents('./components/aus/update-body.xml');

        $_arrayFilterSubstitute = array(
            '@ADDON_TYPE@' => $_addonManifest["type"],
            '@ADDON_ID@' => $_addonManifest["guid"],
            '@ADDON_VERSION@' => $_addonManifest["version"],
            '@PALEMOON_ID@' => $GLOBALS['strPaleMoonID'],
            '@ADDON_MINVERSION@' => $_addonManifest["minVer"],
            '@ADDON_MAXVERSION@' => $_addonManifest["maxVer"],
            '@ADDON_XPI@' => $_addonManifest["baseurl"],
            '@ADDON_HASH@' => $_addonManifest["hash"]
        );
        
        foreach ($_arrayFilterSubstitute as $_key => $_value) {
            $_strUpdateXMLBody = str_replace($_key, $_value, $_strUpdateXMLBody);
        }
        
        print("\n");
        print($_strUpdateXMLBody);
    }
    
    print($_strUpdateXMLTail);
    
    // We are done here...
    exit();
}

// ============================================================================

// == | Main | ================================================================

funcGenerateUpdateXML(funcReadAddonManifest('extension', 'adblock-latitude', 1));

// ============================================================================
?>