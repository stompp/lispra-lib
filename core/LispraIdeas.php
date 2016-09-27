<?php

//namespace lispra;


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

class LispraIdeas {

    //TABLE list_ideas  
    const IDEA_ID = 'idea_id';
    const IDEA_TITLE = 'idea_title';
    const IDEA_DESC = 'idea_desc';
    const TIME_STAMP = 'time_stamp';
    const TAG_ID = 'tag_id';
    const TAG_TITLE = 'tag_title';

    public function getTagsForIdea() {
        
    }

    public function getIdea() {
        
    }

    public function createTag() {
        
    }

}
