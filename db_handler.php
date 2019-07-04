<?php

    const DB_HOST = "us-cdbr-iron-east-02.cleardb.net";
    const DB_USERNAME = "b60754546ea096";
    const DB_PASSWORD = "36da8d02";
    const DB_NAME = "heroku_7fe864cef8db15a";
    const SQL_INSERT = "insert into {table_name} ('{column_name}') values('{values}')";
    const DATE_FORMAT = "y.m.d";
    const SEPARATOR = "', '";

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
    
    function create_db_connect()
    {
        $db = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD) or die();
        mysqli_select_db($db, DB_NAME) or die();
        mysqli_query($db, "SET NAMES utf8");
        mysqli_query($db, "SET CHARACTER SET utf8");
        mysqli_query($db, "SET COLLATION_CONNECTION='utf8_general_ci'"); 
        setlocale(LC_ALL,"ru_RU.UTF8");
        return $db;
    }

    function is_user_set($chat_id)
    {
        $db = create_db_connect();
        $name = mysqli_real_escape_string($db, $chat_id);
        $result = mysqli_query($db, "select * from user where user.chat_id = " . $chat_id . ";");
        if($result->num_rows == 1) 
        {
            mysqli_close($db);
            return true;
        }
        else{
            mysqli_close($db);
            return false;
        }
    }

    function create_user($chat_id, $name)
    {
        $db = create_db_connect();
        $name = mysqli_real_escape_string($db, $name);
        $chat_id = mysqli_real_escape_string($db, $chat_id);
        $query_replase_table = str_replace("{table_name}", dbTableName::USER, SQL_INSERT);
        $query_replase_column = str_replace("{column_name}", dbColumnName::CHAT_ID . SEPARATOR . dbColumnName::USERNAME . SEPARATOR . dbColumnName::CREATE_DATA, $query_replase_table);
        $query = str_replace("{values}", $chat_id . SEPARATOR . $name . SEPARATOR . date(DATE_FORMAT), $query_replase_column);
        return $query;
        mysqli_query($db, $query) or die();
        mysqli_close($db);
        return "Пользователь добавлен";
    }

    
?>















