<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function wp_get_current_lispra_user_id() {
    if (function_exists("wp_get_current_user")) {
        $current_user = wp_get_current_user();
        if(is_null($current_user)) {
            return 0;
        }
        $lispra_user_id = intval($current_user->ID);

        if ($lispra_user_id > 0) {

           return $lispra_user_id;
        }
    }

    return 0;
}

echo wp_get_current_lispra_user_id();
?>