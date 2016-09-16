<!--<!DOCTYPE html>

To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.

<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
<?php
// put your code here
?>
    </body>
</html>-->

<?php
include'../core/Lispra.php';



$meta_data = array(
    "brief_text" => "Some explanation",
    "contact_id" => 0124,
    "role" => "admin",
    "triggerDate" => "29/07/16"
);
$m = new JsonObjectHelper($meta_data);
br($m->toJson());

$a = $m->toAssoc();
echo $a->{"role"};
br("Role : " . $m->getValue("role"));
br("Contact_id : " . $m->get("contact_id"));
br("Ak : " . $m->get("ak"));

//$m->setValue("adress_idd", 699);
//br($m->toJson());
$m->set(array(
    "adress_idd" => 699,
    "contack_id" => 5698,
    "pene" => "092"
));
br($m->toJson());
$m->delete("pene");
br($m->toJson());
br(json_encode($m->get(array(
    "adress_idd",
    "contack_id"
))));
$m->delete(array(
    "adress_idd",
    "contack_id"
));
br($m->toJson());
?>

