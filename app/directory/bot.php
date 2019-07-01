<?php
    include('../vendor/autoload.php'); //Подключаем библиотеку
    use Telegram\Bot\Api; 

    $telegram = new Api('831949384:AAEdN3KQz00sMaFto2yLotRGETTFmw_dk7c'); //Устанавливаем токен, полученный у BotFather
    $result = $telegram -> getWebhookUpdates(); //Передаем в переменную $result полную информацию о сообщении пользователя
    
    $text = $result["message"]["text"]; //Текст сообщения
    $chat_id = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя 1
    $name = $result["message"]["from"]["username"]; //Юзернейм пользователя
    $keyboard = [["Последние статьи"],["Картинка"],["Гифка"],["View Hello, world!"]]; //Клавиатура
    // 1. инициализация
    $ch = curl_init();

    // 2. указываем параметры, включая url
    curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=https://developers.google.com");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);

    if($text){
         if ($text == "/start") {
            $reply = "Добро пожаловать в бота!";
            $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);
        }elseif ($text == "/sayhello") {
            $reply = "Hello, world!";
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
        }elseif ($text == "View Hello, world!") {
            $reply = "Hello, world!";
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);           
        }elseif ($text == "/sayhelloforperson") {
            if (isset($name))
            {
              $reply = "Hello, world, ".$name."!";
              $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]); 
            }else 
            {
              $reply = "Привет незнакомец!";
              $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
            }
        }elseif ($text == "/help") {
            $reply = "Информация с помощью.";
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);           
        }elseif ($text == "Картинка") {
            $url = "https://68.media.tumblr.com/6d830b4f2c455f9cb6cd4ebe5011d2b8/tumblr_oj49kevkUz1v4bb1no1_500.jpg";
            $telegram->sendPhoto([ 'chat_id' => $chat_id, 'photo' => $url, 'caption' => "Описание." ]);
        }elseif ($text == "Гифка") {
            $url = "https://68.media.tumblr.com/bd08f2aa85a6eb8b7a9f4b07c0807d71/tumblr_ofrc94sG1e1sjmm5ao1_400.gif";
            $telegram->sendDocument([ 'chat_id' => $chat_id, 'document' => $url, 'caption' => "Описание." ]);
        }elseif ($text == "Запрос API") {
              $output = curl_exec($ch);
              // А вдруг ошибочка?
              if ($output === FALSE) {
                //Тут-то мы о ней и скажем
                  echo "cURL Error: " . curl_error($ch);
                return;
              }
              
           //Получаем информацию о запросе
              $info = curl_getinfo($ch);
              //Выводим какую-то инфомрацию
              echo 'Запрос выполнился за  ' . $info['total_time'] . ' сек. к URL: ' . $info['url'];
              
              }
        }else{
        	$reply = "По запросу \"<b>".$text."</b>\" ничего не найдено.";
        	$telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode'=> 'HTML', 'text' => $reply ]);
        }
    }else{
    	$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => "Отправьте текстовое сообщение." ]);
    }
?>