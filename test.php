<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once 'core/WPLispra.php';





function callTestScript($name) {
    $scr = "tests/$name.php";
    if (file_exists($scr)) {
        include_once "$scr";
    } else {
        echo "TEST SCRIPT NOT FOUND";
    }
}

$name = getRequestParameterIfSet("t", "default");

callTestScript($name);

?>