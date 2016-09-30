<?php

include_once 'core/WPLispra.php';

class LispraRESTApi {

    public $user;

    public function __construct() {
        $this->user = $this->get_current_lispra_user();
    }

    protected function get_current_lispra_user() {
        if (function_exists("wp_get_current_user")) {
            $current_user = wp_get_current_user();
            $current_user_id = $current_user->ID;
            $lispra_user_id = intval($current_user_id);

            if ($lispra_user_id > 0) {

                $u = new LispraUser($lispra_user_id);
                if ($u->isDataSet()) {
                    return $u;
                }
            }
        }

        return null;
    }

    public function test() {
        $action = array(
            "name" => "getLists",
            "data" => "null"
        );

        $this->executeAction($action);
//        if($this->isUserSet()){
//            echo "LISPRA REST API TEST ak aka kaka";
//        }else {
//            echo "ATRAASSSSS";
//        }
    }

    public function isUserSet() {
        if ($this->user === null) {
            return false;
        } else if (!$this->user->isDataSet()) {
            return false;
        }
        return true;
    }

    public function getUser() {
        return $this->user;
    }

    public function test_scr($name) {

//        $scr = realpath(dirname(__FILE__))."/tests/$name.php";
        $scr = realpath(dirname(__FILE__)) . "/test_scr.php";
        $out = "";
        ob_start();
        if (file_exists($scr)) {
            include_once $scr;
        } else {
            echo "TEST SCRIPT NOT FOUND";
        }
        $out = ob_get_clean();

        return $out;
    }

    function executeActions1($a) {
        $u = get_current_lispra_user();
        if (is_null($u)) {
            echo_error("User is null");
            return;
        }
        if (array_key_exists("user_actions", $a)) {
            foreach ($a["user_actions"] as $action) {
                $r = $u->executeAction($action);

                $response = array(
                    "isDataSet" => 1,
                    "data" => $r
                );
                echo json_encode($response);
            }
        }
    }

    public function actions() {
//        return array("isUserSet" => ($this->isUserSet()) ? "yes" : "no");
//        
        if (!$this->isUserSet()) {

            return array("LispraRestApiMethod" => "actions", "Error" => "User not set");
        }

        $u = $this->getUser();
        $c = getRequestBody();
        if (isNullorEmpty($c)) {
            return array("LispraRestApiMethod" => "actions", "Error" => "Content null or empty");
        }
        // parse content to assoc
        $a = json_decode($c, true);
        $e = json_last_error();
        if ($e != JSON_ERROR_NONE) {
            return array("LispraRestApiMethod" => "actions", "Error" => "JSON ERROR :" . json_last_error_msg());
        }

        $responses = array();

        if (array_key_exists("user_actions", $a)) {
            foreach ($a["user_actions"] as $action) {
                $r = $u->executeAction($action);
                $response = array(
                    "isDataSet" => 1,
                    "data" => $r
                );
                $responses[] = $response;
            }
        }

        if (count($responses)) {
            return $responses[0];
        }

        return array("LispraRestApiMethod" => "actions", "Error" => "JSON ERROR :" . json_last_error_msg());
    }

//    public function actions() {
//        return array("isUserSet" => ($this->isUserSet()) ? "yes" : "no");
//        
//        $scr = realpath(dirname(__FILE__)) . "/actions.php";
//        if (!file_exists($scr)) {
//            return array("LispraRestApiMethod" => "actions", "Error" => "Script not found");
//        }
//        $out = "";
//        ob_start();
//        include_once $scr;
//        $out = ob_get_clean();
//
//        if (isJson($out)) {
//            return json_decode($out, true);
//        }
//        return array("LispraRestApiMethod" => "actions", "Error" => "Output not json");
//    }
//}
}

global $lispraREST;
$lispraREST = new LispraRESTApi();
?>