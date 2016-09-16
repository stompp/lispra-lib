<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


require_once 'wp_core.php';

$s = getIfSet($_REQUEST["s"],"");
if(!isNullorEmpty($s)){
    echo "iesooo";
}  else {
echo "puta";    
}
