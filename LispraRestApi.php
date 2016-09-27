<?php

include_once 'core/WPLispra.php';

class LispraRESTApi {

    protected $user;

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
 
        $scr = realpath(dirname(__FILE__))."/tests/$name.php";
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



}

global $lispraREST;
$lispraREST = new LispraRESTApi();
?>