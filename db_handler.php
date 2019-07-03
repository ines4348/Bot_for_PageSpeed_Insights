<?php

    require_once("/vendor/thingengineer/mysqli-database-class/MysqliDb.php"); 

    const DB_HOST = "us-cdbr-iron-east-02.cleardb.net";
    const DB_USERNAME = "b60754546ea096";
    const DB_PASSWORD = "36da8d02";
    const DB_NAME = "heroku_7fe864cef8db15a";

    class dbColumnName {
        const CHAT_ID = "chat_id";
        const USERNAME = "username";
        const CREATE_DATA = "create_data";
        const USER_LAST_CONNECTION_DATA = "user_last_connect_data";
        const USER_URL = "user_url";
    }

    global $db;
    $db = mysql_connect(DB_HOST, DB_USERNAME, DB_PASSWORD) or die();

    mysql_select_db(DB_NAME, $db) or die();

    mysql_query("SET NAMES utf8", $db);
    mysql_query("SET CHARACTER SET utf8", $db );
    mysql_query("SET COLLATION_CONNECTION="utf8_general_ci"", $db ); 
    setlocale(LC_ALL,"ru_RU.UTF8");




function createNewUser()
{
    $data = Array(CHAT_ID  =>  " admin ", USERNAME => " John ", CREATE_DATA => '01.01.2019'); 
    $id = $db -> insert('user', $ data);
}
?>















