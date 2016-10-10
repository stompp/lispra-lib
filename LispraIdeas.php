<?php

//namespace lispra;

require_once 'core/lispra-beta-core.php';

interface LispraIdeasKeys extends LispraKeys {

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

}

class LispraIdeas implements LispraIdeasKeys {

    public static function testPDOHelper() {
        $out = array();
        try {
            $db = LispraCore::getDB();
            $out = $db->selectWhereColumnInToAssoc(self::IDEAS_TAGS_TABLE, self::IDEA_ID, array(7, "8"));
            $db = null;
        } catch (Exception $ex) {
            
        }

        return $out;
    }

    public static function log($method, $msg) {
        LispraLog::classLog("LispraIdeas", $method, $msg);
    }

    public static function logError($method, $msg) {
        LispraLog::classError("LispraIdeas", $method, $msg);
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

    public static function isUserIdea($user_id, $idea_id) {

        $db = null;
        $stmt = null;
        $acces = false;

        try {
            $db = LispraCore::getPDODB();

            $stmt = $db->prepare("SELECT 1 FROM lispra_ideas_user_ideas WHERE idea_id=$idea_id AND user_id=$user_id LIMIT 1");
            $stmt->execute();
            $acces = $stmt->rowCount();
        } catch (PDOException $e) {
            self::logError("isUserIdea", "PDOException => " . $e->getMessage());
        } catch (Exception $ex) {
            self::logError("isUserIdea", "Exception => " . $ex->getMessage());
        }
        $stmt = null;
        $db = null;
        return $acces;
    }

    public static function userFilterIdeasByID($user_id, $ideas_ids = NULL) {
        $ids = self::userGetIdeasIDs($user_id);

        if (is_null($ideas_ids)) {
            return $ids;
        }

        $ideas_ids = ArrayUtils::toArray($ideas_ids, ",");
        if (count($ideas_ids) == 0) {
            return array();
        }

        $n = array();
        foreach ($ideas_ids as $id) {
            if (in_array($id, $ids)) {
                $n[] = $id;
            }
        }
        return $n;
    }

    public static function userCreate($user_id, $data) {




        $stmt = null;
        $db = null;
        $idea_id = 0;

        try {
            if (($user_id < 1) || (ArrayUtils::isKeyEmpty($data, LispraIdeasKeys::IDEA_TITLE))) {
                return $idea_id;
            }

            $idea_id = LispraIdeasIdea::createIdea($data);
           
            if ($idea_id > 0) {
                $db = LispraCore::getPDODB();

                $stmt = $db->prepare(PDOBPSCreator::insertValues(self::IDEAS_USER_IDEAS_TABLE, array(self::USER_ID, self::IDEA_ID)));
                $stmt->execute(PDOBPSCreator::assoc2Params(array(self::USER_ID => $user_id, self::IDEA_ID => $idea_id)));
                $stmt = null;
                $db = null;
//                self::log("userCreate", "Idea $idea_id created for user $user_id");
            }
        } catch (PDOException $e) {
            self::logError("userCreate", "PDOException => " . $e->getMessage());
        } catch (Exception $ex) {
            self::logError("userCreate", "Exception => " . $ex->getMessage());
        }
        $stmt = null;
        $db = null;
        return $idea_id;
    }

    public static function userUpdate($user_id, $idea_id, $data) {
        if (self::isUserIdea($user_id, $idea_id)) {
            return LispraIdeasIdea::updateIdea($idea_id, $data);
        }
        return 0;
    }

    public static function userGet($user_id, $ideas_ids = NULL) {


        $out = array();

        try {
            $ids = self::userFilterIdeasByID($user_id, $ideas_ids);

            if (count($ids) < 1) {
                throw new Exception("NO VALID ID");
            }

            foreach ($ids as $idea_id) {
                $i = LispraIdeasIdea::getIdea($idea_id);
                if (!is_null($i)) {
                    $out[] = $i;
                }
            }
        } catch (Exception $exc) {
            self::logError("userGet", $exc->getMessage());
        }


        return $out;
    }

    public static function userDelete($user_id, $ideas_ids = NULL) {
      
        if (isNullorEmpty($ideas_ids)) {
            return 0;
        }

        try {
            $ids = self::userFilterIdeasByID($user_id, $ideas_ids);
            if (count($ids) < 1) {
                throw new Exception("NO VALID ID");
            }
            return LispraIdeasIdea::delete($ids);
        } catch (Exception $exc) {
            self::logError("userDelete", $exc->getMessage());
        }


        return 0;
    }

 
  

  

    public static function test() {
//        return array("hola"=>"k ase","y tu " => "ej ke ase");
    }

    //    public static function userGetIdeas($user_id, $ideas_ids = NULL) {
//
//
//        $out = array();
//
//        try {
//            $ids = self::userFilterIdeasByIdeasIDs($user_id, $ideas_ids);
//            if (count($ids) < 1) {
//                throw new Exception("NO VALID ID");
//            }
//
//            foreach ($ids as $idea_id) {
//                $i = LispraIdeasIdea::getIdea($idea_id);
//                if (!is_null($i)) {
//                    $out[] = $i;
//                }
//            }
//        } catch (Exception $exc) {
//            self::logError("userGetIdeas", $exc->getMessage());
//        }
//
//
//        return $out;
//    }
//
//    
}

class LispraIdeasIdea implements LispraIdeasKeys {

    public static function log($method, $msg) {
        LispraLog::classLog(get_class(), $method, $msg);
    }

    public static function logError($method, $msg) {
        LispraLog::classError(get_class(), $method, $msg);
    }

    public static function getTags($idea_id) {

        $stmt = null;
        $db = null;
        $out = array();
        try {
            $db = LispraCore::getPDODB();
            $sql = "SELECT tag_title from lispra_ideas_tag_titles WHERE tag_id IN (SELECT tag_id from lispra_ideas_tags WHERE idea_id = $idea_id)";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $out = TagsUtils::parseToString($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
        } catch (Exception $exc) {
            self::logError("getTags", $exc->getMessage());
        }

        $stmt = null;
        $db = null;
        return $out;
    }

    public static function addTags($idea_id, $tags_str_or_array) {
      
        $stmt = null;
        $db = null;
        $rowCount = 0;

        $tags = TagsUtils::parseToArray($tags_str_or_array);
        if (count($tags) == 0) {
            self::logError("addTags", "tags size is 0");
            return $rowCount;
        }

        try {
            LispraIdeasTags::add($tags);

            $ids = LispraIdeasTags::getIDs($tags);
            if (count($ids) == 0) {
                self::logError("addTags", "ids size is 0");
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
            self::logError("addTags", "Exception => " . $ex->getMessage());
        } catch (PDOException $e) {
            self::logError("addTags", "PDOException => " . $e->getMessage());
        }


        $stmt = null;
        $db = null;
        return $rowCount;
    }

    public static function removeTags($idea_id, $tags_str_or_array) {
        $stmt = null;
        $db = null;
        $rowCount = 0;

        $tags = TagsUtils::parseToArray($tags_str_or_array);
        if (count($tags) == 0) {
            self::logError("removeTags", "tags size is 0");
            return $rowCount;
        }


        try {

            $ids = LispraIdeasTags::getIDs($tags);
            if (count($ids) == 0) {
                self::logError("removeTags", "ids size is 0");
                return $rowCount;
            }
            $s = implode(",", $ids);
            $db = LispraCore::getPDODB();



            $stmt = $db->prepare("DELETE FROM lispra_ideas_tags WHERE idea_id = $idea_id AND tag_id IN($s)");
            $stmt->execute();
            $rowCount = $stmt->rowCount();
            $stmt = null;
            $db = null;
        } catch (Exception $ex) {
            self::logError("removeTags", "Exception => " . $ex->getMessage());
        } catch (PDOException $e) {
            self::logError("removeTags", "PDOException => " . $e->getMessage());
        }


        $stmt = null;
        $db = null;
        return $rowCount;
    }

    public static function clearTags($idea_id) {

        $stmt = null;
        $db = null;
        $rowCount = 0;

        try {
            $db = LispraCore::getPDODB();
            $stmt = $db->prepare("DELETE FROM lispra_ideas_tags WHERE idea_id=$idea_id");
            $stmt->execute();
            $rowCount = $stmt->rowCount();
        } catch (Exception $ex) {
            self::logError("clearTags", "Exception => " . $ex->getMessage());
        } catch (PDOException $e) {
            self::logError("clearTags", "PDOException => " . $e->getMessage());
        }


        $stmt = null;
        $db = null;
        return $rowCount;
    }

    public static function setTags($idea_id, $tags_str_or_array) {
        self::clearTags($idea_id);
        return self::addTags($idea_id, $tags_str_or_array);
    }

    public static function getHeader($idea_id) {

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
            self::logError("getHeader", $exc->getMessage());
        }

        $stmt = null;
        $db = null;
        return $out;
    }

    public static function parseIDsToArray($ids) {
        $out = array();
        if (isNullorEmpty($ids)) {
            return $out;
        } elseif (is_string($ids)) {
            return explode(",", $ids);
        } elseif (is_integer($var)) {
            return array($ids);
        } elseif (is_array($ids)) {
            return $ids;
        }

        return $out;
    }

    public static function getByIDs($ids) {
        $out = array();
        $ids = self::parseIDsToArray($ids);
        foreach ($ids as $idea_id) {
            $i = self::getIdea($idea_id);
            if (isset($i)) {
                $out[] = $i;
            }
        }
        return $out;
    }

    public static function getIdea($idea_id) {
        $out = array();
        try {
            $out = self::getHeader($idea_id);
            if (!isNullorEmpty($out)) {
                $out[self::IDEA_TAGS] = self::getTags($idea_id);
            }
        } catch (Exception $exc) {
            self::logError("getIdea", $exc->getMessage());
        }

        return $out;
    }

    public static function createIdea($data) {

        $dafault_data = array(
            self::IDEA_TITLE => "",
            self::IDEA_DESC => "",
            self::IDEA_TAGS => "");
        $data = ArrayUtils::defaults($data, $dafault_data);

        if (ArrayUtils::isKeyEmpty($data, self::IDEA_TITLE)) {
            return 0;
        }

       

        $stmt = null;
        $db = null;
        $idea_id = 0;

        try {
            $db = LispraCore::getPDODB();


            $stmt = $db->prepare(PDOBPSCreator::insertValues(self::IDEAS_TABLE, array(self::IDEA_TITLE, self::IDEA_DESC)));
            $stmt->execute(PDOBPSCreator::assocExtractParams($data, array(self::IDEA_TITLE, self::IDEA_DESC)));

            $idea_id = $db->lastInsertId();

            $stmt = null;
            $db = null;


            if ($idea_id > 0) {
                self::addTags($idea_id, $data[self::IDEA_TAGS]);
            } else {
                throw new Exception("last insert id is 0");
            }
        } catch (PDOException $e) {
            LispraLog::classError("LispraIdeasIdea", "createIdea", "PDOException => " . $e->getMessage());
        } catch (Exception $ex) {
            LispraLog::classError("LispraIdeasIdea", "createIdea", "Exception => " . $ex->getMessage());
        }
        $stmt = null;
        $db = null;
        return $idea_id;
    }

    public static function updateIdea($idea_id, $data) {
        $stmt = null;
        $db = null;
        $rowCount = 0;


        try {

            $db = LispraCore::getPDODB();

            $sql = "UPDATE lispra_ideas SET idea_title=:idea_title,idea_desc=:idea_desc WHERE idea_id=:idea_id";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(":idea_title", $data[self::IDEA_TITLE], PDO::PARAM_STR);
            $stmt->bindParam(":idea_desc", $data[self::IDEA_DESC], PDO::PARAM_STR);
            $stmt->bindParam(":idea_id", $idea_id, PDO::PARAM_INT);
            $stmt->execute();
//            $rowCount = $stmt->rowCount();

            $stmt = null;
            $db = null;

            self::setTags($idea_id, $data[self::IDEA_TAGS]);

            return self::getIdea($idea_id);
        } catch (PDOException $e) {
            self::logError("updateIdea", "PDOException => " . $e->getMessage());
        } catch (Exception $ex) {
            self::logError("updateIdea", "Exception => " . $ex->getMessage());
        }
        $stmt = null;
        $db = null;
        return array();
    }

    public static function delete($idea_or_ideas) {

        $rowCount = 0;

        try {
            $ids = ArrayUtils::toArray($idea_or_ideas);
            if (count($ids) == 0) {
//                return $rowCount;
                throw new Exception("NO VALID IDS");
            }

            $db = LispraCore::getPDODB();
            $rowCount = $db->deleteWhereColumnIn(LispraIdeasKeys::IDEAS_TAGS_TABLE, LispraIdeasKeys::IDEA_ID, $ids);
            $rowCount = $db->deleteWhereColumnIn(LispraIdeasKeys::IDEAS_TABLE, LispraIdeasKeys::IDEA_ID, $ids);

            $db = null;
        } catch (PDOException $e) {
            self::logError("delete", "PDOException => " . $e->getMessage());
        } catch (Exception $ex) {
            self::logError("delete", "Exception => " . $ex->getMessage());
        }

        $db = null;
        return $rowCount;
    }

}

class LispraIdeasTags extends TagsUtils {

    public static function log($method, $msg) {
        LispraLog::classLog(get_class(), $method, $msg);
    }

    public static function logError($method, $msg) {
        LispraLog::classError(get_class(), $method, $msg);
    }

    public static function getIDs($tags_str_or_array) {

        $tags = self::parseToArray($tags_str_or_array);
        if (count($tags) < 1) {
            self::log("getIDs", "empty tags");
            return array();
        }


        $stmt = null;
        $db = null;
        $out = array();

        try {
            self::add($tags);
            $db = LispraCore::getPDODB();
            $s = sprintf_on_values_and_implode("'%s'", ",", $tags);
            $stmt = $db->prepare("SELECT tag_id FROM lispra_ideas_tag_titles WHERE tag_title IN ($s)");
            $stmt->execute();
            $out = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
            $stmt = null;
            $db = null;
        } catch (Exception $exc) {
            self::logError("getIDs", $exc->getMessage());
        }

        $stmt = null;
        $db = null;
        return $out;
    }

    public static function getAllTags($only_titles = false) {

        $stmt = null;
        $db = null;
        $out = array();

        try {

            $db = LispraCore::getPDODB();

            $stmt = $db->prepare("SELECT * FROM lispra_ideas_tag_titles");
            $stmt->execute();
            if ($only_titles) {
                $out = $stmt->fetchAll(PDO::FETCH_COLUMN, 1);
            } else {
                $out = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            $stmt = null;
            $db = null;
        } catch (Exception $exc) {
            self::logError("getAllTags", $exc->getMessage());
        }

        $stmt = null;
        $db = null;
        return $out;
    }

    public static function getTagsByIDs($ids, $only_titles = false) {
        $stmt = null;
        $db = null;
        $out = array();

        try {

            $db = LispraCore::getPDODB();
            $s = implode(",", $ids);
            if ($only_titles) {
                $stmt = $db->prepare("SELECT tag_title FROM lispra_ideas_tag_titles WHERE tag_id IN ($s)");
                $stmt->execute();
                $out = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
            } else {
                $stmt = $db->prepare("SELECT * FROM lispra_ideas_tag_titles WHERE tag_id IN ($s)");
                $stmt->execute();
                $out = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }



            $stmt = null;
            $db = null;
            return $out;
        } catch (Exception $exc) {
            self::logError("getTagsByIDs", $exc->getMessage());
        }

        $stmt = null;
        $db = null;
        return $out;
    }

    public static function parseIDsToString($ids) {
        return self::parseToString(self::getTagsByIDs($ids, true));
    }

    public static function filterNew($tags_str_or_array) {
        $tags = TagsUtils::parseToArray($tags_str_or_array);
        if (count($tags) < 1) {
            self::log("filterNew", "empty tags");
            return array();
        }

        $stmt = null;
        $db = null;
        $out = $tags;

        try {
            $db = LispraCore::getPDODB();
            $s = sprintf_on_values_and_implode("'%s'", ",", $tags);
            $stmt = $db->prepare("SELECT tag_title FROM lispra_ideas_tag_titles WHERE tag_title IN($s)");
            $stmt->execute();


            $existing = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
            $out = array_values(array_diff($tags, $existing));

            $existing = null;
            $stmt = null;
            $db = null;
            return $out;
        } catch (Exception $exc) {
            self::logError("filterNew", $exc->getMessage());
        } catch (PDOException $exc) {
            self::logError("filterNew", $exc->getMessage());
        }
        $stmt = null;
        $db = null;
        return $out;
    }

    public static function add($tags_str_or_array) {
        $tags = self::filterNew($tags_str_or_array);
        if (count($tags) < 1) {
            self::log("add", "empty tags");
            return 0;
        }

        $stmt = null;
        $db = null;
        $rowCount = 0;

        try {
            $db = LispraCore::getPDODB();

            $s = sprintf_on_values_and_implode("('%s')", ",", $tags);
            $stmt = $db->prepare("INSERT IGNORE INTO lispra_ideas_tag_titles (tag_title) VALUES $s ON DUPLICATE KEY UPDATE tag_title=tag_title");
            $stmt->execute();

            $rowCount = $stmt->rowCount();

            $stmt = null;
            $db = null;
            return $rowCount;
        } catch (Exception $exc) {
            self::logError("add", $exc->getMessage());
        } catch (PDOException $exc) {
            self::logError("add", $exc->getMessage());
        }
        $stmt = null;
        $db = null;
        return $rowCount;
    }

    public static function delete($tags_str_or_array) {
        $tags = TagsUtils::parseToArray($tags_str_or_array);
        if (count($tags) < 1) {
            self::log("delete", "empty tags");
            return 0;
        }

        $stmt = null;
        $db = null;
        $rowCount = 0;

        try {
            $db = LispraCore::getPDODB();

            $s = sprintf_on_values_and_implode("'%s'", ",", $tags);
            $stmt = $db->prepare("DELETE FROM lispra_ideas_tag_titles WHERE tag_title IN ($s)");
            $stmt->execute();

            $rowCount = $stmt->rowCount();

            $stmt = null;
            $db = null;
            return $rowCount;
        } catch (Exception $exc) {
            self::logError("delete", $exc->getMessage());
        } catch (PDOException $exc) {
            self::logError("delete", $exc->getMessage());
        }
        $stmt = null;
        $db = null;
        return $rowCount;
    }

}
