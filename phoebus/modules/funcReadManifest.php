<?php
// == | funcReadManifest | ==============================================

function funcReadManifest($_addonSlug) {  
    // Define some vars
    $_boolProcess = false;
    $_boolRegenerate = false;
    
    // Define locations for files
    $_strObjDirDatastoreBasePath = $GLOBALS['strRootPath'] . '/obj-dir/datastore/addons/';
    $_strDatastoreBasePath = $GLOBALS['strApplicationDatastore'] . 'addons/';
    $_addonBasePath = $_strDatastoreBasePath . $_addonSlug . '/';
    
    // Define which files we are interested in and determin their existance
    $_arrayPhoebusFiles = array(
        'manifest' => array(
            'file' => 'phoebus.manifest',
            'exists' => file_exists($_addonBasePath . '/phoebus.manifest'),
            'hash' => null
        ),
        'content' => array(
            'file' => 'phoebus.content',
            'exists' => file_exists($_addonBasePath . '/phoebus.content'),
            'hash' => null
        ),
        'license' => array(
            'file' => 'phoebus.license',
            'exists' => file_exists($_addonBasePath . '/phoebus.license'),
            'hash' => null
        ),
        'shadow' => array(
            'file' => $_addonSlug. '.shadow',
            'exists' => file_exists($_strObjDirDatastoreBasePath . $_addonSlug. '.shadow'),
        ),
        'temp' => array(
            'file' => $_addonSlug. '.temp',
            'exists' => file_exists($_strObjDirDatastoreBasePath . $_addonSlug. '.temp'),
        ),
        'icon' => file_exists($_addonBasePath . '/icon.png'),
        'preview' => file_exists($_addonBasePath . '/preview.png')
    );
       
    // Iterate through the files and generate a hash where nessisary
    foreach ($_arrayPhoebusFiles as $_key => $_value) {
        if (($_key == 'content' || $_key == 'manifest' || $_key == 'license') && $_value['exists'] == true) {
            $_arrayPhoebusFiles[$_key]['hash'] = hash_file('crc32b', $_strDatastoreBasePath . $_addonSlug . '/' . $_value['file']);
        }
    }
 
    // Determin if the Shadow file exists and decide on a course of action
    if ($_arrayPhoebusFiles['shadow']['exists'] == false && $_arrayPhoebusFiles['temp']['exists'] == false) {
        // There is no shadow file so we should generate it
        // Process the manifest files and generate the shadow file
        $_boolProcess = true;
        $_boolRegenerate = true;
    }
    elseif ($_arrayPhoebusFiles['shadow']['exists'] == true) {
        // There is a shadow file so read it and store it as $_addonManifest
        $_addonManifestShadow = file_get_contents($_strObjDirDatastoreBasePath . $_arrayPhoebusFiles['shadow']['file']);
        $_addonManifest = json_decode($_addonManifestShadow, true);
        
        // If the JSON file is valid json we should consider it good
        if ($_addonManifest != null) {
            $_addonManifest['phoebus']['regenerated'] = false;
            
            // We should only consider regenerating the file if the temporary file does not exist
            if ($_arrayPhoebusFiles['temp']['exists'] == false) {
                // Test to see if any phoebus files have changed
                if ($_addonManifest['phoebus']['manifestHash'] != $_arrayPhoebusFiles['manifest']['hash'] ||
                $_addonManifest['phoebus']['contentHash'] != $_arrayPhoebusFiles['content']['hash'] ||
                $_addonManifest['phoebus']['licenseHash'] != $_arrayPhoebusFiles['license']['hash']) {
                    // Files have changed so we should process the manifest files and regenerate the shadow file
                    $_boolProcess = true;
                    $_boolRegenerate = true;
                }
            }
        }
        else {
            // Fall back to on-the-fly and just process the manifest files
            $_boolProcess = true;
        }
    }
    else {
        // Fall back to on-the-fly and just process the manifest files
        $_boolProcess = true;
    }

    // Process the manifest files
    if ($_boolProcess == true) {
        // If phoebus.manifest exists read it
        if ($_arrayPhoebusFiles['manifest']['exists'] = true) {
            $_addonManifestINI = parse_ini_file($_addonBasePath . $_arrayPhoebusFiles['manifest']['file'], true) or null;
            if ($_addonManifestINI == null) {
                if ($GLOBALS['boolDebugMode'] == true) {
                    funcError('Could not parse manifest file for ' . $_addonSlug);
                }
                else {
                    return null;
                }
            }
        }
        else {
            if ($GLOBALS['boolDebugMode'] == true) {
                funcError('Could not find manifest file for ' . $_addonSlug);
            }
            else {
                return null;
            }
        }
        
        // Define base manifest data structure
        $_addonManifestBase = array(
            'phoebus' => array(
                'isShadow' => null,
                'regenerated' => null,
                'manifestHash' => null,
                'contentHash' => null,
                'licenseHash' => null
            ),
            'addon' => array(
                'type' => null,
                'id' => null,
                'release' => null
            ),
            'metadata' => array(
                'name' => null,
                'slug' => null,
                'author' => null,
                'shortDescription' => null,
                'homepageURL' => null,
                'supportURL' => null,
                'supportEmail' => null,
                'repository' => null,
                'license' => null, // preferred spelling
                'licence' => null
            ),
        );
        
        // Create a new array that will replace existing values from manifest ini onto the predefined data structure
        // then unset the base and ini-read arrays
        $_addonManifest = array_replace_recursive($_addonManifestBase, $_addonManifestINI);
        unset($_addonManifestINI);
        unset($_addonManifestBase);
        
        // Let's do some sanity checks
        if (funcCheckVar($_addonManifest['addon']['type']) == null ||
            funcCheckVar($_addonManifest['addon']['id']) == null ||
            funcCheckVar($_addonManifest['addon']['release']) == null ||
            funcCheckVar($_addonManifest['metadata']['name']) == null ||
            funcCheckVar($_addonManifest['metadata']['slug']) == null ||
            funcCheckVar($_addonManifest['metadata']['author']) == null ||
            funcCheckVar($_addonManifest['metadata']['shortDescription']) == null ||
            array_key_exists($_addonManifest['addon']['release'], $_addonManifest) == false)
        {
            if ($GLOBALS['boolDebugMode'] == true) {
                funcError('Missing minimum required entries in manifest file for ' . $_addonSlug);
            }
            else {
                return null;
            }
        }
        
        // If any value of a metadata subkey is 'none' replace it with null
        foreach ($_addonManifest['metadata'] as $_metadataKey => $_metadataValue) {
            if ($_metadataValue === 'none') {
                $_addonManifest['metadata'][$_metadataKey] = null;
            }
        }
        
        // INI has depth and identical section name issues so we need to mangle it
        // Create a temporary array that we can easily manipulate
        $_addonManifestVersions = $_addonManifest;
        
        // Drop the addon and metadata keys off the temporary array
        unset($_addonManifestVersions['phoebus']);
        unset($_addonManifestVersions['addon']);
        unset($_addonManifestVersions['metadata']);
        
        // Reverse sort the keys
        krsort($_addonManifestVersions, SORT_NATURAL | SORT_FLAG_CASE);
        
        // mangle filename.xpi sections into a subkey
        // we are now working on the add-on manifest array
        foreach ($_addonManifestVersions as $_key => $_value) {
            unset($_addonManifest[$_key]);
            $_addonManifest['xpinstall'][$_key] = $_value;
        }
        
        // clear the temporary array out of memory
        unset($_addonManifestVersions);
        
        // Set the URL for the add-on to the manifest
        $_addonManifest['metadata']['url'] = '/addon/' . $_addonManifest['metadata']['slug'] . '/';

        // shortDescription should be html entity'd
        $_addonManifest['metadata']['shortDescription'] = htmlentities($_addonManifest['metadata']['shortDescription'], ENT_XHTML);
        $_addonFullShortDesc = $_addonManifest['metadata']['shortDescription'];
        if (strlen($_addonManifest['metadata']['shortDescription']) >= 205) {
            $_addonManifest['metadata']['shortDescription'] = substr($_addonManifest['metadata']['shortDescription'], 0, 200) . '...';
        }

        // Deal with phoebus.content   
        if ($_arrayPhoebusFiles['content']['exists'] == true) {
            // Read phoebus.content
            $_addonPhoebusContent = file_get_contents($_addonBasePath . $_arrayPhoebusFiles['content']['file']);
            
            // html encode phoebus.content
            $_addonPhoebusContent = htmlentities($_addonPhoebusContent, ENT_XHTML);
            
            // create a temporary array that contains the strs to simple pseudo-bbcode to real html
            $_arrayPhoebusCode = array(
                'simple' => array(
                    "\r\n" => "\n",
                    "\n" => "<br />\n",
                    '[b]' => '<strong>',
                    '[/b]' => '</strong>',
                    '[i]' => '<em>',
                    '[/i]' => '</em>',
                    '[u]' => '<u>',
                    '[/u]' => '</u>',
                    '[ul]' => '</p>' . "\n" .'<ul>',
                    '[/ul]' => '</ul>' . "\n" . '<p>',
                    '[li]' => '<li>',
                    '[/li]' => '</li>',
                    '[section]' => '</p>' . "\n" . '<h3>',
                    '[/section]' => '</h3>' . "\n" . '<p>'
                ),
                'complex' => array(
                    '\<(ul|\/ul|li|\/li|p|\/p)\><br \/>' => '<$1>',
                    '\[url=(.*)\](.*)\[\/url\]' => '<a href="$1" target="_blank">$2</a>',
                    '\[url\](.*)\[\/url\]' => '<a href="$1" target="_blank">$1</a>',
                    '\[img(.*)\](.*)\[\/img\]' => ''
                )
            );

            // str replace pseudo-bbcode with real html
            foreach ($_arrayPhoebusCode['simple'] as $_key => $_value) {
                $_addonPhoebusContent = str_replace($_key, $_value, $_addonPhoebusContent);
            }
            
            // Regex replace pseudo-bbcode with real html
            foreach ($_arrayPhoebusCode['complex'] as $_key => $_value) {
                $_addonPhoebusContent = preg_replace('/' . $_key . '/iU', $_value, $_addonPhoebusContent);
            }

            // Assign parsed phoebus.content to the add-on manifest array
            $_addonManifest['metadata']['longDescription'] = $_addonPhoebusContent;
        }
        else {
            // Since there is no phoebus.content use the short description
            $_addonManifest['metadata']['longDescription'] = $_addonFullShortDesc;
        }

        // Hack for people using repositories as their homepage
        if ($_addonManifest['metadata']['repository'] == null && (
            strpos($_addonManifest['metadata']['homepageURL'], 'github.com') > -1 ||
            strpos($_addonManifest['metadata']['homepageURL'], 'bitbucket.org') > -1 ||
            strpos($_addonManifest['metadata']['homepageURL'], 'gitlab.com') > -1)) {
            $_addonManifest['metadata']['repository'] = $_addonManifest['metadata']['homepageURL'];
            $_addonManifest['metadata']['homepageURL'] = null;
        }

        $arrayLicenses = array(
            'custom' => 'Custom License',
            'Apache-2.0' => 'Apache License 2.0',
            'Apache-1.1' => 'Apache License 1.1',
            'BSD-3-Clause' => 'BSD 3-Clause',
            'BSD-2-Clause' => 'BSD 2-Clause',
            'GPL-3.0' => 'GNU General Public License 3.0',
            'GPL-2.0' => 'GNU General Public License 2.0',
            'LGPL-3.0' => 'GNU Lesser General Public License 3.0',
            'LGPL-2.1' => 'GNU Lesser General Public License 2.1',
            'AGPL-3.0' => 'GNU Affero General Public License v3',
            'MIT' => 'MIT License',
            'MPL-2.0' => 'Mozilla Public License 2.0',
            'MPL-1.1' => 'Mozilla Public License 1.1',
            'PD' => 'Public Domain'
        );

        $arrayLicenses = array_change_key_case($arrayLicenses, CASE_LOWER);
        
        // Hack for license/licence
        if ($_addonManifest['metadata']['license'] == null && $_addonManifest['metadata']['licence'] != null) {
            $_addonManifest['metadata']['license'] = $_addonManifest['metadata']['licence'];
        }

        unset($_addonManifest['metadata']['licence']);
        
        // License Logic
        if ($_arrayPhoebusFiles['license']['exists'] == true) {
            $_addonManifest['metadata']['license'] = 'custom';
        }
        
        if ($_addonManifest['metadata']['license'] != null) {
            $_addonManifest['metadata']['license'] = strtolower($_addonManifest['metadata']['license']);
            if (array_key_exists($_addonManifest['metadata']['license'], $arrayLicenses)) {
                $_addonManifest['metadata']['licenseName'] = $arrayLicenses[$_addonManifest['metadata']['license']];
            }
            else {
                $_addonManifest['metadata']['license'] = 'unknown';
                $_addonManifest['metadata']['licenseName'] = 'Unknown License';
            }
        }
        
        if ($_addonManifest['metadata']['license'] == 'custom' && $_arrayPhoebusFiles['license']['exists'] == true) {
            $_addonManifest['metadata']['licenseText'] = file_get_contents($_addonBasePath . $_arrayPhoebusFiles['license']['file']);
        }
        elseif ($_addonManifest['metadata']['license'] == 'unknown') {
            $_addonManifest['metadata']['license'] = null;
        }
        else {
            $_addonManifest['metadata']['licenseText'] = null;
        }

        // Generate a sha256 hash for the current release
        if (file_exists($_addonBasePath . $_addonManifest['addon']['release'])) {    
            $_addonManifest['addon']['hash'] = hash_file('sha256', $_addonBasePath . $_addonManifest['addon']['release']);
        }
        else {
            if ($GLOBALS['boolDebugMode'] == true) {
                funcError('Could not find ' . $_addonManifest["xpi"]);
            }
            else {
                return null;
            }
        }

        $_addonManifest['addon']["baseURL"] = 'http://' . $GLOBALS['strApplicationURL'] . '<![CDATA[/?component=download&id=]]>';

        // assign the basePath to the add-on manifest array
        $_addonManifest['addon']['basePath'] = $_addonBasePath;

        // We are supposed to regenerate the shadow file
        if ($_boolRegenerate == true) {
            // Create the temp file
            $_fileTemp = fopen($_strObjDirDatastoreBasePath . $_arrayPhoebusFiles['temp']['file'], 'w+');
            
            // Assign Phoebus values for storage
            $_addonManifest['phoebus']['isShadow'] = true;
            $_addonManifest['phoebus']['manifestHash'] = $_arrayPhoebusFiles['manifest']['hash'];
            $_addonManifest['phoebus']['contentHash'] = $_arrayPhoebusFiles['content']['hash'];
            $_addonManifest['phoebus']['licenseHash'] = $_arrayPhoebusFiles['license']['hash'];
            
            // JSON Encode the array
            $_addonManifestShadow = json_encode($_addonManifest, JSON_PRETTY_PRINT);

            // Write the JSON to temp file
            fwrite($_fileTemp, $_addonManifestShadow);
            
            // Close the temp file
            fclose($_fileTemp);
            
            // Move (Overwrite) the shadow file with the temp file
            rename($_strObjDirDatastoreBasePath . $_arrayPhoebusFiles['temp']['file'], $_strObjDirDatastoreBasePath . $_arrayPhoebusFiles['shadow']['file']);

            // Set values for return
            $_addonManifest['phoebus']['isShadow'] = false;
            $_addonManifest['phoebus']['regenerated'] = true;
        }
    }
    
    // Post Manifest additions that can't be cached
    if ($_addonManifest != null) {
        if ($_arrayPhoebusFiles['icon'] == true) {
            $_addonManifest['metadata']['icon'] = substr($_addonBasePath . 'icon.png', 1);
        }
        else {
            $_addonManifest['metadata']['icon'] = substr($_strDatastoreBasePath . 'default/' . $_addonManifest['addon']['type'] . '.png', 1);
        }
        
        if ($_arrayPhoebusFiles['preview'] == true) {
            $_addonManifest['metadata']['preview'] = substr($_addonBasePath . 'preview.png', 1);
            $_addonManifest['metadata']['hasPreview'] = true;
        }
        else {
            $_addonManifest['metadata']['preview'] = substr($_strDatastoreBasePath . 'default/preview.png', 1);
            $_addonManifest['metadata']['hasPreview'] = false;
        }
    }
    
    // Return the data structure as an array or null
    return $_addonManifest;
}

// ============================================================================

?>