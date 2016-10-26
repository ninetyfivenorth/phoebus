<?php

$arrayDatabases = array(
    'dbExtensions' => '../../backend/modules/dbExtensions.php',
    'dbThemes' => '../../backend/modules/dbThemes.php',
    'dbLangPacks' => '../../backend/modules/dbLangPacks.php',
    'dbSearchPlugins' => '../../backend/modules/dbSearchPlugins.php',
    'dbExternals' => '../../backend/modules/dbExternals.php'
)

foreach($arrayDatabases as $_key => $_value) {
    include_once($_value);
}

?>
