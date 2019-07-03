<?php

    const DB_HOST = "us-cdbr-iron-east-02.cleardb.net";
    const DB_USERNAME = "b60754546ea096";
    const DB_PASSWORD = "36da8d02";
    const DB_NAME = "heroku_7fe864cef8db15a";
    const SQL_INSERT = "insert into {table_name} {column_name} values({values})";

    class dbColumnName {
        const CHAT_ID = 'chat_id';
        const USERNAME = 'username';
        const CREATE_DATA = 'create_data';
        const USER_LAST_CONNECTION_DATA = 'user_last_connect_data';
        const USER_URL = 'user_url';
    }

    class dbTableName {
        const USER = 'user';
        const USER_LAST_CONNECT = 'user_last_connect';
        const USER_URL = 'user_url';
    }

    global $db;
    $db = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD) or die();

    mysqli_select_db($db, DB_NAME) or die();

    mysqli_query($db, "SET NAMES utf8");
    mysqli_query($db, "SET CHARACTER SET utf8");
    mysqli_query($db, "SET COLLATION_CONNECTION='utf8_general_ci'"); 
    setlocale(LC_ALL,"ru_RU.UTF8");

    function create_user($chat_id, $name)
    {
        global $db;
        $name = mysqli_real_escape_string($name);
        $chat_id = mysqli_real_escape_string($chat_id);
        $query_replase_table = str_replace("{table_name}", dbTableName::USER, SQL_INSERT);
        $query_replase_column = str_replace("{column_name}", dbColumnName::CHAT_ID . ', ' . dbColumnName::USERNAME . ', ' . dbColumnName::CREATE_DATA, $query_replase_table);
        $query = str_replace("{values}", "'" . $chat_id . "', '" . $name . "', '" . date("m.d.y") . "'", $query_replase_column);
        mysqli_query($db, $query) or die();
    }
?>















