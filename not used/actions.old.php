<?php
include_once 'core/WPLispra.php';

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

// get input body, must be json

$json = getRequestBody();
//decode input
$d = json_decode($json,true);

$u = new LispraUser($d["user"]);


$actions = $d["actions"];

foreach ($actions as $action) {


    switch ($action["action"]) {
        case "create_list":
            $u->createList($action["data"]);
            break;
        case "update_list":
            break;
        case "update_list_header":
            break;
        case "update_list_content":
            break;
        case "get_list_content":
            break;
        case "update_":
            break;
        case "update_list_header":
            break;
        case "update_list_header":
            break;

        default:
            break;
    }
    
}