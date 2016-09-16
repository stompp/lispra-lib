<?php


require_once 'config.php';




// keys
define('LISPRA_KEY_ID','_id');
define('LISPRA_KEY_USER_ID','user_id');
define('LISPRA_KEY_EMAIL','email');
define('LISPRA_KEY_DISPLAY_NAME','display_name');
define('LISPRA_KEY_PASSWORD','password');
define('LISPRA_KEY_STATUS','status');
define('LISPRA_KEY_META','meta');
define('LISPRA_KEY_LIST_ID','list_id');
define('LISPRA_KEY_LIST_NAME','list_name');
define('LISPRA_KEY_LIST_TABLE','list_table');
define('LISPRA_KEY_LIST_CLASS','list_class');
define('LISPRA_KEY_CREATION_TS','creation_ts');
define('LISPRA_KEY_TODO','todo');
define('LISPRA_KEY_TITLE','title');
define('LISPRA_KEY_STATUS_CHANGE_TS','status_change_ts');
define('LISPRA_KEY_LAST_STATUS_CHANGE_TS','last_status_change_ts');


// status keys
define('LISPRA_STATUS_OPEN',"open");
define('LISPRA_STATUS_CLOSED',"closed");
define('LISPRA_STATUS_ACTIVE',"active");
define('LISPRA_STATUS_CANCELED',"canceled");
define('LISPRA_STATUS_COMPLETE',"complete");
define('LISPRA_STATUS_PENDING',"pending");
define('LISPRA_STATUS_DELAYED',"delayed");
define('LISPRA_STATUS_RESPONSE_PENDING',"responsePending");
define('LISPRA_STATUS_CREATED',"created");
define('LISPRA_STATUS_TRASH',"trash");
define('LISPRA_STATUS_ERASED',"erased");

define('LISPRA_LIST_ITEM_STATUS_TYPES',  serialize(array(LISPRA_STATUS_PENDING,LISPRA_STATUS_DELAYED,LISPRA_STATUS_COMPLETE,LISPRA_STATUS_CANCELED,LISPRA_STATUS_RESPONSE_PENDING,LISPRA_STATUS_ERASED,LISPRA_STATUS_TRASH)));

//list classes
define('LISPRA_LIST_CLASS_TODO',"todo");
define('LISPRA_LIST_CLASS_TOGO',"togo");
//list status types
define('LISPRA_LIST_CLASSES',  serialize(array(LISPRA_LIST_CLASS_TODO,LISPRA_LIST_CLASS_TOGO)));
define('LISPRA_LIST_STATUS_TYPES',  serialize(array(LISPRA_STATUS_ACTIVE,LISPRA_STATUS_CLOSED,LISPRA_STATUS_COMPLETE,LISPRA_STATUS_ERASED,LISPRA_STATUS_TRASH)));



//list task status types
//list task classes


//TABLES
define('LISPRA_TABLE_USERS',"lispra_users");
define('LISPRA_TABLE_WP_USERS',"lispra_wp_users");

// TABLE NAME TEMPLATES 
define('LISPRA_TEMPLATE_USER_LISTS_TABLE_NAME',"u%user_id_lists");

define('LISPRA_USER_LISTS_KEYS',  serialize(array(LISPRA_KEY_LIST_NAME,LISPRA_KEY_LIST_CLASS,LISPRA_KEY_CREATION_TS,LISPRA_KEY_META,LISPRA_KEY_STATUS,LISPRA_KEY_STATUS_CHANGE_TS)));

define('LISPRA_TEMPLATE_USER_LIST_TABLE_NAME',"u%user_id_l%list_id");


// MYSQL STATEMENTS 

// MYSQL START DB STATEMENTS

// CREATE USER LISTS
define('LISPRA_MYSQL_CREATE_USERS_TABLE',"CREATE TABLE IF NOT EXISTS lispra_users(
  user_id INT NOT NULL AUTO_INCREMENT,
  email VARCHAR(80) NOT NULL,
  password CHAR(50) NOT NULL,
  display_name VARCHAR(50) NOT NULL,
  PRIMARY KEY (user_id),
  UNIQUE INDEX (email)
);");


define('LISPRA_MYSQL_TEMPLATE_INSERT_USER_IN_USERS_TABLE', "INSERT INTO lispra_users (email,password,display_name) OUTPUT INSERTED.user_id VALUES ('%email','%password','%display_name');");
//$sql = "INSERT INTO lispra_users (email) OUTPUT INSERTED.product_id VALUES (?)";


// MYSQL STATEMENT TEMPLATES
define('LISPRA_MYSQL_TEMPLATE_CREATE_USER_LISTS_TABLE',"CREATE TABLE IF NOT EXISTS u%user_id_lists(
  list_id INT NOT NULL AUTO_INCREMENT,
  list_table VARCHAR(80) NOT NULL,
  list_name VARCHAR(80) NOT NULL,
  list_class VARCHAR(40) NOT NULL,
  creation_ts TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  meta TEXT NOT NULL,
  status VARCHAR(40) NOT NULL,
  status_change_ts TIMESTAMP NOT NULL,
  PRIMARY KEY (list_id),
  UNIQUE INDEX (list_table)
  );");




define('LISPRA_MYSQL_TEMPLATE_GET_USER_LISTS',"SELECT * FROM u%user_id_lists;");
define('LISPRA_MYSQL_TEMPLATE_GET_USER_LIST_DATA',"SELECT * FROM u%user_id_l%list_id;");

define('LISPRA_MYSQL_TEMPLATE_CREATE_USER_TODO_LIST_TABLE',"CREATE TABLE  u%user_id_l%list_id(
    _id INT NOT NULL AUTO_INCREMENT,
    title TEXT NOT NULL,
    creation_ts TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(40) NOT NULL,
    status_change_ts TIMESTAMP NOT NULL,
    meta TEXT NOT NULL,
    PRIMARY KEY (_id)
);");



// FILES
define('LISPRA_FILE_DB_ACTIONS_ON_CREATE_DB_JSON',"./core/json/db_actions_on_create_db.json");




?>