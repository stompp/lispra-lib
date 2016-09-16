<?php

include_once 'core/Lispra.php';


$u_param = getIfSet($_REQUEST['user'],'not set');
br("user : $u_param");
$scripts_param = getIfSet($_REQUEST['scripts']);
$scripts = array();
if(is_string($scripts_param))
    if(isJson($scripts_param))$scripts = json_decode ($scripts_param);
    else $scripts = explode(",", $scripts_param);
else if(is_array($scripts_param)) $scripts = $scripts_param;

var_dump($scripts);
br();


?>
