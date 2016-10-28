<?php
// == | funcReadManifest | ===============================================

function funcReadManifest($_addonType, $_addonSlug, $_mode, $_useNewManifest) {
    $_addonDirectory = $_addonType . 's/' . $_addonSlug . '/';
    $_addonBasePath = '../datastore/' . $_addonDirectory;
    $_addonManifestINIFile = 'manifest.ini';
    $_addonPhoebusManifestFile = 'phoebus.manifest';
    $_addonPhoebusContentFile = 'phoebus.content';
    
    if ($_useNewManifest == true && file_exists($_addonBasePath . $_addonPhoebusManifestFile)) {
        $_addonManifest = parse_ini_file($_addonBasePath . $_addonPhoebusManifestFile, true);
        if ($_addonManifest != false) {
            $_addonManifestVersions = $_addonManifest;
            unset($_addonManifestVersions['addon']);
            unset($_addonManifestVersions['metadata']);
            
            foreach ($_addonManifestVersions as $_key => $_value) {
                unset($_addonManifest[$_key]);
                $_addonManifest['xpi'][$_key] = $_value;
            }
            unset($_addonManifestVersions_);
            
            if (file_exists($_addonBasePath . $_addonPhoebusContentFile)) {
                $_addonPhoebusContent = file_get_contents($_addonBasePath . $_addonPhoebusContentFile);
                $_addonPhoebusContent = htmlentities($_addonPhoebusContent, ENT_XHTML);
                $_addonPhoebusContent = str_replace("\r\n", "\n", $_addonPhoebusContent);
                $_addonPhoebusContent = str_replace("\n", "<br />\n", $_addonPhoebusContent);
                
                $arrayPhoebusCode = array(
                    '\[url="(.*)"\](.*)\[\/url\]' => '<a href="$1" target="_blank">$2</a>',
                    '\[img(.*)\](.*)\[\/img\]' => '<img src="$2"$1 />'
                );

                foreach ($arrayPhoebusCode as $_key => $_value) {
                    $_addonPhoebusContent = preg_replace('/' . $_key . '/iU', $_value, $_addonPhoebusContent);
                }

                $_addonManifest['metadata']['longDescription'] = $_addonPhoebusContent;
            }
            else {
                $_addonManifest['metadata']['longDescription'] = $_addonManifest['metadata']['shortDescription'];
            }
            
            if (file_exists($_addonBasePath . $_addonManifest['addon']['release'])) {    
                $_addonManifest['addon']['hash'] = hash_file('sha256', $_addonBasePath . $_addonManifest['addon']['release']);
            }
            else {
                funcError('Could not find ' . $_addonManifest["xpi"]);
            }
            
            $_addonManifest['addon']["baseURL"] = 'https://dev.addons.palemoon.org/datastore/' . $_addonDirectory;
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