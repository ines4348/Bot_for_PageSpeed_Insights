<?php
    include('../vendor/autoload.php'); //Подключаем библиотеку
    use Telegram\Bot\Api; 
    $telegram = new Api('831949384:AAEdN3KQz00sMaFto2yLotRGETTFmw_dk7c'); //Устанавливаем токен, полученный у BotFather
    $result = $telegram -> getWebhookUpdates(); //Передаем в переменную $result полную информацию о сообщении пользователя
    
    $text = $result["message"]["text"]; //Текст сообщения
    $separatedText = explode(" ", $text);
    $chat_id = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя 1
    $name = $result["message"]["from"]["username"]; //Юзернейм пользователя
    $keyboard = [["Список команд"],["Ping API"],["Получить ответ от API"]]; //Клавиатура

    
    function getFormatedJson($responseJson):string
    {
      $textJson = json_encode($responseJson, JSON_PRETTY_PRINT);
      return $textJson;
    }
    
    function getResponseApi($urlApi): mixed
    {
        return $urlApi;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $urlApi);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = curl_exec($ch);
        
        if ($output === FALSE) 
        {
          $output = "cURL Error: ".curl_error($ch);
        }
      
        curl_close($ch);
    }
    
    function getResponseApiInfo($urlApi):string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $urlApi);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = curl_exec($ch);
        $info = curl_getinfo($ch);
        
        if ($info === FALSE) 
        {
          $responseInfo = "cURL Error: ".curl_error($ch);
        }
        else
        {
          $responseInfo = "Запрос выполнился за  ".$info['total_time'].' сек. к URL: '.$info['url'];
        }
      
        curl_close($ch);
        return $responseInfo;
    }
    
    if($text){
        if ($text == "/start") {
          if (isset($name))
          {
            $reply = "Добро пожаловать в бота, ".$name."!";
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]); 
          }else 
          {
            $reply = "Добро пожаловать в бота, незнакомец!";
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
          }
        }elseif ($text == "Список команд") {
            $reply = "Информация с помощью.";
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);           
        }elseif ($separatedText[0] == "/check") {
            array_shift($separatedText);
            foreach ($separatedText as $currentUrl)
            {
              $urlForPingApi = "https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=".$currentUrl."&key=AIzaSyDZk6qaWml22Q8CiYms9Y8u4IkZ2rIsRVs";
              $responseJson = getResponseApi($urlForPingApi);
              $reply = getFormatedJson($responseJson);
              $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
            }
        }elseif ($text == "Ping API") {
            $urlForPingApi = "https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=https://developers.google.com&key=AIzaSyDZk6qaWml22Q8CiYms9Y8u4IkZ2rIsRVs";
            $responseApiInfo = getResponseApiInfo($urlForPingApi);
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $responseApiInfo ]);
        }else{
        	$reply = "По запросу \"<b>".$text."</b>\" ничего не найдено.";
        	$telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode'=> 'HTML', 'text' => $reply ]);
        }
    }else{
    	$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => "Отправьте текстовое сообщение." ]);
    }
?>