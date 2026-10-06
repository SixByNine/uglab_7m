<?php
header("Content-Type: text/json");

function printn($arg) {
    print($arg.PHP_EOL);
}


$root_url="http://www.jb.man.ac.uk/~undergrd/7mlab_data/7m_observers.list";

print("[");
$delim="";
$data = file($root_url);
foreach ($data as $line){
    $line = trim($line);
    print($delim."\"".$line."\"");
    $delim=",";
}

print("]\n");
?>
