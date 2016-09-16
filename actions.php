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

// get user
$lispra_user_id = getIfSet($current_user_id, 0);
if (intval($lispra_user_id) < 1) {
    die("NO USER");
}
$u = new LispraUser($lispra_user_id);
if (!$u->isDataSet()) {
    die("USER DATA NOT SET");
}
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
}
        

?>