<?php

/**
 * Input will be a json like
 * { 
 *      "user" : @userID,
 *      "actions" @array[actions array]
 * }
 * 
 * Actiones will be like
 * 
 * {   "action" : @actionIdentifier
 *     "data"   : @objet 
 * }
 * 
 * Example
 * {
 *      "user" : 3,
 *      "actions" : [
 *          {
 *              "action" : "createL"},
 *          {},
 *          {},
 *      ]
 * }
 */
include_once 'core/WPLispra.php';

$current_user = null;
$current_user_id = 0;
$lispra_user_id = 0;
// get user
if(function_exists("wp_get_current_user")){
    $current_user = wp_get_current_user();
    $current_user_id = $current_user->ID;
    $lispra_user_id = intval($current_user_id);
}

if (intval($lispra_user_id) < 1) {
//    echo "NO USER";
    die("NO USER");
    return;
}

$u = new LispraUser($lispra_user_id);
if (!$u->isDataSet()) {
//    echo "USER DATA NOT SET";
    die("USER DATA NOT SET");
    return;
}
// echo "USER CHACHI";
// get content
$c = getRequestBody();
// parse content to assoc
$a = json_decode($c,true);

if(array_key_exists("user_actions", $a)){
//    br("USER ACTIONS EXISTS, EXECUTING ACTIONS..");
    foreach ($a["user_actions"] as $action) {
        $r = $u->executeAction($action);
        echo json_encode($r);
//        br(json_format_encode($r, true));
        
    }
}else{
//    echo "NO FUCKING user_actions KEY MAAAAN";
}

        

?>