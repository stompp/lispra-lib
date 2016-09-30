<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


//include_once 'core/lispra-core.php';
include_once 'core/LispraIdeas.php';

if (!LispraCore::isUserSet()) {
    exit("YOU CANNOT PASSS!!!");
}


$i = array(
    LispraIdeas::IDEA_TITLE => "akaka IDEA TITLE",
    LispraIdeas::IDEA_DESC => "aakak IDEA DESCRIPTIONS",
    LispraIdeas::IDEA_TAGS => "pollas#nabos bukakes #teen hardcore"
);
echo json_encode(LispraIdeas::removeTagsFromIdea(11,"pollas#nabos bukakes #teen hardcore"));

//$st = "Hola     k ase  t y u  k ase   e ker   ";
////echo $st;
//$chunks = explode(" ", $st);
//
//
//$o = array();
//foreach ($chunks as $c){
//    if(strlen($c)>0){
//        $o[] = $c;
//    }
//}
//echo json_encode($o);
//$ideas = new LispraIdeas();
//echo json_encode($ideas->getIdea(3));




    