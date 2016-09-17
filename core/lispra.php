<?php

require_once 'libs/DBHelper.php';
require_once 'core.php';




class LispraWPDBHelper extends DBHelper {

    private function setWordPressDBCredentials() {
//        if ($this->dbName != WP_DATABASE_NAME) {
//            if ($this->connected)
//                $this->close();
//        }
        $this->setDBCredentials(WP_DATABASE_NAME, WP_DATABASE_USER, WP_DATABASE_PASS, WP_DATABASE_HOST);
    }

    function __construct() {
        parent::__construct();
        $this->setWordPressDBCredentials();
    }

    function __destruct() {
        parent::__destruct();
    }

    public function getWPUserData($user_id_or_email) {
        if (isNullorEmpty($user_id_or_email))
            return;

        $columns = unserialize(WP_LISPRA_GET_USER_DATA_VALID_KEYS);
        $where = isEmail($user_id_or_email) ?
                WP_KEY_USER_EMAIL . " = '$user_id_or_email'" : WP_KEY_ID . " = '$user_id_or_email'";

        $r = $this->select(WP_LISPRA_TABLE_USERS, $columns, $where);

        return $r[0];
//        if (!isNullorEmpty($r)) {
//            $this->fromAssoc($r[0]);
//        }
    }

}

class LispraDBHelper extends DBHelper {

    private function setLispraDBCredentials() {
//        if ($this->dbName != LISPRA_DATABASE_NAME) {
//            if ($this->connected)
//                $this->close();
//        }
        $this->setDBCredentials(LISPRA_DATABASE_NAME, LISPRA_DATABASE_USER, LISPRA_DATABASE_PASS, LISPRA_DATABASE_HOST);
    }

//    private function setWordPressDBCredentials() {
//        if ($this->dbName != WP_DATABASE_NAME) {
//            if ($this->connected)
//                $this->close();
//        }
//        $this->setDBCredentials(WP_DATABASE_NAME, WP_DATABASE_USER, WP_DATABASE_PASS, WP_DATABASE_HOST);
//    }

    function __construct() {
        parent::__construct();
        $this->setLispraDBCredentials();
    }

    function __destruct() {
        parent::__destruct();
    }

    public function startDataBase() {
        br("Starting db");
        $this->executeJSONActions(LISPRA_FILE_DB_ACTIONS_ON_CREATE_DB_JSON, "file");
    }

    private function isWPIDRegistered($ID) {

//        if(is_numeric($ID)){
//              $d = $this->getWPUserData($ID);
//        if(!is_null($d)) return false;
        return $this->findInColumn(LISPRA_TABLE_WP_USERS, WP_KEY_ID, $ID);
//        }
//        return false;
    }

    public function getWPUserData($user_id_or_email) {
        $s = new LispraWPDBHelper();
        $o = $s->getWPUserData($user_id_or_email);

        if (!isNullorEmpty($o)) {
            if (!$this->isWPIDRegistered($o[WP_KEY_ID])) {
                $this->createUserFromWPID($o[WP_KEY_ID]);
            }
        }
        $s->close();
        return $o;
    }

    /**
     * 
     * @param type $user_data Assoc array containing user data to save
     * @return \LispraUser
     */
    public function createUserFromWPID($ID) {
        br("createUserFromWPID START");
        if (!is_numeric($ID))
            return false;
        br("IS NUMERIC");
        if ($this->insertArray(LISPRA_TABLE_WP_USERS, array($ID))) {
            br("INSERT OK");
// Create user lists table
            $q = sprintf_assoc(
                    LISPRA_MYSQL_TEMPLATE_CREATE_USER_LISTS_TABLE, array(LISPRA_KEY_USER_ID => $ID)
            );
//             br("create user list table statment : $q");
            $r = $this->query($q);
            if ($r)
                return true;
        }
        br("INSERT ERROR");
        return false;
    }

}

/**
 * Description of LispraUser
 *
 * @author josem
 */
class LispraUser extends ClassConverter {

    public $ID = -1;
//    protected $user_id = -1;
    public $user_email = '';
    public $display_name = '';

    private function getDB() {
        return new LispraDBHelper();
    }

    public function getID() {
        return intval($this->getKey(WP_KEY_ID));
//        return intval($this->$ID);
    }

    public function getUserId() {
        return intval($this->getKey(WP_KEY_ID));
    }

    public function getUserEmail() {
        return $this->$user_email;
    }

    public function getUserDisplayName() {
        return $this->display_name;
    }

    public function isDataSet() {
        return ($this->getUserId() > 0) ? true : false;
    }

    public function getListsTableName() {
        return sprintf_assoc(LISPRA_TEMPLATE_USER_LISTS_TABLE_NAME, array(LISPRA_KEY_USER_ID => $this->getUserId()));
//        return str_replace('%user_id', $this->getUserId(), LISPRA_TEMPLATE_USER_LISTS_TABLE_NAME);
    }

//    public function getListTableName($list_id) {
//        return sprintf_assoc(
//                LISPRA_TEMPLATE_USER_LIST_TABLE_NAME, array(
//            LISPRA_KEY_USER_ID => $this->getUserId(),
//            LISPRA_KEY_LIST_ID => is_int($list_id) ? $list_id : intval($list_id)
//                )
//        );
//    }

    public function getListTableNameByListId($list_id) {
        return sprintf_assoc(
                LISPRA_TEMPLATE_USER_LIST_TABLE_NAME, array(
            LISPRA_KEY_USER_ID => $this->getUserId(),
            LISPRA_KEY_LIST_ID => is_int($list_id) ? $list_id : intval($list_id)
                )
        );
    }

    public function getLists() {
//           br("getLists START");
//        br("getLists user data : " . $this->toJSON());
        $db = new LispraDBHelper();
//         br("getLists db started ");
//         br("".$this->getKey(WP_KEY_ID));
        $q = sprintf_assoc(LISPRA_MYSQL_TEMPLATE_GET_USER_LISTS, array(LISPRA_KEY_USER_ID => $this->getID()));
//        br($q);
        $r = $db->queryResultAssocs($q);
        return $r;
    }

    /**
     * 
     * @param type $data
     * @return type
     */
    public function createList($data) {

// current timestamp
        $ts = sqlTimeStamp();
// db helper
        $db = new LispraDBHelper();
// copy of input data
        $d = $data;
        // unset id and table name by now
        if (array_key_exists(LISPRA_KEY_LIST_ID, $d))
            unset($d[LISPRA_KEY_LIST_ID]);
        if (array_key_exists(LISPRA_KEY_LIST_TABLE, $d))
            unset($d[LISPRA_KEY_LIST_TABLE]);
// check if name and class are defined
        if (isNullorEmpty($d[LISPRA_KEY_LIST_NAME]) || isNullorEmpty($d[LISPRA_KEY_LIST_CLASS]))
            return NULL;
//            br("OK NAME AND CLASS NOT EMPTY");
// check if class exists
        if (!in_array($d[LISPRA_KEY_LIST_CLASS], unserialize(LISPRA_LIST_CLASSES)))
            return NULL;
//         br("CLASS IN ARRAY");
// dafault data
        if (isNullorEmpty($d[LISPRA_KEY_CREATION_TS]))
            $d[LISPRA_KEY_CREATION_TS] = $ts;
        if (isNullorEmpty($d[LISPRA_KEY_META]))
            $d[LISPRA_KEY_META] = "";
        if (isNullorEmpty($d[LISPRA_KEY_STATUS]))
            $d[LISPRA_KEY_STATUS] = LISPRA_STATUS_ACTIVE;
        if (isNullorEmpty($d[LISPRA_KEY_STATUS_CHANGE_TS]))
            $d[LISPRA_KEY_STATUS_CHANGE_TS] = $d[LISPRA_KEY_CREATION_TS];
//        if(isNullorEmpty($d))

        if ($db->insertAssocArray($this->getListsTableName(), $d)) {
//            br("LIST INSERT OK");
            $list_id = $db->getLastInsertID();
            if ($list_id < 1)
                return NULL;
            $list_created = false;
//            br("list id $list_id");
            switch ($d[LISPRA_KEY_LIST_CLASS]) {
                case LISPRA_LIST_CLASS_TODO:
//                    br("IS TODO");
                    $q = sprintf_assoc(LISPRA_MYSQL_TEMPLATE_CREATE_USER_TODO_LIST_TABLE, array(LISPRA_KEY_USER_ID => $this->getUserId(), LISPRA_KEY_LIST_ID => $list_id));
//                    br($q);
                    $r = $db->query($q);
                    if ($r)
                        $list_created = true;
//                    var_dump(mysqli_fetch_assoc($r));


                    break;

                default:
                    break;
            }

            if ($list_created) {
                //update table name
                $u = $db->updateFromAssocArray($this->getListsTableName(), array(LISPRA_KEY_LIST_TABLE => $this->getListTableNameByListId($list_id)), LISPRA_KEY_LIST_ID . " = '$list_id'");
                return $this->getListHeader($list_id);
            }
        } else {
            br("LIST INSERT FAILED");
        }
        return null;
    }

    public function deleteList($list_id) {

        $db = new LispraDBHelper();
        $list_header = $this->getListHeader($list_id);
        if (array_key_exists(LISPRA_KEY_LIST_ID, $list_header)) {
            $db->delete($this->getListsTableName(), LISPRA_KEY_LIST_ID . " = $list_id");
            if (array_key_exists(LISPRA_KEY_LIST_TABLE, $list_header)) {
                $db->dropTable($list_header[LISPRA_KEY_LIST_TABLE]);
            }
            return true;
        }
        return false;
    }

    public function getListHeader($list_id) {
        if (isNullorEmpty($list_id))
            return NULL;
        if (!is_int($list_id)) {
            $list_id = intval($list_id);
        }

        if ($list_id < 1)
            return NULL;

        $db = new LispraDBHelper();
        return $db->selectRowWhere($this->getListsTableName(), LISPRA_KEY_LIST_ID . " = $list_id");
    }

    public function getListContent($list_id) {
        $list_header = $this->getListHeader($list_id);
        if (!isNullorEmpty($list_header[LISPRA_KEY_LIST_TABLE])) {
            $db = new LispraDBHelper();
            return $db->select($list_header[LISPRA_KEY_LIST_TABLE]);
        }
    }

    public function updateListStatus($list_header_data) {

        $ts = sqlTimeStamp();
        if (!$this->isDataSet())
            return false;

        // get list_id
        $list_id = getIfSet($list_header_data[LISPRA_KEY_LIST_ID], 0);
        $list_id = intval($list_id);

        // if valid list_id
        if ($list_id > 0) {

            $prev = $this->getListHeader($list_id);
            if (isNullorEmpty($prev))
                return false;

            $status = getIfSet($list_header_data[LISPRA_KEY_STATUS]);

            //valid status
            if (in_array($status, unserialize(LISPRA_LIST_STATUS_TYPES))) {
                // check if new status is erase
                if ($status == LISPRA_STATUS_ERASED) {
                    return $this->deleteList($list_id);
                }
                // check if new status is actually new


                if ($status != $prev[LISPRA_KEY_STATUS]) {

                    // get status change ts if set
                    $status_ts = getIfSet($list_header_data[LISPRA_KEY_STATUS_CHANGE_TS], $ts);
                    if ($status_ts == $prev[LISPRA_KEY_STATUS_CHANGE_TS])
                        $status_ts = $ts;

                    $db = new LispraDBHelper();
                    return $db->updateFromAssocArray(
                                    $this->getListsTableName(), array(
                                LISPRA_KEY_STATUS => $status,
                                LISPRA_KEY_STATUS_CHANGE_TS => $status_ts), LISPRA_KEY_LIST_ID . " = $list_id"
                    );
                }
            }
        }

        return false;
    }

    public function updateListHeader($list_header_data) {

        $ts = sqlTimeStamp();
        $d = array();
        $list_id = 0;

        if (array_key_exists(LISPRA_KEY_LIST_ID, $list_header_data)) {
            $list_id = $list_header_data[LISPRA_KEY_LIST_ID];
        } else
            return false;

        $prev = $this->getListHeader($list_id);
        if (!$prev)
            return false;


        if (array_key_exists(LISPRA_KEY_LIST_NAME, $list_header_data)) {
            $d[LISPRA_KEY_LIST_NAME] = (isNullorEmpty($list_header_data[LISPRA_KEY_LIST_NAME])) ?
                    "List $list_id" : $list_header_data[LISPRA_KEY_LIST_NAME];
        }
        if (array_key_exists(LISPRA_KEY_META, $list_header_data)) {
            $d[LISPRA_KEY_META] = $list_header_data[LISPRA_KEY_META];
        }


        $status = getIfSet($list_header_data[LISPRA_KEY_STATUS]);
        if (in_array($status, unserialize(LISPRA_LIST_STATUS_TYPES))) {
            // check if new status is erase
            if ($status == LISPRA_STATUS_ERASED) {
                return $this->deleteList($list_id);
            }
            // check if new status is actually new
            if ($status != $prev[LISPRA_KEY_STATUS]) {
                // get status change ts if set
                $status_ts = getIfSet($list_header_data[LISPRA_KEY_STATUS_CHANGE_TS], $ts);
                $d[LISPRA_KEY_STATUS] = $status;
                $d[LISPRA_KEY_STATUS_CHANGE_TS] = ($status_ts == $prev[LISPRA_KEY_STATUS_CHANGE_TS]) ? $ts : $status_ts;
            }
        }

        $updated = false;
        if (count($d)) {
            $db = new LispraDBHelper();
            $r = $db->updateFromAssocArray(
                    $this->getListsTableName(), $d, LISPRA_KEY_LIST_ID . " = $list_id");
            if ($r)
                $updated = true;
        }

//        if (array_key_exists(LISPRA_KEY_STATUS, $list_header_data)) {
//            $r2 = $this->updateListStatus($list_header_data);
//            if ($r2)
//                $updated = true;
//        }

        return $updated;
    }

    public function createListItem($list_item_data) {

        $ts = sqlTimeStamp();
        $d = array();
        $list_id = 0;

        if (array_key_exists(LISPRA_KEY_LIST_ID, $list_item_data)) {
            $list_id = $list_item_data[LISPRA_KEY_LIST_ID];
        } else
            return false;

        $list_header = $this->getListHeader($list_id);

        if (!$list_header)
            return false;

        if (array_key_exists(LISPRA_KEY_TITLE, $list_item_data)) {
            if (isNullorEmpty($list_item_data[LISPRA_KEY_TITLE]))
                return false;
            $d[LISPRA_KEY_TITLE] = $list_item_data[LISPRA_KEY_TITLE];
        }
        if (array_key_exists(LISPRA_KEY_META, $list_item_data)) {
            $d[LISPRA_KEY_META] = $list_item_data[LISPRA_KEY_META];
        }


        $status = getIfSet($list_item_data[LISPRA_KEY_STATUS], LISPRA_STATUS_PENDING);

        if (in_array($status, unserialize(LISPRA_LIST_ITEM_STATUS_TYPES))) {
            // check if new status is erase
//            if ($status == LISPRA_STATUS_ERASED) {
//                return $this->deleteList($list_id);
//            }
            // check if new status is actually new
//            if ($status != $prev[LISPRA_KEY_STATUS]) {
            // get status change ts if set     
        } else {
            $status = LISPRA_STATUS_PENDING;
        }

        $d[LISPRA_KEY_STATUS] = $status;

        $creation_ts = getIfSet($list_item_data[LISPRA_KEY_CREATION_TS], $ts);
        if (strtotime($creation_ts) == 0)
            $creation_ts = $ts;
        $d[LISPRA_KEY_CREATION_TS] = $creation_ts;

        $status_ts = getIfSet($list_item_data[LISPRA_KEY_STATUS_CHANGE_TS], $ts);
        if (strtotime($status_ts) == 0)
            $status_ts = $ts;
        $d[LISPRA_KEY_STATUS_CHANGE_TS] = $status_ts;


        $tableName = $list_header[LISPRA_KEY_LIST_TABLE];

        $db = new LispraDBHelper();
        if ($db->tableExists($tableName)) {
            $r = $db->insertAssocArray($tableName, $d);
            if ($r)
                return true;
        }
        return false;
    }

    public function updateListItem($list_item_data) {

        $ts = sqlTimeStamp();
        $d = array();
        $db = $this->getDB();

        $list_id = intval(getIfSet($list_item_data[LISPRA_KEY_LIST_ID], -1));
        $list_item_id = intval(getIfSet($list_item_data[LISPRA_KEY_ID], -1));
        br("list : $list_id item : $list_item_id");
        if (($list_id < 1) || ($list_item_id < 1)) {
            return null;
        }
        br("Ok list and item id");

        // check list header
        $list_header = $this->getListHeader($list_id);
        if (!$list_header)
            return false;

        // check if table exists   
        $tableName = $list_header[LISPRA_KEY_LIST_TABLE];
        if (!$db->tableExists($tableName))
            return false;
        // check if irem exists exists   
        $prev_list_item = $this->getListItem($list_item_data);
        if (!$prev_list_item)
            return false;

        //Keys


        if (array_key_exists(LISPRA_KEY_TITLE, $list_item_data)) {
            if (!isNullorEmpty($list_item_data[LISPRA_KEY_TITLE]))
                $d[LISPRA_KEY_TITLE] = $list_item_data[LISPRA_KEY_TITLE];
        }

        if (array_key_exists(LISPRA_KEY_META, $list_item_data)) {
            $d[LISPRA_KEY_META] = $list_item_data[LISPRA_KEY_META];
        }


        $prev_status = getIfSet($prev_list_item[LISPRA_KEY_STATUS]);
        $status = getIfSet($list_item_data[LISPRA_KEY_STATUS]);

        if (in_array($status, unserialize(LISPRA_LIST_ITEM_STATUS_TYPES))) {
            // check if new status is erase
            if ($status == LISPRA_STATUS_ERASED) {
                return $db->delete($tableName, LISPRA_KEY_ID . " = $list_item_id");
            }

            // check if new status is new actually 
            if ($status != $prev_status) {
                // get status change ts if set     
                $d[LISPRA_KEY_STATUS] = $status;
                $status_ts = getIfSet($list_item_data[LISPRA_KEY_STATUS_CHANGE_TS], $ts);
                if (strtotime($status_ts) == 0)
                    $status_ts = $ts;
                $d[LISPRA_KEY_STATUS_CHANGE_TS] = $status_ts;
            }
        }

        $tableName = $list_header[LISPRA_KEY_LIST_TABLE];
        br("tableName : $tableName");
        br("d : " . json_encode($d));
        if ($db->tableExists($tableName)) {
            br("Table exists");
            $r = $db->updateFromAssocArray($tableName, $d, LISPRA_KEY_ID . " = $list_item_id");
            if ($r)
                return true;
        }
        return false;
    }

    public function getListItem($list_item_data) {

        $list_id = intval(getIfSet($list_item_data[LISPRA_KEY_LIST_ID], -1));
        $list_item_id = intval(getIfSet($list_item_data[LISPRA_KEY_ID], -1));
//        br("list : $list_id item : $list_item_id");
        if (($list_id < 1) || ($list_item_id < 1)) {
            return null;
        }

        $tableName = $this->getListTableNameByListId($list_id);
//        br("tableName : $tableName");
        if (!isNullorEmpty($tableName)) {
//            $db = $this->getDB();  
//             br("Got db");
            return $this->getDB()->selectRowWhereKeyEquals(
                            $tableName, LISPRA_KEY_ID, $list_item_id);
        } else {
//              br("NO db");
        }

        return null;
    }

    /**
     * 
     * @param type $user_id_or_email
     */
    private function fillFromDBBy($user_id_or_email) {
//        br("fillFromDBBy $user_id_or_email");
        $db = new LispraDBHelper();

        $r = $db->getWPUserData($user_id_or_email);

        if (!isNullorEmpty($r)) {
//             br("fillFromDBBy got : ".  json_encode($r));
            $this->fromAssoc($r);
        }
    }

    public function reload() {
        if ($this->isDataSet()) {
            $this->fillFromDBBy($this->getUserId());
        }
    }

    function __construct($user_id_or_email = null) {
//br("lispraUser construct start");
        if (!isNullorEmpty($user_id_or_email)) {
            $this->fillFromDBBy($user_id_or_email);
        }
//        br($this->toJSON());
//        br("lispraUser construct end");
    }

    function __destruct() {
        
    }

    public function test($a = NULL) {
        
    }

    public function executeAction($action) {
        if (!is_array($action)) {
            return NULL;
        }
        if (isNullorEmpty($action)) {
            return NULL;
        }
        if (isNullorEmpty($action["name"])) {
            return NULL;
        }
        $data = array_key_exists("data", $action) ? $action["data"] : array();
        switch ($action["name"]) {
            case "createList":
                return $this->createList($data);
                break;
            case "deleteList":
                return $this->deleteList($data);
                break;
            case "getLists":
                return $this->getLists();
                break;
            case "createListItem":
                return $this->createListItem($data);
                break;
            case "getListContent":
                return $this->getListContent(intval($data));
                break;
            case "updateListItem":
                return $this->updateListItem($data);
                break;
            case "updateListHeader":
                return $this->updateListHeader($data);
                break;




            default:
                break;
        }

        return NULL;
    }

}

class LispraList extends ClassConverter {

    protected $list_id = -1;
    protected $list_table = -1;
    protected $list_name = "";
    protected $list_class = "";
    protected $creation_ts = "";
    protected $meta = "";
    protected $status = "";
    protected $status_change_ts = "";
    protected $list_content = NULL;

    function __construct($r = NULL) {
        if (!is_null($r)) {
            $this->fromAssoc($r);
        }
    }

}

class LispraUserList extends LispraList {

    protected $user_id;

    function __construct($r = NULL) {
        parent::__construct();
    }

}

?>