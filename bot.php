<?php
    include('vendor/autoload.php'); //Подключаем библиотеку
    use Telegram\Bot\Api; 

    $telegram = new Api('831949384:AAEdN3KQz00sMaFto2yLotRGETTFmw_dk7c'); //Устанавливаем токен, полученный у BotFather
    $result = $telegram -> getWebhookUpdates(); //Передаем в переменную $result полную информацию о сообщении пользователя
    
    $text = $result["message"]["text"]; //Текст сообщения
    $chat_id = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
    $name = $result["message"]["from"]["username"]; //Юзернейм пользователя
    $keyboard = [["Последние статьи"],["Картинка"],["Гифка"]]; //Клавиатура

    if($text){
         if ($text == "/start") {
            $reply = "Добро пожаловать в бота!";
            $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);
        }elseif ($text == "/help") {
            $reply = "Информация с помощью.";
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
        }elseif ($text == "Картинка") {
            $url = "https://68.media.tumblr.com/6d830b4f2c455f9cb6cd4ebe5011d2b8/tumblr_oj49kevkUz1v4bb1no1_500.jpg";
            $telegram->sendPhoto([ 'chat_id' => $chat_id, 'photo' => $url, 'caption' => "Описание." ]);
        }elseif ($text == "Гифка") {
            $url = "https://68.media.tumblr.com/bd08f2aa85a6eb8b7a9f4b07c0807d71/tumblr_ofrc94sG1e1sjmm5ao1_400.gif";
            $telegram->sendDocument([ 'chat_id' => $chat_id, 'document' => $url, 'caption' => "Описание." ]);
        }elseif ($text == "Последние статьи") {
            $html=simplexml_load_file('http://netology.ru/blog/rss.xml');
            foreach ($html->channel->item as $item) {
	     $reply .= "\xE2\x9E\xA1 ".$item->title." (<a href='".$item->link."'>читать</a>)\n";
        	}
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode' => 'HTML', 'disable_web_page_preview' => true, 'text' => $reply ]);
        }else{
        	$reply = "По запросу \"<b>".$text."</b>\" ничего не найдено.";
        	$telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode'=> 'HTML', 'text' => $reply ]);
        }
    }else{
    	$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => "Отправьте текстовое сообщение." ]);
    }








/*<?php

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
        $telegram->sendMessage([TelegramCommandKey.CHAT_ID => $chat_id, TelegramCommandKey.PARSE_MODE => TelegramCommandKey.HTML, TelegramCommandKey.TEXT_MESSAGE => $reply]);
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

*/

?>














