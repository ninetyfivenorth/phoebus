<?php

header('Content-Type: text/plain');
print( $_SERVER['REQUEST_URI'] . "\n" . $strRequestPage . "\n");
$parsed = parse_url($_SERVER['REQUEST_URI']);
var_dump($parsed);

?>