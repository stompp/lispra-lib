<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define('LISPRA_LOG_FILE', realpath(dirname(__FILE__)) . '/lispra_beta_log.txt');

require_once 'lispra-beta-config.php';
require_once 'libs/utilities.php';
require_once 'LispraLog.php';

function wp_get_current_lispra_user_id() {
    if (function_exists("wp_get_current_user") && function_exists("get_current_user_id")) {
        return get_current_user_id();
    }

    return 0;
}

class PDOBPSCreator {

//INSERT INTO subs
//  (subs_name, subs_email, subs_birthday)
//VALUES
//  (?, ?, ?)
//ON DUPLICATE KEY UPDATE
//  subs_name     = VALUES(subs_name),
//  subs_birthday = VALUES(subs_birthday)
    public static function insertValues($table, $columns) {

        $params = array();
        foreach ($columns as $c) {
            $params[] = ":" . $c;
        }
        return sprintf("INSERT IGNORE INTO %s (%s) VALUES (%s)", $table, implode(",", $columns), implode(",", $params));
//        return sprintf("INSERT INTO %s (%s) VALUES (%s)", $table, implode(",", $columns), implode(",", $params));
    }

    public static function selectRowWhereColumnsEquals($table, $columns) {

        $params = array();
        foreach ($columns as $c) {
            $params[] = "$c=:$c";
        }

        return sprintf("SELECT * FROM %s WHERE %s", $table, implode(" AND ", $params));
    }

    public static function toBindedParameterKey($key) {
        try {
            return ($key[0] == ':') ? $key : (":$key");
        } catch (Exception $exc) {
            return "";
        }

        return "";
    }

    public static function assocExtractParams($array, $keys) {
        $o = array();
        foreach ($keys as $key) {
            if (array_key_exists($key, $array)) {
                $o[":$key"] = $array[$key];
            }
        }



        return $o;
    }

    public static function assoc2Params($array, $excluded = null) {
        $o = array();
        $a = array();
        if (!is_null($excluded) && is_array($excluded)) {

            foreach ($excluded as $e) {
                if (array_key_exists($e, $array)) {
                    unset($array[$e]);
                }
            }
        }
        foreach ($array as $key => $value) {

            $o[":" . $key] = $value;
        }

        return $o;
    }

}

interface LispraKeys {

    const USER_ID = 'user_id';
    const TIME_STAMP = 'time_stamp';
    const TAG_ID = 'tag_id';
    const TAG_TITLE = 'tag_title';

}


class LispraCore {

    protected static function getPDODSN() {
        return sprintf('mysql:host=%s;dbname=%s;charset=%s', LISPRA_BETA_DATABASE_HOST, LISPRA_BETA_DATABASE_NAME, LISPRA_BETA_DATABASE_CHARSET);
    }

    public static function getPDODB() {
        $db = new PDO(self::getPDODSN(), LISPRA_BETA_DATABASE_USER, LISPRA_BETA_DATABASE_PASS);
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