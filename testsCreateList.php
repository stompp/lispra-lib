<?php
echo "holaa";
include'core/WPLispra.php';
echo "loaded";
br("wplispra loaded");
//$db = new LispraDBHelper();

$user = new LispraUser("1");
br("user loaded");
$list_data = array(
     LISPRA_KEY_LIST_NAME => "Todo List 4",
     LISPRA_KEY_LIST_CLASS => LISPRA_LIST_CLASS_TODO,
     LISPRA_KEY_META => '{"location":"unknown","text":"This is some text"}'
 );
$user->createList($list_data);
br("LIST CREATED");
br(json_encode($user->getLists()));

$list_data = array(
     LISPRA_KEY_LIST_NAME => "Todo List 5",
     LISPRA_KEY_LIST_CLASS => LISPRA_LIST_CLASS_TODO,
     LISPRA_KEY_META => '{"location":"unknown","text":"This is some text"}'
 );
$user->createList($list_data);
br("LIST CREATED");
br(json_encode($user->getLists()));

$list_data = array(
     LISPRA_KEY_LIST_NAME => "Todo List 6",
     LISPRA_KEY_LIST_CLASS => LISPRA_LIST_CLASS_TODO,
     LISPRA_KEY_META => '{"location":"unknown","text":"This is some text"}'
 );
$user->createList($list_data);
br("LIST CREATED");
br(json_encode($user->getLists()));

//$user->deleteList(2);
//br("LIST DELETED");
//br(json_encode($user->getLists()));

//$u = new LispraUser("admin@riggitt.org");


?>
