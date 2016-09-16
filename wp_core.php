<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


//function sayU(){
//    echo "UUUUUUUUUUU";
//}
include_once 'core/WPLispra.php';
include_once 'components/components.php';

//function lispra_wp_core_get_component_content(&$func_name) {
//
//    $content_desc = "";
//    $exists = false;
//
//    if (isNullorEmpty($func_name)) {
//        $content_desc = "Function name null or empty";
//    } else if (!function_exists($func_name)) {
//        $content_desc = "Function not found";
//    } else
//        $exists = true;
//
//    ob_start();
//
//    if ($exists) {
//        call_user_func($func_name);
//    } else {
//        $attr = array(
//            "class" => "alert-error",
//            "content" => "<strong>Lispra Error!</strong>" . $content_desc
//        );
//        bootstrap_dismissable_alert($attr);
//    }
//
//    $output = ob_get_contents();
//    ob_end_clean();
//    return $output;
//}

/**
 * Data is an array containing
 * tag => function name
 * content => content to embed in component(string by now)
 * attr_key_n => attr:value_n... Rest of keys are attr values
 *  
 * @param type $data
 * @return type
 */
//function lispra_wp_core_get_component_content2($func_name, $attr = null, $content = null) {
//
//    $func_name_e = "lispra_dismissable_alert";
//    $attr_e = array("class" => "alert-error");
//    $content_e = "<strong>Lispra Error!</strong> ";
//
//    $exists = false;
//    if (isNullorEmpty($func_name)) {
//        $content_e .= "Function name null or empty";
//    } else if (!function_exists($func_name)) {
//        $content_e .= "Function not found";
//    } else {
//        $exists = true;
//    }
//
//    ob_start();
//
//    if ($exists) {
//        call_user_func_array($func_name, array($attr, $content));
//    } else {
//        call_user_func_array($func_name_e, array($attr_e, $content_e));
//    }
//
//    $output = ob_get_contents();
//    ob_end_clean();
//    return $output;
//}
//
//function lispra_wp_core_echo_component($func_name, $attr, $content) {
//    $output = lispra_wp_core_get_component_content2($func_name, $attr, $content);
//    echo $output;
//    return $output;
//}

function lispra_get_component_error($content_e) {

    $func_name_e = "lispra_dismissable_alert";
    $attr_e = array("class" => "alert-error");
    call_user_func_array($func_name_e, array($attr_e, "<strong>lispra:wp_core:error </strong>" . $content_e));
}

function lispra_get_component($func_name, $attr = null, $content = null) {

    $e = "Invalid input";
    if (is_string($func_name)) {

        if (function_exists($func_name)) {
            $a = is_null($attr) ? array() : $attr;
            $c = is_null($content) ? array() : $content;
            return call_user_func_array($func_name, array($a, $c));
        }
        $e = "Component [\"$func_name\"] not found";
    }
    lispra_wp_core_echo_error("lispra_get_component", $e);
}

function lispra_get_component_content($func_name, $attr = null, $content = null) {

    $a = is_null($attr) ? array() : $attr;
    $c = is_null($content) ? array() : $content;
    ob_start();
    lispra_get_component($func_name, $a, $c);
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}

?>