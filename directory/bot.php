<?php
    include('../vendor/autoload.php'); //Подключаем библиотеку
    include("pagespeed_api.php"); //Подключаем библиотеку

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
    $telegram = new Api(BOT_KEY); //Устанавливаем токен, полученный у BotFather
    $result = $telegram -> getWebhookUpdates(); //Передаем в переменную $result полную информацию о сообщении пользователя
    $text = $result[TelegramCommandKey.MESSAGE][TelegramCommandKey.TEXT]; //Текст сообщения
    $separatedText = explode(" ", $text);
    $chat_id = $result[TelegramCommandKey.MESSAGE][TelegramCommandKey.CHAT][ID]; //Уникальный идентификатор пользователя 1
    $name = $result[TelegramCommandKey.MESSAGE][FROM][USERNAME]; //Юзернейм пользователя
    $keyboard = [[COMMAND_VIEW_LIST_COMMAND]]; //Клавиатура

    $welcomeMessage = str_replace("{name}", $name, WELCOME_USER);

    function getTextMessage(): string
    {
        return $text;
    }

    function getChatIdMessage(): string
    {
        return $chat_id;
    }

    function getUsername(): string
    {
        return $name;
    }

    function getKeyboard(): string
    {
        return $keyboard;
    }

    function sendMessageToChat($telegram, $chat_id, $reply)
    {
        $telegram -> sendMessage([TelegramCommandKey.CHAT_ID => $chat_id, TelegramCommandKey.PARSE_MODE => TelegramCommandKey.HTML, TelegramCommandKey.TEXT_MESSAGE => $reply]);
        return;
    }
    
    function analyzeMessage($text)
    {
        if($text){
            if($text == COMMAND_START) {
                if(isset($name))
                {
                    sendMessageToChat($telegram, $chat_id, $welcomeMessage); 
                }else 
                {
                    sendMessageToChat($telegram, $chat_id, WELCOME_INCOGNIT);
                }
            }elseif($text == COMMAND_HELP) {
                sendMessageToChat($telegram, $chat_id, LIST_COMMAND);           
            }elseif($separatedText[0] == COMMAND_CHECK) {
                $delItem = array_shift($separatedText);
                foreach($separatedText as $currentUrl)
                {
                    if(substr($currentUrl, 0, 8) == CONDITION_FOR_URL)  
                    {
                        $urlForPingApi = URL_API;
                        $reply = getResponseApi($urlForPingApi);
                        sendMessageToChat($telegram, $chat_id, $reply);
                    }
                }
            }
        }else{
            $reply = COMMAND_NOT_FOUND;
            sendMessageToChat($telegram, $chat_id, $reply);
        }
        return;
    }

    analyzeMessage($text);
?>


















