<?php
    require_once("config.php");

    const DATE_FORMAT = "y.m.d";
    const LIMIT_OLD_RECORD = 10;

    $db = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);  

    if ($db->connect_errno) 
    {
        exit();
    }

    $db->query("SET NAMES utf8");
    $db->query("SET CHARACTER SET utf8");
    $db->query("SET COLLATION_CONNECTION='utf8_general_ci'"); 
    setlocale(LC_ALL,"ru_RU.UTF8");

    function isUserSet($chat_id)
    {
        global $db;
        $chat_id = $db->real_escape_string($chat_id);
        $result = $db->query("SELECT * FROM  user as u WHERE u.chat_id = $chat_id;");
        
        if($result->num_rows >= 1) 
        {
            return true;
        }
        
        return false;
    }

    function isUrlSet($url)
    {
        global $db; 
        $result = $db->query("SELECT * FROM url as ur WHERE ur.url = '$url';");
        
        if($result->num_rows >= 1) 
        {
            return true;
        }
        
        return false;
    }

    function deleteOldUrlData($url)
    {
        global $db; 
        $result = $db->query(
            "SELECT * FROM url as ur 
            INNER JOIN check_url_data as uc USING (url_id)
            WHERE ur.url = '$url'
            ORDER BY check_url_data_id;");
        
        if($result->num_rows >= LIMIT_OLD_RECORD) 
        {
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $check_url_data_id = $row['check_url_data_id'];
            deleteRecordUrlData($check_url_data_id);
        }
    }

    function deleteRecordUrlData($check_url_data_id)
    {
        global $db;
        $db->query("DELETE FROM check_url_data WHERE check_url_data_id = $check_url_data_id;");
    }

    function isUserUrlSet($user_id, $url_id)
    {
        global $db;
        $result = $db->query(
            "SELECT * FROM user_url as uu
            INNER JOIN url as ur USING (url_id)
            WHERE uu.user_id = $user_id AND ur.url_id = $url_id;");
        
        if($result->num_rows >= 1) 
        {
            return true;
        }
        
        return false;
    }

    function getUserId($chat_id)
    {
        global $db;
        $result = $db->query("SELECT user_id FROM user as u WHERE u.chat_id = $chat_id;");
        if($result->num_rows >= 1)
        {
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $user_id = $row['user_id'];
            return $user_id;
        }
    }

    function getUrlId($url)
    {
        global $db;
        $result = $db->query("SELECT url_id FROM url as ur WHERE ur.url = '$url';");
        if($result->num_rows >= 1)
        {
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $url_id = $row['url_id'];
            return $url_id;           
        }
    }

    function getUrlData($url)
    {
        global $db;
        $url = $db->real_escape_string($url);
        $url_id = getUrlId($url);
        $result = $db->query("SELECT check_url_data FROM check_url_data as uc WHERE uc.url_id = '$url_id';");
        if($result->num_rows >= 1)
        {
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $urlData = $row['check_url_data'];
            return $urlData;           
        }
        return false;
    }

    function createUser($chat_id, $name)
    {
        global $db;
        $name = $db->real_escape_string($name);
        $chat_id = $db->real_escape_string($chat_id);;
        $create_date = date(DATE_FORMAT);
        $result = $db->query("INSERT INTO user (chat_id, username, create_date) VALUES ('$chat_id', '$name', '$create_date');");

        $user_id = getUserId($chat_id);
        $result = $db->query("INSERT INTO user_last_connect (user_id, user_last_connect_date) VALUES ('$user_id', '$create_date');");
    }

    function addUrl($chat_id, $url)
    {
        global $db;
        $url = $db->real_escape_string($url);
        
        if(!isUrlSet($url)){
            $result = $db->query("INSERT INTO url (url) VALUES ('$url');");
        }
        addUserUrl($chat_id, $url);
    }

    function addUserUrl($chat_id, $url)
    {
        global $db;
        $chat_id = $db->real_escape_string($chat_id);
        $url = $db->real_escape_string($url);
        $user_id = getUserId($chat_id);
        $url_id = getUrlId($url);
        if(!(isUserUrlSet($user_id, $url_id))){ 
            $result = $db->query("INSERT INTO user_url (user_id, url_id) VALUES ($user_id, $url_id);");
        }
    }

    function getUserUrlList($chat_id):string
    {
        global $db;
        $chat_id = $db->real_escape_string($chat_id);
        $user_id = getUserId($chat_id);
        $result = $db->query(
            "SELECT ur.url FROM user_url as uu 
            INNER JOIN url as ur USING (url_id)
            WHERE uu.user_id = $user_id 
            ORDER BY uu.url_id DESC, ur.url 
            LIMIT 100;");
        if ($result->num_rows > 0) {
            while($urlList = $result->fetch_array(MYSQLI_ASSOC)) 
            {
                $userUrlList = $userUrlList . " " . $urlList["url"];
            }
        }else
        {
            $userUrlList = "";
        }

        return $userUrlList;
    }

    function getAllUrlList()
    {
        global $db;
        $result = $db->query(
            "SELECT url FROM url as ur 
            LIMIT 25000;");
        $allUrlList = array();
        if ($result->num_rows > 0) {
            while($urlItem = $result->fetch_array(MYSQLI_ASSOC)) 
            {
                $allUrlList[] = $urlItem["url"];
            }
        }else
        {
            $allUrlListp[] = "";
        }

        return $allUrlListp;
    }

    function isCheck():
    {
        global $db;
        $result = $db->query(
            "SELECT check_history_date FROM check_history as ch 
            ORDER BY check_history_id DESC LIMIT 1;");
        if ($result->num_rows > 0) {
            $row = $result->fetch_array(MYSQLI_ASSOC)) 
            $checkHistoryDate = $row['check_history_date'];
            if($checkHistoryDate < date(DATE_FORMAT))
            {
                return false;
            }
        }

        return true;
    }

    function updateLastActivityUser($chat_id)
    {
        global $db;
        $chat_id = $db->real_escape_string($chat_id);
        $user_id = getUserId($chat_id);
        $today = date(DATE_FORMAT);
        $result = $db->query("UPDATE user_last_connect as ul SET ul.user_last_connect_date = '$today' WHERE ul.user_id = $user_id;");
    }

    function addCheckUrlData($url, $data)
    {
        global $db;
        $url = $db->real_escape_string($url);
        $data = $db->real_escape_string($data);
        $url_id = getUrlId($url);
        deleteOldUrlData($url);
        $result = $db->query("INSERT INTO check_url_data (url_id, check_url_data) VALUES ($url_id, '$data');");
    }

    $db->close();

?>















