<?php
header("Content-Type: text/plain");

$file = filter_var($_GET['id'],FILTER_SANITIZE_STRING);
$type = filter_var($_GET['type'],FILTER_SANITIZE_STRING);
$observer = filter_var($_GET['o'],FILTER_SANITIZE_STRING);


function printn($arg) {
    print($arg.PHP_EOL);
}


#printn($file);
#printn($type);

if ($observer === "-NONE-" ){
    $root_url="http://www.jb.man.ac.uk/~undergrd/7mlab_data/";
} else {
    $root_url="http://www.jb.man.ac.uk/~undergrd/7mlab_data/$observer/";
}
$ext=".UNK";

if ($type==="spec") {
    $ext=".ASC";
} else if ($type==="scan") {
    $ext=".SCN";
}



#printn($root_url.$file.$ext);
#
#


print("[");
$mode="HEADER";
$delim="{";
$data = file($root_url.$file.$ext);
foreach ($data as $line){
    $line = trim($line);

    if ($line==="DATA") {
        $dat=array();
        $xval=array();
        ## Go into data mode
        $mode="DATA";
        continue;
    }
    if ($line==="END") {
        # We need to write the data segment.
        print($delim."\"DATA\":[");
        print(implode(",",$dat));
        print("]");

        print($delim."\"XVAL\":[");
        if ($type==="scan"){
            print(implode(",",$xval));
        }
        print("]".PHP_EOL);

        print("}".PHP_EOL);
        $mode="HEADER";
        $delim=",{";
        continue;
    }

    if ($mode==="HEADER") {
        $e = explode("=",$line);
        $key=trim($e[0]);
        $val=trim($e[1]);
        if (!is_numeric($val)) {
            $val = "\"".$val."\"";
        } else {
            if (round(floatval($val)) == floatval($val)){
                $val=intval($val);
            } else {
                $val=floatval($val);
            }
        }

        print($delim."\"".$key."\":".$val);
        $delim=",";
        continue;
    }
    if ($mode==="DATA") {
        if ($type==="scan"){
            $e=explode(" ",$line);
            array_push($dat,$e[1]);
            array_push($xval,$e[0]);
        } else {
            array_push($dat,$line);
        }
    }
}

print("]");
?>
