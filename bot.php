<?php

    require_once ('vendor/autoload.php'); //Подключаем библиотеку
    require_once ("pagespeed_api.php"); //Подключаем библиотеку

    const WELCOME_USER = "Добро пожаловать в бота, {name}!"; 
    const WELCOME_INCOGNIT = "Добро пожаловать в бота, незнакомец!";
    const COMMAND_START = "/start";
    const COMMAND_HELP = "/help";
    const COMMAND_CHECK = "/check";
    const COMMAND_VIEW_LIST_COMMAND = "Список команд";
    const LIST_COMMAND = "/start - начать общение <br/> /check {указать url} - запуск проверки, можно указать несколько адресов через пробел, каждый адрес начинается с https://";
    const URL_API = "https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url={currentUrl}&key=AIzaSyDZk6qaWml22Q8CiYms9Y8u4IkZ2rIsRVs&locale=RU";
    const BOT_KEY = '831949384:AAEdN3KQz00sMaFto2yLotRGETTFmw_dk7c';
    const CONDITION_FOR_URL = "https://";
    const COMMAND_NOT_FOUND = "По запросу \"<b>{text}</b>\" ничего не найдено.";

//const DB_URL mysql://b60754546ea096:36da8d02@us-cdbr-iron-east-02.cleardb.net/heroku_7fe864cef8db15a?reconnect=true
//const DB_NAME heroku_7fe864cef8db15a

    class TelegramCommandKey {
        const CHAT_ID = 'chat_id';
        const MESSAGE = 'message';
        const TEXT = 'text';
        const CHAT = 'chat';
        const ID = 'id';
        const FROM = 'from';
        const USERNAME = 'username';
        const PARSE_MODE = 'parse_mode';
        const HTML = 'HTML';
    }
    use Telegram\Bot\Api;
    $telegram = new Api(BOT_KEY);
    $result = $telegram -> getWebhookUpdates(); 
    
    $text = $result["message"]["text"]; 
    $separatedText = explode(" ", $text);
    $chat_id = $result["message"]["chat"]["id"]; 
    $name = $result["message"]["from"]["username"]; 
    $keyboard = [["Последние статьи"],["Картинка"],["Гифка"]]; 

 /*   
    $telegram = new Api(BOT_KEY); 
    $result = $telegram -> getWebhookUpdates(); 
    $text = $result[TelegramCommandKey::MESSAGE][TelegramCommandKey::TEXT]; 
    
    $chat_id = $result[TelegramCommandKey::MESSAGE][TelegramCommandKey::CHAT][TelegramCommandKey::ID]; 
    $name = $result[TelegramCommandKey::MESSAGE][TelegramCommandKey::FROM][TelegramCommandKey::USERNAME]; 
    $keyboard = [[COMMAND_VIEW_LIST_COMMAND]]; 
*/
    $welcomeMessage = str_replace("{name}", $name, WELCOME_USER);

    function analyzeMessage($text)
    {
        if($text){
            if($text == COMMAND_START) {
                if(isset($name))
                {
                    $reply = $welcomeMessage; 
                }else 
                {
                    $reply = WELCOME_INCOGNIT;
                }
            }elseif($text == COMMAND_HELP) {
                $reply = LIST_COMMAND;           
            }elseif($separatedText[0] == COMMAND_CHECK) {
                $delItem = array_shift($separatedText);
                foreach($separatedText as $currentUrl)
                {
                    if(substr($currentUrl, 0, 8) == CONDITION_FOR_URL)  
                    {
                        $urlForPingApi = URL_API;
                        $reply = getResponseApi($urlForPingApi);
                    }
                }
            }
        }else{
            $reply = COMMAND_NOT_FOUND;
        }
        return $reply;
    }

   /*$telegram->sendMessage([TelegramCommandKey::CHAT_ID => $chat_id, TelegramCommandKey::PARSE_MODE => TelegramCommandKey::HTML, TelegramCommandKey::MESSAGE => analyzeMessage($text)]);*/
   if($text){
       $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => "Test" ]);
   }
?>















