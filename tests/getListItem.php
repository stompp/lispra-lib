<?php

include_once '../core/Lispra.php';


$u = new LispraUser("oficina@vinalopo2020.es");

br("user : " . $u->toJSON());
$list_item_data = array(
    LISPRA_KEY_LIST_ID => 3,
    LISPRA_KEY_ID => 1,
    LISPRA_KEY_TITLE => "Trenzado",
    LISPRA_KEY_STATUS => LISPRA_STATUS_CANCELED
);

$list_item_data2 = array(
    LISPRA_KEY_LIST_ID => 3,
    LISPRA_KEY_ID => 3,
    LISPRA_KEY_TITLE => "Picaora",
    LISPRA_KEY_STATUS => LISPRA_STATUS_COMPLETE,
    LISPRA_KEY_META => "{\"what\":\"Recoger algo\"}"
);


$list_item = $u->getListItem($list_item_data);
echo json_encode($list_item);

?>
