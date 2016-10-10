<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'lispra-beta-config.php';

require_once 'libs/utilities.php';
require_once 'libs/PDOBPSCreator.php';
//require_once 'lispra-beta-functions.php';


define('LISPRA_BETA_LOG_FILE', realpath(dirname(__FILE__)) . '/lispra_beta_log.txt');
require_once 'LispraLog.php';

function wp_get_current_lispra_user_id() {
    if (function_exists("wp_get_current_user") && function_exists("get_current_user_id")) {
        return get_current_user_id();
    }

    return 0;
}

interface LispraKeys {

    const USER_ID = 'user_id';
    const TIME_STAMP = 'time_stamp';
    const TAG_ID = 'tag_id';
    const TAG_TITLE = 'tag_title';

}

class PDOHelper extends PDO {


    public function tableToAssoc($table, $where = "") {

        $sql = "SELECT * FROM $table";
        if (strlen($where)) {
            $sql .= " WHERE $where";
        }

        $stmt = null;

        $out = array();
        try {

            $stmt = $this->prepare($sql);
            $stmt->execute();
            $out = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt = null;
            return $out;
        } catch (PDOException $e) {
            LispraLog::classError("PDOHelper", "tableToAssoc", "PDOException " . $e->getMessage()) . "  While trying to execute $sql";
        } catch (Exception $e) {
            LispraLog::classError("PDOHelper", "tableToAssoc", "Exception " . $e->getMessage()) . "  While trying to execute $sql";
        }
        $stmt = null;

        return $out;
    }

    public function selectWhereColumnInToAssoc($table, $column, $array_in) {

        $sql = sprintf("SELECT * FROM $table WHERE $column IN (%s)", implode(",", sqlize($array_in)));
        $stmt = null;
        $out = array();
        try {

            $stmt = $this->prepare($sql);
            $stmt->execute();
            $out = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt = null;
            return $out;
        } catch (PDOException $e) {
            LispraLog::classError("PDOHelper", "selectWhereColumnInToAssoc", "PDOException " . $e->getMessage()) . "  While trying to execute $sql";
        } catch (Exception $e) {
            LispraLog::classError("PDOHelper", "selectWhereColumnInToAssoc", "Exception " . $e->getMessage()) . "  While trying to execute $sql";
        }
        $stmt = null;

        return $out;
    }

    public function delete($table, $where) {

        if((strlen($table)== 0) || (strlen($where) == 0)){
            return 0;
        }
        
        $sql = sprintf("DELETE FROM %s WHERE %s", $table, $where);
        $stmt = null;

        $rowCount = 0;
        try {

            $stmt = $this->prepare($sql);
            $stmt->execute();
            $rowCount = $stmt->rowCount();
            $stmt = null;
            return $rowCount;
        } catch (PDOException $e) {
            LispraLog::classError("PDOHelper", "delete", "PDOException " . $e->getMessage()) . "  While trying to execute $sql";
        } catch (Exception $e) {
            LispraLog::classError("PDOHelper", "delete", "Exception " . $e->getMessage()) . "  While trying to execute $sql";
        }
        $stmt = null;

        return $rowCount;
    }

    public function deleteWhereColumnIn($table, $column, $array_in) {

       
        $where = sprintf("$column IN (%s)", implode(",", sqlize($array_in)));
        return $this->delete($table, $where);
        
    }

}

class LispraCore {

    public static function getPDODB() {
        $db = new PDOHelper(LISPRA_BETA_DSN, LISPRA_BETA_DATABASE_USER, LISPRA_BETA_DATABASE_PASS);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $db;
    }

    public static function getDB() {


        try {
//            $db = new PDO(self::getPDODSN(), LISPRA_BETA_DATABASE_USER, LISPRA_BETA_DATABASE_PASS);
//            return $db;
            return self::getPDODB();
        } catch (PDOException $e) {
            return null;
//        print "Error!: " . $e->getMessage() . "<br/>";
//        die();
        }

        return null;
    }

    public static function wpGetCurrentUserID() {
        if (function_exists("wp_get_current_user") && function_exists("get_current_user_id")) {
            return get_current_user_id();
        }

        return 0;
    }

    public static function getCurrentLispraUserID() {
        //por ahora
        return self::wpGetCurrentUserID();
    }

    public static function isUserSet() {
        return (self::getCurrentLispraUserID() > 0) ? true : false;
    }

}

?>