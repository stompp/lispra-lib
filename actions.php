<?php

include_once 'core/WPLispra.php';


function get_current_lispra_user() {
    if (function_exists("wp_get_current_user")) {
        $current_user = wp_get_current_user();
        $current_user_id = $current_user->ID;
        $lispra_user_id = intval($current_user_id);

        if ($lispra_user_id > 0) {

            $u = new LispraUser($lispra_user_id);
            if ($u->isDataSet()) {
                return $u;
            }
        }
    }

    return null;
}


function executeActions($a) {
    $u = get_current_lispra_user();
    if (array_key_exists("user_actions", $a)) {
        foreach ($a["user_actions"] as $action) {
            $r = $u->executeAction($action);
            echo json_encode($r);
        }
    }
}

// get content
$c = getRequestBody();
// parse content to assoc
$a = json_decode($c, true);

executeActions($a);

?>