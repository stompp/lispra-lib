<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function wp_get_current_lispra_user_id() {
    if (function_exists("wp_get_current_user") && function_exists("get_current_user_id")) {
        return get_current_user_id();
    }

    return 0;
}



?>