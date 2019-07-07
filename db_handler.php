<?php
    require_once("config.php");

    const DATE_FORMAT = "y.m.d";
    
    class dbColumnName {
        const USER_ID = 'user_id';
        const CHAT_ID = 'chat_id';
        const USERNAME = 'username';
        const CREATE_DATE = 'create_date';
        const USER_LAST_CONNECTION_DATE = 'user_last_connect_date';
        const USER_URL = 'user_url';
    }

    class dbTableName {
        const USER = 'user';
        const USER_LAST_CONNECT = 'user_last_connect';
        const USER_URL = 'user_url';
    }

    global $db;
    $db = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if ($db->connect_errno) 
    {
        exit();
    }

    $db->query("SET NAMES utf8");
    $db->query("SET CHARACTER SET utf8");
    $db->query("SET COLLATION_CONNECTION='utf8_general_ci'"); 
    setlocale(LC_ALL,"ru_RU.UTF8");
    

    function is_user_set($chat_id)
    {
        $chat_id = $db->real_escape_string($chat_id);
        $result = $db->query("SELECT * FROM  user as u WHERE u.chat_id = $chat_id;");
        
        if($result->num_rows == 1) 
        {
            return true;
        }
        
        return false;
    }

    function is_url_set($user_id, $url)
    {
        $result = $db->query("SELECT * FROM  user_url as uu WHERE uu.user_id = $user_id AND uu.user_url = '$url';");
        
        if($result->num_rows == 1) 
        {
            return true;
        }
        
        return false;
    }

    function get_user_id($chat_id)
    {
        $chat_id = $db->real_escape_string($chat_id);
        $result = $db->query("SELECT user_id FROM user as u WHERE u.chat_id = $chat_id;");
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $user_id = $row[dbColumnName::USER_ID];
        return $user_id;
    }

    function create_user($chat_id, $name)
    {
        $name = $db->real_escape_string($name);
        $chat_id = $db->real_escape_string($chat_id);;
        $create_date = date(DATE_FORMAT);
        $result = $db->query("INSERT INTO user (chat_id, username, create_date) VALUES ('$chat_id', '$name', '$create_date');");
        $result->free();

        $user_id = get_user_id($chat_id);
        $result = $db->query("INSERT INTO user_last_connect (user_id, user_last_connect_date) VALUES ('$user_id', '$create_date');");
    }

    function add_url($chat_id, $url)
    {
        $chat_id = $db->real_escape_string($chat_id);
        $url = $db->real_escape_string($url);
        $user_id = get_user_id($chat_id);
        
        if(!is_url_set($user_id, $url)){
            $result = $db->query("INSERT INTO user_url (user_id, user_url) VALUES ('$user_id', '$url);");
        }
        
        return;
    }

    function getUserUrlList($chat_id):string
    {
        $chat_id = $db->real_escape_string($chat_id);
        $user_id = get_user_id($chat_id);
        $result = $db->query("SELECT uu.user_url FROM user_url as uu WHERE uu.user_id = $user_id ORDER BY uu.user_url_id DESC LIMIT 100;");
        
        if ($result->num_rows > 0) {
            $resultArray = $result->fetch_array(MYSQLI_ASSOC);
            foreach($resultArray as $currentUrl) {
                $userUrlList = $currentUrl . " ";
            }
        }else
        {
            $userUrlList = '';
        }

        return $userUrlList;
    }

    function update_last_activity_user($chat_id)
    {
        $chat_id = $db->real_escape_string($chat_id);
        $user_id = get_user_id($chat_id);
        $today = date(DATE_FORMAT);
        $result = $db->query("UPDATE user_last_connect as ul SET ul.user_last_connect_date = '$today' WHERE ul.user_id = $user_id;");
        return;
    }

    $db->close();
    
?>















