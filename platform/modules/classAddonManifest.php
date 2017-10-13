<?php
// ============================================================================

class classAddonManifest {
    
    // The main exposed method that will control the flow
    // Takes two variables str $_addonSlug and bool $_processOnly
    // Returns an array representing the datastructure of an add-on manifest
    public function funcGetManifest($_addonSlug, $_processOnly = null) {
        
    }
    
    // Get Phoebus files and return array
    private function funcGetFiles($_addonSlug) {
        
    }

    // Reads shadow json and returns add-on manifest
    private function funcReadJSON($_addonSlug, $_phoebusFile) {
        
    }

    // Reads phoebus.manifest and returns add-on manifest
    private function funcReadINI($_addonSlug, $_phoebusFile) {
        
    }

    // Reads and process phoebus.content
    private function funcProcessContent($_addonSlug, $_phoebusFile) {
        
    }

    // Reads install.rdf, returns a filename.xpi key
    private function funcProcessXPInstall($_addonSlug, $_xpiFile) {
        
    }

    // Regnerates the Shadow file
    private function funcRegenerate($_addonSlug, $_addonManifest, $_phoebusFile) {
    
    }

    // Perform fixups on add-on manifest keys
    // Ex: author to creator
    private function funcUpgradeKey($_addonManifest, $_oldKey, $_newKey) {
        
    }

}

// ============================================================================

?>