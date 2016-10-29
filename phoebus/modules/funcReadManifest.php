<?php
// == | funcReadManifest | ===============================================

function funcReadManifest($_addonType, $_addonSlug, $_mode, $_useNewManifest) {
    $_addonDirectory = $_addonType . 's/' . $_addonSlug . '/';
    $_addonBasePath = './datastore/' . $_addonDirectory;
    $_addonManifestINIFile = 'manifest.ini';
    $_addonPhoebusManifestFile = 'phoebus.manifest';
    $_addonPhoebusContentFile = 'phoebus.content';
    
    if ($_useNewManifest == true && file_exists($_addonBasePath . $_addonPhoebusManifestFile)) {
        $_addonManifest = parse_ini_file($_addonBasePath . $_addonPhoebusManifestFile, true);
        if ($_addonManifest != false) {
            // INI has depth and identical section name issues so we need to mangle it
            // Create a temporary array that we can easily manipulate
            $_addonManifestVersions = $_addonManifest;
            
            // Drop the addon and metadata keys off the temporary array
            unset($_addonManifestVersions['addon']);
            unset($_addonManifestVersions['metadata']);
            
            // mangle filename.xpi sections into a subkey
            // we are now working on the add-on manifest array
            foreach ($_addonManifestVersions as $_key => $_value) {
                unset($_addonManifest[$_key]);
                $_addonManifest['xpi'][$_key] = $_value;
            }
            
            // clear the temporary array out of memory
            unset($_addonManifestVersions_);
            
            // Parse phoebus.content
            if (file_exists($_addonBasePath . $_addonPhoebusContentFile)) {
                // Read phoebus.content
                $_addonPhoebusContent = file_get_contents($_addonBasePath . $_addonPhoebusContentFile);
                
                // html encode phoebus.content
                $_addonPhoebusContent = htmlentities($_addonPhoebusContent, ENT_XHTML);
                
                // normalize line endings
                $_addonPhoebusContent = str_replace("\r\n", "\n", $_addonPhoebusContent);
                
                // automagically turn newlines into <br />
                $_addonPhoebusContent = str_replace("\n", "<br />\n", $_addonPhoebusContent);
            
                // create a temporary array that contains the strs to simple pseudo-bbcode to real html
                $_arrayPhoebusCodeSimple = array(
                    '[b]' => '<strong>',
                    '[/b]' => '</strong>',
                    '[i]' => '<em>',
                    '[/i]' => '</em>',
                    '[u]' => '<u>',
                    '[/u]' => '</u>',
                    '[ul]' => '<ul>',
                    '[/ul]' => '</ul>',
                    '[li]' => '<li>',
                    '[/li]' => '</li>',
                    '[section]' => '<h3>',
                    '[/section]' => '</h3>'
                );
                
                // create a temporary array that contains the regex to convert pseudo-bbcode to real html
                $_arrayPhoebusCodeRegex = array(
                    '\[url=(.*)\](.*)\[\/url\]' => '<a href="$1" target="_blank">$2</a>',
                    '\[url\](.*)\[\/url\]' => '<a href="$1" target="_blank">$1</a>',
                    '\[img(.*)\](.*)\[\/img\]' => '<img src="$2"$1 />'
                );
                
                // str replace pseudo-bbcode with real html
                foreach ($_arrayPhoebusCodeSimple as $_key => $_value) {
                    $_addonPhoebusContent = preg_replace('/' . $_key . '/iU', $_value, $_addonPhoebusContent);
                }
                
                // Regex replace pseudo-bbcode with real html
                foreach ($_arrayPhoebusCodeRegex as $_key => $_value) {
                    $_addonPhoebusContent = preg_replace('/' . $_key . '/iU', $_value, $_addonPhoebusContent);
                }
                
                // Clear the temporary arrays out of memory
                unset($_arrayPhoebusCodeSimple);
                unset($_arrayPhoebusCodeRegex);
                
                // Assign parsed phoebus.content to the add-on manifest array
                $_addonManifest['metadata']['longDescription'] = $_addonPhoebusContent;
            }
            else {
                // Since there is no phoebus.content use the short description
                $_addonManifest['metadata']['longDescription'] = $_addonManifest['metadata']['shortDescription'];
            }
            
            // Generate a sha256 hash on the fly for the add-on
            if (file_exists($_addonBasePath . $_addonManifest['addon']['release'])) {    
                $_addonManifest['addon']['hash'] = hash_file('sha256', $_addonBasePath . $_addonManifest['addon']['release']);
            }
            else {
                funcError('Could not find ' . $_addonManifest["xpi"]);
            }
            
            // assign the baseURL to the add-on manifest array
            $_addonManifest['addon']["baseURL"] = 'https://dev.addons.palemoon.org/datastore/' . $_addonDirectory;
            
            // We are using the new manifest
            $_addonManifest['isNewManifest'] = true;
            return $_addonManifest;
        }
        else {
            funcError('Unable to read manifest file');
        }
    }
    elseif ($_useNewManifest == false && file_exists($_addonBasePath . $_addonManifestINIFile)) {
        $_addonManifest = parse_ini_file($_addonBasePath . $_addonManifestINIFile);
        if ($_addonManifest != false) {
            // $_mode 0 = (future for fe) 1 = aus, 2 = download
            switch ($_mode) {
                case 0:
                    $_arrayUnsetKeys = null;
                    break;
                case 1:
                    $_arrayUnsetKeys = array('id', 'compat', 'name', 'author', 'description');
                    if (file_exists($_addonBasePath . $_addonManifest["xpi"])) {    
                        $_addonManifest["hash"] = hash_file('sha256', $_addonBasePath . $_addonManifest["xpi"]);
                    }
                    else {
                        funcError('Could not find ' . $_addonManifest["xpi"]);
                    }
                    
                    switch($_SERVER["HTTP_X_FORWARDED_HOST"]) {
                        case 'dev.addons.palemoon.org':
                            $_addonManifest["baseurl"] = 'http://dev.addons.palemoon.org/datastore/' . $_addonDirectory;
                            break;
                        default:
                            $_addonManifest["baseurl"] = 'https://addons.palemoon.org/phoebus/datastore/' . $_addonDirectory;
                            break;
                    }
                    break;
                case 2:
                    $_arrayUnsetKeys = array('id', 'compat', 'minVer', 'maxVer', 'name', 'author', 'description');
                    $_addonManifest['basepath'] = $_addonBasePath;
                    break;
            }
            if ($_arrayUnsetKeys != null) {
                foreach ($_arrayUnsetKeys as $_value) {
                    unset($_addonManifest[$_value]);
                }
            }
            $_addonManifest['isNewManifest'] = false;
            return $_addonManifest;
        }
        else {
            funcError('Unable to read manifest file');
        }
    }
    else {
        funcError('Unable to find manifest file');
    }
}

// ============================================================================
?>