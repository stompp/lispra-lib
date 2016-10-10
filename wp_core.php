<?php

include_once 'core/WPLispra.php';
include_once 'components/components.php';

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