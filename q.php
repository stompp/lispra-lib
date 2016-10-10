<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


//include_once 'core/lispra-core.php';
include_once 'LispraIdeas.php';

if (!LispraCore::isUserSet()) {
    exit("YOU CANNOT PASSS!!!");
}

$user = LispraCore::getCurrentLispraUserID();
$data = array(
    LispraIdeasKeys::IDEA_TITLE => "Idea 29",
    LispraIdeasKeys::IDEA_DESC => "Descripción para idea 29",
    LispraIdeasKeys::IDEA_TAGS => "web java android aruino something");

$response = array();

$reponse["antes"] = LispraIdeas::userGet($user);
LispraIdeas::userDelete(1,"30,31,1458");

//$reponse["UPDATED"] = LispraIdeas::userUpdate(1, 29,$data);
$reponse["despues"] = LispraIdeas::userGet($user);
echo json_encode($reponse);
//echo json_encode(TagsUtils::parseToArray($data[LispraIdeas::IDEA_TAGS]));
//echo json_encode(LispraIdeas::userGet($user));
?>

