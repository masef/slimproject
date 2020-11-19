<?php

function encryps($plain_arr,$c_plain_arr,$temp,$cou,$k){
    $done=false;
    if($cou>($c_plain_arr-1)){ 
        $cou = ($cou-($c_plain_arr-1));
        $done=true;
        encryps($plain_arr,$c_plain_arr,$temp,$cou,$k); 
    }
    else if($plain_arr[$cou]=="|"){ 
        $cou=$cou+1;
        $all_null = count(array_filter($plain_arr, function ($a) { return $a == "|";}));
        if($all_null==$c_plain_arr){ $done=true; var_dump(implode('',$temp)); }
        else{ $done=true; encryps($plain_arr,$c_plain_arr,$temp,$cou,$k); } 
    }
    
    if($done==false){
        array_push($temp,$plain_arr[$cou]);
        $plain_arr[$cou]="|";
        $cou=$cou+$k;
        $done=true;
        encryps($plain_arr,$c_plain_arr,$temp,$cou,$k);
    }
}

$plain = "TELEGRAM";
$k = 3;

$plain_arr = str_split($plain);
$c_plain_arr = count($plain_arr);
$temp = array();
$cou=0;
encryps($plain_arr,$c_plain_arr,$temp,$cou,$k);

?>