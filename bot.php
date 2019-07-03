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
    const TELEGRAM_KEY_CHAT_ID = 'chat_id';
    const TELEGRAM_KEY_MESSAGE = 'message';
    const TELEGRAM_KEY_TEXT = 'text';
    const TELEGRAM_KEY_CHAT = 'chat';
    const TELEGRAM_KEY_ID = 'id';
    const TELEGRAM_KEY_FROM = 'from';
    const TELEGRAM_KEY_USERNAME = 'username';
    const TELEGRAM_KEY_PARSE_MODE = 'parse_mode';
    const TELEGRAM_KEY_HTML = 'HTML';




    const CONDITION_FOR_URL = "https://";
    const COMMAND_NOT_FOUND = "По запросу \"<b>{text}</b>\" ничего не найдено.";

//const DB_URL mysql://b60754546ea096:36da8d02@us-cdbr-iron-east-02.cleardb.net/heroku_7fe864cef8db15a?reconnect=true
//const DB_NAME heroku_7fe864cef8db15a
    
    use Telegram\Bot\Api; 
    $telegram = new Api(BOT_KEY); //Устанавливаем токен, полученный у BotFather
    $result = $telegram -> getWebhookUpdates(); //Передаем в переменную $result полную информацию о сообщении пользователя
    $text = $result[TELEGRAM_KEY_MESSAGE][TELEGRAM_KEY_TEXT]; //Текст сообщения
    $separatedText = explode(" ", $text);
    $chat_id = $result[TELEGRAM_KEY_MESSAGE][TELEGRAM_KEY_CHAT][ID]; //Уникальный идентификатор пользователя 1
    $name = $result[TELEGRAM_KEY_MESSAGE][FROM][USERNAME]; //Юзернейм пользователя
    $keyboard = [[COMMAND_VIEW_LIST_COMMAND]]; //Клавиатура

    $telegram = new Api('375466075:AAEARK0r2nXjB67JiB35JCXXhKEyT42Px8s'); //Устанавливаем токен, полученный у BotFather
    $result = $telegram -> getWebhookUpdates(); //Передаем в переменную $result полную информацию о сообщении пользователя
    
    $text = $result["message"]["text"]; //Текст сообщения
    $separatedText = explode(" ", $text);
    $chat_id = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
    $name = $result["message"]["from"]["username"]; //Юзернейм пользователя
    $keyboard = [["Последние статьи"],["Картинка"],["Гифка"]]; //Клавиатура



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

   $telegram->sendMessage([TELEGRAM_KEY_CHAT_ID => $chat_id, TELEGRAM_KEY_PARSE_MODE => TELEGRAM_KEY_HTML, TELEGRAM_KEY_MESSAGE => analyzeMessage($text)]);
?>















