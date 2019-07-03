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

    $db = new MysqliDb(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
    $db -> autoReconnect = false;

function createNewUser()
{
    $data = Array(CHAT_ID  =>  " admin ", USERNAME => " John ", CREATE_DATA => '01.01.2019'); 
    $id = $db -> insert('user', $ data);
}
?>















