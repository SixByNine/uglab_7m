<?php
header("Content-Type: text/plain");


$file = filter_var($_GET['id'],FILTER_SANITIZE_STRING);
$type = filter_var($_GET['type'],FILTER_SANITIZE_STRING);
$count= filter_var($_GET['c'],FILTER_SANITIZE_STRING);
$observer = filter_var($_GET['o'],FILTER_SANITIZE_STRING);
header('Content-Disposition: attachment; filename="'.$file."_".$type."_".$count.'.csv"');

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


$mode="HEADER";
$data = file($root_url.$file.$ext);
foreach ($data as $line){
    $line = trim($line);

    if ($line==="DATA") {
        $mode="DATA";
        $idata = 0;
        $v0=$v-$dv*$nch/2.0-$dv/2.0;
        $nu0=$nu-$dnu*$nch/2.0-$dnu/2.0;

        continue;
    }
    if ($line==="END") {
        $mode="HEADER";
        $count -= 1;
        continue;
    }
    if ($mode==="HEADER") {
        $e = explode("=",$line);
        $key=trim($e[0]);
        $val=trim($e[1]);
        if ($key=="DV") {
            $dv = $val;
        }
        if ($key=="V") {
            $v = $val;
        }
        if ($key=="DNU") {
            $dnu = $val;
        }
        if ($key=="NU") {
            $nu = $val;
        }
        if ($key=="NCH") {
            $nch = $val;
        }

    }


    if ($mode==="DATA" && $count == 0) {
        if ($type==="scan") {
            $e=explode(" ",$line);
            print($idata.",".$e[0].",".$e[1].PHP_EOL);
        } else {
            $vel = $v0 + $dv*$idata;
            $freq = $nu0 + $dnu*$idata;

            print($idata.",".$freq.",".$vel.",".$line.PHP_EOL);
        }
        $idata++;
    }
}

?>
