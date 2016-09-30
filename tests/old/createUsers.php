<?php

include_once '../core/Lispra.php';


$db = new LispraDBHelper();



$user_data = array(
    LISPRA_KEY_EMAIL => "admin@riggitt.org",
    LISPRA_KEY_PASSWORD => "admin",
    LISPRA_KEY_DISPLAY_NAME => "Riggitt"
);
br("CREATING USER");
$u = $db->createUser($user_data);
br("USER CREATED : " . $u->toJSON());

$user_data = array(
    LISPRA_KEY_EMAIL => "chemi@riggitt.org",
    LISPRA_KEY_PASSWORD => "admin",
    LISPRA_KEY_DISPLAY_NAME => "Chemi"
);
br("CREATING USER");
$u = $db->createUser($user_data);
br("USER CREATED : " . $u->toJSON());

$user_data = array(
    LISPRA_KEY_EMAIL => "cortado@vinalopo2020.es",
    LISPRA_KEY_PASSWORD => "admin",
    LISPRA_KEY_DISPLAY_NAME => "Cortado"
);
br("CREATING USER");
$u = $db->createUser($user_data);
br("USER CREATED : " . $u->toJSON());
$user_data = array(
    LISPRA_KEY_EMAIL => "oficina@vinalopo2020.es",
    LISPRA_KEY_PASSWORD => "admin",
    LISPRA_KEY_DISPLAY_NAME => "oficina"
);
br("CREATING USER");
$u = $db->createUser($user_data);
br("USER CREATED : " . $u->toJSON());
//$iv = 200;
//br(intval($iv));
//br("Hola k ase");
$u = new LispraUser("admin@riggitt.org");
br("user : " . $u->toJSON());


?>
