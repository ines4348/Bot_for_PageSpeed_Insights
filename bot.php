<?php

    require_once("vendor/autoload.php");
    require_once("pagespeed_api.php"); 
    require_once("db_handler.php"); 

    const WELCOME_USER = "Добро пожаловать в бота, {name}!"; 
    const WELCOME_INCOGNIT = "Добро пожаловать в бота, незнакомец!";
    const COMMAND_START = "/start";
    const COMMAND_HELP = "/help";
    const COMMAND_CHECK = "/check";
    const COMMAND_VIEW_LIST_COMMAND = "Список команд";
    const LIST_COMMAND = "/start - начать общение \n/check {указать url} - запуск проверки, можно указать несколько адресов через пробел, каждый адрес начинается с https://";
    const URL_API = "https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url={currentUrl}&key=AIzaSyDZk6qaWml22Q8CiYms9Y8u4IkZ2rIsRVs&locale=RU";
    const BOT_KEY = "831949384:AAEdN3KQz00sMaFto2yLotRGETTFmw_dk7c";
    const CONDITION_FOR_URL = "https://";
    const COMMAND_NOT_FOUND = "По запросу \"{text}\" ничего не найдено.";

    class TelegramCommandKey {
        const CHAT_ID = "chat_id";
        const MESSAGE = "message";
        const TEXT = "text";
        const CHAT = "chat";
        const ID = "id";
        const FROM = "from";
        const USERNAME = "username";
        const PARSE_MODE = "parse_mode";
        const HTML = "HTML";
    }

    use Telegram\Bot\Api;
    $telegram = new Api(BOT_KEY);
    $result = $telegram -> getWebhookUpdates(); 
    $text = $result[TelegramCommandKey::MESSAGE][TelegramCommandKey::TEXT]; 
    $separatedText = explode(" ", $text);
    $chat_id = $result[TelegramCommandKey::MESSAGE][TelegramCommandKey::CHAT][TelegramCommandKey::ID]; 
    $name = $result[TelegramCommandKey::MESSAGE][TelegramCommandKey::FROM][TelegramCommandKey::USERNAME]; 
    $keyboard = [[COMMAND_VIEW_LIST_COMMAND]]; 

    function setWelcomeMessage($name)
    {
        if(isset($name))
        {
            $welcomeMessage = str_replace("{name}", $name, WELCOME_USER); 
        }else 
        {
            $welcomeMessage = WELCOME_INCOGNIT;
        }
        return $welcomeMessage;
    }

    function analyzeMessage($text, $welcomeMessage, $separatedText)
    {
        if($text == COMMAND_START)
        {
            $reply = $welcomeMessage; 
        }elseif($text == COMMAND_HELP)
        {
            $reply = LIST_COMMAND;           
        }elseif($separatedText[0] == COMMAND_CHECK)
        {
            $delItem = array_shift($separatedText);
            foreach($separatedText as $currentUrl)
            {
                if(substr($currentUrl, 0, 8) == CONDITION_FOR_URL)  
                {
                    $urlForPingApi = str_replace("{currentUrl}", $currentUrl, URL_API);
                    $reply = getResponseApi($urlForPingApi);
                }
            }
        }
        else{
            $reply = str_replace("{text}", $text, COMMAND_NOT_FOUND);
        }
        return $reply;
    }

   if($text){
       $telegram -> sendMessage([ TelegramCommandKey::CHAT_ID => $chat_id, TelegramCommandKey::TEXT => is_user_set($chat_id)]);
       if(is_user_set($chat_id) == false){
           $temp = create_user($chat_id, $name);
           $telegram -> sendMessage([ TelegramCommandKey::CHAT_ID => $chat_id, TelegramCommandKey::TEXT => $temp]);
	   }
       $welcomeMessage = setWelcomeMessage($name);
       $temp=analyzeMessage($text, $welcomeMessage, $separatedText);
       $telegram -> sendMessage([ TelegramCommandKey::CHAT_ID => $chat_id, TelegramCommandKey::TEXT => $temp]);
   }
?>















