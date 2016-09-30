<?php

include_once realpath(dirname(__FILE__)).'/core/WPLispra.php';

function echo_error($str) {
    $a = array(
        "isDataSet" => 0,
        "error" => $str,
        "body" => getRequestBody());
    echo json_encode($a);
    exit();
}

//function wp_get_current_lispra_user_id() {
//    if (function_exists("wp_get_current_user") && function_exists("get_current_user_id")) {
//        return get_current_user_id();
//    }else{
//        echo_error("NO WP");
//    }
//
//    return 0;
//}
//
//
//function get_current_lispra_user() {
//    
//        $lispra_user_id = wp_get_current_lispra_user_id();
//
//        if ($lispra_user_id > 0) {
//
//            $u = new LispraUser($lispra_user_id);
//            if ($u->isDataSet()) {
//                return $u;
//            }else{
//                echo_error("get_current_lispra_user data not set");
//            }
//        }
//    
//
//    return null;
//}
//
//
//
//function executeActions($a) {
//    $u = get_current_lispra_user();
//    if (is_null($u)) {
//        echo_error("User is null");
//        return;
//    }
//    if (array_key_exists("user_actions", $a)) {
//        foreach ($a["user_actions"] as $action) {
//            $r = $u->executeAction($action);
//            echo json_encode($r);
//        }
//    }
//}
function get_current_lispra_user() {
    
        $lispra_user_id = LispraCore::getCurrentLispraUserID();
echo_error("$lispra_user_id");
        if ($lispra_user_id > 0) {

            $u = new LispraUser($lispra_user_id);
            return $u;
//            if ($u->isDataSet()) {
//                return $u;
//            }else{
//                echo_error("get_current_lispra_user data not set");
//            }
        }
    

    return null;
}
function executeActions1($a) {
    $u = get_current_lispra_user();
    if (is_null($u)) {
        echo_error("User is null");
        return;
    }
    if (array_key_exists("user_actions", $a)) {
        foreach ($a["user_actions"] as $action) {
            $r = $u->executeAction($action);

            $response = array(
                "isDataSet" => 1,
                "data" => $r
            );
            echo json_encode($response);
        }
    }
}

function oldActions() {

    // get content
    $c = getRequestBody();
    if (isNullorEmpty($c)) {
        echo_error("Content null or empty");
        return;
    }
// parse content to assoc
    $a = json_decode($c, true);
    $e = json_last_error();
    if ($e == JSON_ERROR_NONE) {
        executeActions($a);
    } else {
        echo_error("JSON ERROR :" . json_last_error_msg());
    }
}

function doActions() {

    // get content
    $c = getRequestBody();
    if (isNullorEmpty($c)) {
        echo_error("Content null or empty");
        return;
    }
// parse content to assoc
    $a = json_decode($c, true);
    $e = json_last_error();
    if ($e == JSON_ERROR_NONE) {
        executeActions1($a);
    } else {
        echo_error("JSON ERROR :" . json_last_error_msg());
    }
}

doActions();
//oldActions();
?>