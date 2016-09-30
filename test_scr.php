<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once realpath(dirname(__FILE__)).'/core/lispra-core.php';
//echo "gola";

//if(!LispraCore::isUserSet()){
//    echo("YOU CANNOT PASS!!");
////    exit();
//}
function callScript($scrPath) {
    $scr = realpath(dirname(__FILE__))."/$scrPath.php";
    if (file_exists($scr)) {
        include_once $scr;
    } else {
        echo "SCRIPT NOT FOUND";
    }
}

function callTestScript($name) {
    $scr = "tests/$name";
    callScript($scr);
}




$t = getRequestParameterIfSet("t", null);
$p = getRequestParameterIfSet("p", null);


if (!is_null($t)) {
    callTestScript($t);
}elseif (!is_null($p)) {
    callScript(urldecode($p));
}else{
    echo "Nothing to do";
}

?>