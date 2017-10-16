<?php
// == | funcReadManifest | ==============================================

function funcReadManifest($_addonSlug, $_boolLegacy = null) {  
    // Define some vars
    $_boolProcess = false;
    $_boolRegenerate = false;
    
    // Define locations for files
    $_strObjDirDatastoreBasePath = $GLOBALS['strObjDirPath'] . 'shadow/addons/';
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
            'file' => $_addonSlug. '.json',
            'exists' => file_exists($_strObjDirDatastoreBasePath . $_addonSlug. '.json'),
        ),
        'temp' => array(
            'file' => $_addonSlug. '.temp',
            'exists' => file_exists($_strObjDirDatastoreBasePath . $_addonSlug. '.temp'),
        ),
        'icon' => file_exists($_addonBasePath . '/icon.png'),
        'preview' => file_exists($_addonBasePath . '/preview.png'),
        'xpinstall' => array(
            'files' => null,
            'count' => null
        )
    );
       
    // Iterate through the files and generate a hash where nessisary
    foreach ($_arrayPhoebusFiles as $_key => $_value) {
        if (($_key == 'content' || $_key == 'manifest' || $_key == 'license') && $_value['exists'] == true) {
            $_arrayPhoebusFiles[$_key]['hash'] = hash_file('crc32b', $_strDatastoreBasePath . $_addonSlug . '/' . $_value['file']);
        }
    }
    
    // Interate through XPI files in datastore
    foreach (glob($_addonBasePath . '*.xpi') as $_xpiFile) {
        $_arrayPhoebusFiles['xpinstall']['files'][] = basename($_xpiFile);
    }
    
    // Count XPI Files
    $_arrayPhoebusFiles['xpinstall']['count'] = count($_arrayPhoebusFiles['xpinstall']['files']);
 
    if ($_boolLegacy == null) {
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
                    $_addonManifest['phoebus']['licenseHash'] != $_arrayPhoebusFiles['license']['hash'] ||
                    $_addonManifest['phoebus']['xpiCount'] != $_arrayPhoebusFiles['xpinstall']['count']) {
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
    }
    else {
        $_boolProcess = true;
    }

    // Process the manifest files
    if ($_boolProcess == true) {
        // Load RDF Module
        require_once($GLOBALS['arrayModules']['rdf']);
        
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
                'licenseDefault' => null,
                'licenseURL' => null,
                'licence' => null
            ),
            'xpinstall' => null
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
            funcCheckVar($_addonManifest['metadata']['slug']) == null)
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

        // Remove any preset filename.xpi keys from the manifest
        foreach ($_addonManifest as $_key => $_value) {
            if (endsWith($_key, '.xpi')) {
                unset($_addonManifest[$_key]);
            }
        }

        // Get data from XPI Files
        $_addonXPI = new ZipArchive;
        $_addonInstallStat = null;
        $_addonRDF = new RdfComponent();
        $_addonInstallRDF = null;
        
        $_addonTypes = array(
            2 => 'extension',
            4 => 'theme',
            8 => 'langpack',
            32 => 'package',
            64 => 'dictionary',
        );

        // Create the xpinstall key and populate it
        foreach ($_arrayPhoebusFiles['xpinstall']['files'] as $_value) {
            // Open the xpi file
            if ($_addonXPI->open($_addonBasePath  . '/' . $_value) === true) {

                // Read install.rdf
                $_addonInstallRDF = $_addonXPI->getFromName('install.rdf');

                $_addonInstallStat = $_addonXPI->statName('install.rdf');

                // Close the XPI File
                $_addonXPI->close();

                // Parse install.rdf
                $_addonInstallRDF = $_addonRDF->parseInstallManifest($_addonInstallRDF);
                
                // Consistancy and sanity checks..
                // If not release XPI they are non-fatal errors and drop the xpi on the floor
                // But if it IS the release xpi.. then it is fatal... error or return null
                if ((string)$_addonManifest['addon']['id'] != $_addonInstallRDF['id']) {                   
                    if ($_value != $_addonManifest['addon']['release']) {
                        $_addonManifest['errors'][] = $_value . ' has a mismatched add-on id';
                        continue;
                    }
                    else {
                        if ($GLOBALS['boolDebugMode'] == true) {
                            funcError('Release XPI id does not match manifest id for ' . $_addonSlug);
                        }
                        else {
                            return null;
                        }
                    }
                }
                
                if (!array_key_exists($GLOBALS['strPaleMoonID'], $_addonInstallRDF['targetApplication'])) {
                     if ($_value != $_addonManifest['addon']['id']) {
                        $_addonManifest['errors'][] = $_value . ' does not have a Pale Moon targetApplication';
                        continue;
                    }
                    else {
                        if ($GLOBALS['boolDebugMode'] == true) {
                            funcError('Release XPI does not have a Pale Moon targetApplication for ' . $_addonSlug);
                        }
                        else {
                            return null;
                        }
                    }
                }

                // Fill in xpinstall values from install.rdf
                $_addonManifest['xpinstall'][$_value]['version'] =
                    $_addonInstallRDF['version'];
                $_addonManifest['xpinstall'][$_value]['minAppVersion'] =
                    $_addonInstallRDF['targetApplication'][$GLOBALS['strPaleMoonID']]['minVersion'];
                $_addonManifest['xpinstall'][$_value]['maxAppVersion'] =
                    $_addonInstallRDF['targetApplication'][$GLOBALS['strPaleMoonID']]['maxVersion'];

                $_addonManifest['xpinstall'][$_value]['hash'] = hash_file('sha256', $_addonBasePath . $_value);
                $_addonManifest['xpinstall'][$_value]['mtime'] = $_addonInstallStat['mtime'];
            }
            else {
                if ($GLOBALS['boolDebugMode'] == true) {
                    funcError('An XPI file could not be opened ' . $_addonSlug);
                }
                else {
                    return null;
                }
            }
        }

        // If xpi files failed in some way and the xpinstall key is null then error/return null
        if ($_addonManifest['xpinstall'] == null) {
            if ($GLOBALS['boolDebugMode'] == true) {
                funcError('xpinstall key is null for ' . $_addonSlug);
            }
            else {
                return null;
            }
        }
        
        // Ensure the release xpi physically exists
        if (file_exists($_addonBasePath . '/' . $_addonManifest['addon']['release'])) {
            // Open the xpi file
            $_addonXPI->open($_addonBasePath  . '/' . $_value);

            // Read install.rdf
            $_addonInstallRDF = $_addonXPI->getFromName('install.rdf');

            // Close the XPI File
            $_addonXPI->close();

            // Parse install.rdf
            $_addonInstallRDF = $_addonRDF->parseInstallManifest($_addonInstallRDF);
            
            // Override potentally set values in the manifest file with those from the release XPI
            if (array_key_exists('name', $_addonInstallRDF)) {
                $_addonManifest['metadata']['name'] = $_addonInstallRDF['name']['en-US'];
            }
            
            if (array_key_exists('description', $_addonInstallRDF) && $_addonManifest['metadata']['shortDescription'] == null) {
                $_addonManifest['metadata']['shortDescription'] = $_addonInstallRDF['description']['en-US'];
            }
            elseif ($_addonManifest['metadata']['shortDescription'] == null) {
                $_addonManifest['metadata']['shortDescription'] = 'The ' . $_addonManifest['metadata']['name'] . ' ' . $_addonManifest['addon']['type'];
            }
            
            if (array_key_exists('creator', $_addonInstallRDF) && $_addonManifest['metadata']['author'] == null) {
                $_addonManifest['metadata']['author'] = $_addonInstallRDF['creator'];
            }
            elseif ($_addonManifest['metadata']['author'] == null) {
                $_addonManifest['metadata']['author'] == 'The ' . $_addonManifest['metadata']['name'] . ' Developers';
            }
            
            if (array_key_exists('homepageURL', $_addonInstallRDF)) {
                $_addonManifest['metadata']['homepageURL'] = $_addonInstallRDF['homepageURL'];
            }
        }
        else {
            if ($GLOBALS['boolDebugMode'] == true) {
                funcError('Release XPI does not physically exist for ' . $_addonSlug);
            }
            else {
                return null;
            }
        }
        
        // Reverse sort the xpinstall keys by version number using an anonymous function and a spaceship..
        // space.. SPACE! SPAAAAAAAAAAAAAACE!!!!!!!!
        uasort($_addonManifest['xpinstall'], function ($_xpi1, $_xpi2) {
            return $_xpi2['mtime'] <=> $_xpi1['mtime'];
        });
       
        // Set the URL for the add-on to the manifest
        $_addonManifest['metadata']['url'] = '/addon/' . $_addonManifest['metadata']['slug'] . '/';

        // metadata should be stripped of tags and be html entity'd
        foreach ($_addonManifest['metadata'] as $_key => $_value) {
            if (is_string($_value) == true) {
                $_addonManifest['metadata'][$_key] = preg_replace('/\<(.*)\>/iU', '', $_addonManifest['metadata'][$_key]);
                $_addonManifest['metadata'][$_key] = htmlentities($_addonManifest['metadata'][$_key], ENT_XHTML);
            }
        }
        
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
            $_addonPhoebusContent = str_replace("\r\n", "\n", $_addonPhoebusContent);
            $_addonPhoebusContent = str_replace("\n", "<br />\n", $_addonPhoebusContent);
            
            $_arrayPhoebusCode = array(
                'simple' => array(
                    '[b]' => '<strong>',
                    '[/b]' => '</strong>',
                    '[i]' => '<em>',
                    '[/i]' => '</em>',
                    '[u]' => '<u>',
                    '[/u]' => '</u>',
                    '[ul]' => '</p>' . "\n" .'<ul>',
                    '[/ul]' => '</ul>' . "\n" . '<p>',
                    '[ol]' => '</p>' . "\n" .'<ol>',
                    '[/ol]' => '</ol>' . "\n" . '<p>',
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
            'PD' => 'Public Domain',
            'COPYRIGHT' => '&copy;' . ' ' . date("Y") . ' - ' . $_addonManifest['metadata']['author']
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
        elseif ($_addonManifest['metadata']['license'] == 'custom' && startsWith($_addonManifest['metadata']['licenseURL'], 'http')) {
            $_addonManifest['metadata']['licenseText'] = null;
        }
        elseif ($_addonManifest['metadata']['license'] == 'unknown') {
            $_addonManifest['metadata']['license'] = null;
        }
        else {
            $_addonManifest['metadata']['licenseText'] = null;
        }
        
        if ($_addonManifest['metadata']['license'] == null) {
            $_addonManifest['metadata']['license'] = 'copyright';
            $_addonManifest['metadata']['licenseName'] = $arrayLicenses['copyright'];
            $_addonManifest['metadata']['licenseDefault'] = true;
        }

        $_addonManifest['addon']["baseURL"] = 'http://' . $GLOBALS['strApplicationURL'] . '<![CDATA[/?component=download&version=latest&id=]]>';

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
            $_addonManifest['phoebus']['xpiCount'] = $_arrayPhoebusFiles['xpinstall']['count'];
            
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