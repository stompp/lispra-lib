<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once 'core/WPLispra.php';


br("user_lists.php START");

$lispra_user_id = function_exists("get_current_user_id") ?
        getIfSet(get_current_user_id(), 0) : 0;


if (intval($lispra_user_id) > 0) {

    $u = new LispraUser($lispra_user_id);
    br("user : " . $u->isDataSet());
    echo "<div>";
   echo(createHtmlTableFromAssocsArray($u->getLists(),true));
   echo "</div>";
//    br(json_encode($r));
} else {
    br("NO USER ID");
}

br("user_lists.php END");
?>