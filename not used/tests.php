<?php


include_once 'core/Lispra.php';

$tt = "akakka";
$iv = is_numeric($tt);
br("$tt intval : $iv");
$tt = "98";
$iv = is_numeric($tt);
br("$tt intval : $iv");
br();
$u = new LispraUser("oficina@vinalopo2020.es");

br("user : " . $u->toJSON());
$list_item_data = array(
    LISPRA_KEY_LIST_ID => 3,
    LISPRA_KEY_ID => 2,
    LISPRA_KEY_TITLE => "Trenzado",
    LISPRA_KEY_STATUS => LISPRA_STATUS_CANCELED
);

$list_item_data2 = array(
    LISPRA_KEY_LIST_ID => 3,
    LISPRA_KEY_ID => 2,
    LISPRA_KEY_TITLE => "Luisa",
    LISPRA_KEY_STATUS => LISPRA_STATUS_PENDING,
//    LISPRA_KEY_META => 'Nada'
    LISPRA_KEY_META => '{"what":"Recoger algo","placeID" : @456}'
);
$list_item_data3 = array(
    LISPRA_KEY_LIST_ID => 3,
    LISPRA_KEY_ID => 3,
    LISPRA_KEY_TITLE => "Picaora",
    LISPRA_KEY_STATUS => LISPRA_STATUS_COMPLETE,
    LISPRA_KEY_META => "{\"what\":\"Recoger algo\"}"
);

//$u->createListItem($list_item_data);
//$u->createListItem($list_item_data2);
//$u->updateListItem($list_item_data);
//$u->updateListItem($list_item_data2);

$list_item = $u->getListItem($list_item_data);
br(json_encode($list_item));
$u->updateListItem($list_item_data2);
$list_item = $u->getListItem($list_item_data);
br(json_encode($list_item));
echo(createHtmlTableFromAssocsArray(array($list_item),true));
//$list_data = array(
//    LISPRA_KEY_LIST_NAME => "02/08",
//    LISPRA_KEY_LIST_CLASS => LISPRA_LIST_CLASS_TODO
//);
//
//$u->createList($list_data);
//$list_data = array(
//    LISPRA_KEY_LIST_NAME => "04/08",
//    LISPRA_KEY_LIST_CLASS => LISPRA_LIST_CLASS_TODO
//);
//
//$u->createList($list_data);
//$list_data = array(
//    LISPRA_KEY_LIST_NAME => "03/08",
//    LISPRA_KEY_LIST_CLASS => LISPRA_LIST_CLASS_TODO
//);
//
//$u->createList($list_data);
//$list_data = array(
//    LISPRA_KEY_LIST_NAME => "Tareas",
//    LISPRA_KEY_LIST_CLASS => LISPRA_LIST_CLASS_TODO
//);
//
//$u->createList($list_data);
//$lists = $u->getLists();
//br("LISTS ".json_encode($lists));
//$c_r = arrayOfAssocsToHeadersRowsAssoc($lists);
//br(" header rows : " .json_encode($c_r));
//echo createHtmlTableFromHeadersRows($c_r, true);
//$list_h = $u->getListHeader(3);
//br(json_encode($list_h));
//
//
//$list_h[LISPRA_KEY_STATUS] = LISPRA_STATUS_ACTIVE;
//$list_h[LISPRA_KEY_LIST_NAME] = "Cagar";
//$u->updateListHeader($list_h);
//$list_h = $u->getListHeader(3);
//br(json_encode($list_h));

//}
?>
