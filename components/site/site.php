<?php

if ($_SERVER['REQUEST_URI'] == '/') {
    $strRequestPath = "/";
}
else {
    $strRequestPath = funcHTTPGetValue('path');
}

header('Content-Type: text/plain');
print( $_SERVER['REQUEST_URI'] . "\n" . $strRequestPath . "\n");
$parsed = parse_url($_SERVER['REQUEST_URI']);
var_dump($parsed);

?>