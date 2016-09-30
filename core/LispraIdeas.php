<?php

//namespace lispra;

require_once 'lispra-core.php';


function tags_extract_tags_from_string($tags_string) {
    $tags_str = str_replace("#", " ", $tags_string);
    $chunks = array_filter(explode(' ', $tags_str), 'strlen');
    return $chunks;
}

function tags_validate_tags_array_values(&$tags_array) {
    $tags_array = str_replace("#", "", $tags_array);
    $tags_array = array_filter($tags_array, 'strlen');
    return $tags_array;
}

//function parseToTagsArray($tags_str_or_array) {
//    $a = array();
//    if (is_string($tags_str_or_array)) {
//        $tags_str_or_array = str_replace("#", " ", $tags_str_or_array);
//        $a = array_filter(explode(' ', $tags_str_or_array), 'strlen');
//    } elseif (is_array($tags_str_or_array)) {
//        $tags_str_or_array = str_replace("#", "", $tags_str_or_array);
//        $a = array_filter($tags_str_or_array, 'strlen');
//    }
//}

class LispraIdeas implements LispraKeys {

    // TABLES
    const IDEAS_USER_IDEAS_TABLE = "lispra_ideas_user_ideas";
    const IDEAS_TABLE = "lispra_ideas";
    const IDEAS_TAG_TITLES_TABLE = "lispra_ideas_tag_titles";
    const IDEAS_TAGS_TABLE = "lispra_ideas_tags";
    // KEYS
    const IDEA_ID = 'idea_id';
    const IDEA_TITLE = 'idea_title';
    const IDEA_DESC = 'idea_desc';
    // JSON/ASSOC KEYS
    const IDEA_TAGS = 'idea_tags';

    public static function log($method, $msg) {
        LispraLog::classLog("LispraIdeas", $method, $msg);
    }

    public static function logError($method, $msg) {
        LispraLog::classError("LispraIdeas", $method, $msg);
    }

    public static function parseTagsString($tags_string) {

        if (is_array($tags_string)) {
            foreach ($tags_string as $key => $value) {
                if (startsWith($value, "#")) {
                    $tags_string[$key] = str_replace("#", "", $value);
                }
            }
            return $tags_string;
        }
        $tags_string = str_replace("#", " ", $tags_string);
        $chunks = explode(" ", $tags_string);


        $tags = array();
        foreach ($chunks as $c) {
            if (strlen($c) > 0) {
                $tags[] = $c;
            }
        }


        return $tags;
    }

    public static function getTagsIDs($tags_str_or_array) {

        $stmt = null;
        $db = null;
        $out = array();

        try {
            $db = LispraCore::getPDODB();

            $tags = self::parseTagsString($tags_str_or_array);
            foreach ($tags as &$tag) {
                $tag = "'$tag'";
            }
            $s = implode(",", $tags);


            $stmt = $db->prepare("SELECT tag_id FROM lispra_ideas_tag_titles WHERE tag_title IN ($s)");
            $stmt->execute();

            $out = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
//            return $sql;
        } catch (Exception $exc) {
            self::logError("getTagsIDs", $exc->getMessage());
        }

        $stmt = null;
        $db = null;
        return $out;
    }

//    public static function getTagsByTitle($tags_str_or_array) {
//
//        $stmt = null;
//        $db = null;
//        $out = array();
//
//        try {
//            $db = LispraCore::getPDODB();
//
//            $tags = self::parseTagsString($tags_str_or_array);
//            foreach ($tags as &$tag) {
//                $tag = "'$tag'";
//            }
//            $s = implode(",", $tags);
//
//
//            $stmt = $db->prepare("SELECT * FROM lispra_ideas_tag_titles WHERE tag_title IN ($s)");
//            $stmt->execute();
//
//            $out = $stmt->fetchAll(PDO::FETCH_ASSOC);
////            return $sql;
//        } catch (Exception $exc) {
//            self::logError("getTagsIDs", $exc->getMessage());
//        }
//
//        $stmt = null;
//        $db = null;
//        return $out;
//    }

    public static function addTags($tags_str_or_array) {
        $tags = self::parseTagsString($tags_str_or_array);
        if (count($tags) < 1) {
            self::log("addTags", "empty tags");
            return 0;
        }
        $stmt = null;
        $db = null;
        $rowCount = 0;

//        INTO table_tags (tag) VALUES ('tag_a'),('tab_b'),('tag_c') ON DUPLICATE KEY UPDATE tag=tag;
        try {
            $db = LispraCore::getPDODB();

            $ft = array();
            foreach ($tags as $tag) {
                $ft[] = "('$tag')";
            }
            $s = implode(",", $ft);
            $stmt = $db->prepare("INSERT IGNORE INTO lispra_ideas_tag_titles (tag_title) VALUES $s ON DUPLICATE KEY UPDATE tag_title=tag_title");
//            $stmt = $db->prepare("INSERT INTO lispra_ideas_tag_titles (tag_title) VALUES $s ON DUPLICATE KEY UPDATE tag_title=tag_title");
            $stmt->execute();
            $rowCount = $stmt->rowCount();
        } catch (PDOException $exc) {
            self::logError("addTags", $exc->getMessage());
        }
        $stmt = null;
        $db = null;
        return $rowCount;
    }

    public static function getIdeaTags($idea_id) {

        $stmt = null;
        $db = null;
        $out = array();
        try {
            $db = LispraCore::getPDODB();
            $sql = "SELECT * from lispra_ideas_tag_titles WHERE tag_id IN (SELECT tag_id from lispra_ideas_tags WHERE idea_id = $idea_id)";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $out = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            self::logError("getIdeaTags", $exc->getMessage());
        }

        $stmt = null;
        $db = null;
        return $out;
    }

    public static function addTagsToIdea($idea_id, $tags_str_or_array) {

        $stmt = null;
        $db = null;
        $rowCount = 0;

        $tags = self::parseTagsString($tags_str_or_array);
        if (count($tags) == 0) {
            self::logError("addTagsToIdea", "tags size is 0");
            return $rowCount;
        }


        try {
            self::addTags($tags);
            $ids = self::getTagsIDs($tags);
            if (count($ids) == 0) {
                self::logError("addTagsToIdea", "ids size is 0");
                return $rowCount;
            }


            $db = LispraCore::getPDODB();
            $f = array();
            foreach ($ids as $id) {
                $f[] = "($idea_id,$id)";
            }
            $s = implode(",", $f);

            $stmt = $db->prepare("INSERT IGNORE INTO lispra_ideas_tags (idea_id,tag_id) VALUES $s ");
            $stmt->execute();
            $rowCount = $stmt->rowCount();
        } catch (Exception $ex) {
            self::logError("addTagsToIdea", "Exception => " . $ex->getMessage());
        } catch (PDOException $e) {
            self::logError("addTagsToIdea", "PDOException => " . $e->getMessage());
        }


        $stmt = null;
        $db = null;
        return $rowCount;
    }

    public static function removeTagsFromIdea($idea_id, $tags_str_or_array) {
        $stmt = null;
        $db = null;
        $rowCount = 0;

        $tags = self::parseTagsString($tags_str_or_array);
        if (count($tags) == 0) {
            self::logError("removeTagsFromIdea", "tags size is 0");
            return $rowCount;
        }


        try {

            $ids = self::getTagsIDs($tags);
            if (count($ids) == 0) {
                self::logError("removeTagsFromIdea", "ids size is 0");
                return $rowCount;
            }
            $s = implode(",", $ids);
            $db = LispraCore::getPDODB();



            $stmt = $db->prepare("DELETE FROM lispra_ideas_tags WHERE idea_id = $idea_id AND tag_id IN($s)");
            $stmt->execute();
            $rowCount = $stmt->rowCount();
        } catch (Exception $ex) {
            self::logError("removeTagsFromIdea", "Exception => " . $ex->getMessage());
        } catch (PDOException $e) {
            self::logError("removeTagsFromIdea", "PDOException => " . $e->getMessage());
        }


        $stmt = null;
        $db = null;
        return $rowCount;
    }

    public static function clearTagsFromIdea($idea_id) {

        $stmt = null;
        $db = null;
        $rowCount = 0;

        try {
            $db = LispraCore::getPDODB();
            $stmt = $db->prepare("DELETE FROM lispra_ideas_tags WHERE idea_id=$idea_id");
            $stmt->execute();
            $rowCount = $stmt->rowCount();
        } catch (Exception $ex) {
            self::logError("clearTagsFromIdea", "Exception => " . $ex->getMessage());
        } catch (PDOException $e) {
            self::logError("clearTagsFromIdea", "PDOException => " . $e->getMessage());
        }


        $stmt = null;
        $db = null;
        return $rowCount;
    }

    public static function setTagsForIdea($idea_id, $tags_str_or_array) {
        if ($idea_id > 0) {
            self::clearTagsFromIdea($idea_id);
            self::addTagsToIdea($idea_id, $tags_str_or_array);
        }
    }

    public static function getIdeaHeader($idea_id) {

        $stmt = null;
        $db = null;
        $out = array();
        try {
            $db = LispraCore::getPDODB();
            $stmt = $db->prepare(PDOBPSCreator::selectRowWhereColumnsEquals(self::IDEAS_TABLE, array(self::IDEA_ID)));
            $stmt->execute(PDOBPSCreator::assoc2Params(array(self::IDEA_ID => $idea_id)));

            $out = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $out = $out[0];
        } catch (Exception $exc) {
            self::logError("getIdeaHeader", $exc->getMessage());
        }

        $stmt = null;
        $db = null;
        return $out;
    }

    public static function getIdea($idea_id) {
        $out = array();
        try {
            $out = self::getIdeaHeader($idea_id);
            if (!isNullorEmpty($out)) {
                $out[self::IDEA_TAGS] = self::getIdeaTags($idea_id);
            }
        } catch (Exception $exc) {
            self::logError("getIdeaByID", $exc->getMessage());
        }

        return $out;
    }

    public static function deleteIdea($idea_id) {
        self::clearTagsFromIdea($idea_id);

        $stmt = null;
        $db = null;

        $rowCount = 0;
        try {

            $db = LispraCore::getPDODB();
            $stmt = $db->prepare("DELETE FROM lispra_ideas WHERE idea_id=$idea_id");
            $stmt->execute();
            $rowCount = $stmt->rowCount();
//            $stmt = null;
//            $stmt = $db->prepare("DELETE FROM lispra_ideas_user_ideas WHERE idea_id=$idea_id");
//            $stmt->execute();
            $stmt = null;
            $db = null;
        } catch (PDOException $e) {
            self::logError("deleteIdea", "PDOException => " . $e->getMessage());
        } catch (Exception $ex) {
            self::logError("deleteIdea", "Exception => " . $ex->getMessage());
        }
        $stmt = null;
        $db = null;
        return $rowCount;
    }

    public static function createIdea($data) {

        $stmt = null;
        $db = null;
        $idea_id = 0;
        try {
            $db = LispraCore::getPDODB();


            $stmt = $db->prepare(PDOBPSCreator::insertValues(self::IDEAS_TABLE, array(self::IDEA_TITLE, self::IDEA_DESC)));
            $stmt->execute(PDOBPSCreator::assocExtractParams($data, array(self::IDEA_TITLE, self::IDEA_DESC)));

            $idea_id = intval($db->lastInsertId());

            $stmt = null;
            $db = null;

            if ($idea_id > 0) {
                self::addTagsToIdea($idea_id, $data[self::IDEA_TAGS]);
                throw new Exception("last insert id is 0");
            }
        } catch (PDOException $e) {
            self::logError("createIdea", "PDOException => " . $e->getMessage());
        } catch (Exception $ex) {
            self::logError("createIdea", "Exception => " . $ex->getMessage());
        }
        $stmt = null;
        $db = null;
        return intval($idea_id);
    }

    public static function userGetIdeasIDs($user_id) {

        $stmt = null;
        $db = null;
        $out = array();

        try {
            $db = LispraCore::getPDODB();
            $stmt = $db->prepare("SELECT idea_id FROM lispra_ideas_user_ideas WHERE user_id = $user_id");
            $stmt->execute();

            $out = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
//            return $sql;
        } catch (Exception $exc) {
            self::logError("userGetIdeasIDs", $exc->getMessage());
        }

        $stmt = null;
        $db = null;
        return $out;
    }

    public static function userGetIdeas($user_id) {


        $out = array();

        try {
            $ids = self::userGetIdeasIDs($user_id);
            if (count($ids) > 0) {
                foreach ($ids as $idea_id) {
                    $out[] = self::getIdea($idea_id);
                }
            }
        } catch (Exception $exc) {
            self::logError("userGetIdeas", $exc->getMessage());
        }


        return $out;
    }

    public static function userCreateIdea($user_id, $data) {

        if ($user_id < 1) {
            return 0;
        }
        $stmt = null;
        $db = null;
        $idea_id = 0;
        try {

            $idea_id = self::createIdea($data);
            if (intval($idea_id) > 0) {
                $db = LispraCore::getPDODB();

                $stmt = $db->prepare(PDOBPSCreator::insertValues(self::IDEAS_USER_IDEAS_TABLE, array(self::USER_ID, self::IDEA_ID)));
                $stmt->execute(PDOBPSCreator::assoc2Params(array(self::USER_ID => $user_id, self::IDEA_ID => $idea_id)));

                self::log("userCreateIdea", "Idea $idea_id created for user $user_id");
            }
        } catch (Exception $e) {
            self::logError("userCreateIdea", "PDOException => " . $e->getMessage());
        } catch (Exception $ex) {
            self::logError("userCreateIdea", "Exception => " . $ex->getMessage());
        }
        $stmt = null;
        $db = null;
        return $idea_id;
    }

    public function test() {
        $db = LispraCore::getDB();
        if (!is_null($db)) {
            try {
                echo 'hey';

//                foreach ($db->query('SELECT * from lispra_ideas') as $row) {
//                    print_r($row);
//                }

                $db = null;
            } catch (PDOException $e) {
//                print "Error!: " . $e->getMessage() . "<br/>";
//                $db = null;
//                die();ç
                return false;
            }
        }
        return true;
    }

}
