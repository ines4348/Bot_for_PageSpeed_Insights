<?php
    include('../vendor/autoload.php'); //Подключаем библиотеку
    use Telegram\Bot\Api; 
    $telegram = new Api('831949384:AAEdN3KQz00sMaFto2yLotRGETTFmw_dk7c'); //Устанавливаем токен, полученный у BotFather
    $result = $telegram -> getWebhookUpdates(); //Передаем в переменную $result полную информацию о сообщении пользователя
    
    $text = $result["message"]["text"]; //Текст сообщения
    $separatedText = explode(" ", $text);
    $chat_id = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя 1
    $name = $result["message"]["from"]["username"]; //Юзернейм пользователя
    $keyboard = [["Последние статьи"],["Ping API"],["Получить ответ от API"],["View Hello, world!"]]; //Клавиатура

    
    function getFormatedJson($responseJson):string
    {
      $jsonDecode = json_decode($responseJson);
      $textJson = "Chrome User Experience Report Results<br/>First Contentful Paint: ".$jsonDecode->FIRST_CONTENTFUL_PAINT_MS->category."<br/>"..$jsonDecode->FIRST_INPUT_DELAY_MS->category;
    }
    
    function getApiResponse($urlApi):string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $urlApi);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = curl_exec($ch);
        if ($output === FALSE) 
        {
          $errorMessage = "cURL Error: ".curl_error($ch);
          return $errorMessage;
        }
        else
        {
          return $output;
        }
        curl_close($ch);
    }
    
    function getApiResponseInfo($urlApi):string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $urlApi);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $info = curl_getinfo($ch);
        if ($info === FALSE) 
        {
          $errorMessage = "cURL Error: ".curl_error($ch);
          return $errorMessage;
        }
        else
        {
          return $info;
        }
        curl_close($ch);
    }
    
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
        }elseif ($separatedText[0] == "/check") {
            array_shift($separatedText);
            foreach ($separatedText as $currentUrl)
            {
              $urlForPingApi = "https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=".$currentUrl;
              $responseJson = getApiResponse($urlForPingApi);
              $reply = getFormatedJson($responseJson);
              $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
            }
        }elseif ($text == "Ping API") {
            $urlForPingApi = "https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=https://developers.google.com";
            $responseApiInfo = getApiResponseInfo($urlForPingApi);
            $currentMessage = "Запрос выполнился за  ".$responseApiInfo['total_time'].' сек. к URL: '.$responseApiInfo['url'];
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $currentMessage ]);
        }else{
        	$reply = "По запросу \"<b>".$text."</b>\" ничего не найдено.";
        	$telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode'=> 'HTML', 'text' => $reply ]);
        }
    }else{
    	$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => "Отправьте текстовое сообщение." ]);
    }
?>