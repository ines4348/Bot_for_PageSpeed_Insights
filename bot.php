<?php

    require_once("vendor/autoload.php");
    require_once("pagespeed_api.php"); 
    require_once("db_handler.php"); 
    require_once("config.php");

    const WELCOME_USER = "Добро пожаловать в бота, {name}!"; 
    const WELCOME_INCOGNIT = "Добро пожаловать в бота, незнакомец!";
    const COMMAND_START = "/start";
    const COMMAND_HELP = "/help";
    const COMMAND_CHECK = "/check";
    const COMMAND_VIEW_LIST_COMMAND = "Список команд";
    const LIST_COMMAND = "/start - начать общение \n/check {указать url} - запуск проверки, можно указать несколько адресов через пробел, каждый адрес начинается с https://";
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
    $chat_id = $result[TelegramCommandKey::MESSAGE][TelegramCommandKey::CHAT][TelegramCommandKey::ID]; 
    $name = $result[TelegramCommandKey::MESSAGE][TelegramCommandKey::FROM][TelegramCommandKey::USERNAME]; 
    $keyboard = [COMMAND_VIEW_LIST_COMMAND]; 

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

    function analyzeMessage($text, $welcomeMessage, $chat_id)
    {
        
        switch ($text) {
            case COMMAND_START:
                $reply = $welcomeMessage;
                break;
            case COMMAND_HELP:
                $reply = LIST_COMMAND;
                break;
            default:
                $reply = switchCommand($text);
                break;
        }
        return $reply;
    }

    function switchCommand($text)
    {
        $separatedText = explode(" ", $text);
        if($separatedText[0] == COMMAND_CHECK)
        {
            $delItem = array_shift($separatedText);
            foreach($separatedText as $currentUrl)
            {
                if(strripos($currentUrl, CONDITION_FOR_URL) == 0)  
                {
                    addUrl($chat_id, $currentUrl);
                    updateLastActivityUser($chat_id);
                    $urlForPingApi = str_replace("{currentUrl}", $currentUrl, URL_API);
                    $reply = getResultFromApi($urlForPingApi);
                }
            }
        }
        else{
            $reply = str_replace("{text}", $text, COMMAND_NOT_FOUND);
        }
        return $reply;
    }

    if($text){       
       if(isUserSet($chat_id) == false){
           $temp = createUser($chat_id, $name);
       }
       $welcomeMessage = setWelcomeMessage($name);
       $replay_message=analyzeMessage($text, $welcomeMessage, $chat_id);
       $telegram->sendMessage([ TelegramCommandKey::CHAT_ID => $chat_id, TelegramCommandKey::TEXT => $replay_message]);
    }
?>















