<?php
header("Content-Type: text/json");

$observer = filter_var($_GET['o'],FILTER_SANITIZE_STRING);
$type = filter_var($_GET['type'],FILTER_SANITIZE_STRING);
function printn($arg) {
    print($arg.PHP_EOL);
}

$ext="ZZZZZ";
if ($type==="spec") {
    $ext="ASC";
} else if ($type=="scan") {
    $ext="SCN";
}

if ($observer=="-NONE-") {
    $root_url="http://www.jb.man.ac.uk/~undergrd/asclist.dat";
}else {
    $root_url="http://www.jb.man.ac.uk/~undergrd/7mlab_data/$observer.txt";
}
print("[");
$delim="";
$data = file($root_url);
foreach ($data as $line){
    $line = trim($line);
    $parts=pathinfo($line);
    if ($parts['extension'] === $ext){
        print($delim."\"".$parts['filename']."\"");
        $delim=",";
    }
}

print("]\n");
?>
