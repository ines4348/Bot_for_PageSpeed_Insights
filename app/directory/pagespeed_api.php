<?php

    function getDataFromJson($responseJson): string
    {
        $textJson = json_decode($responseJson);
        $textResult = "Производительность: ".$textJson->lighthouseResult->categories->performance->score;
        return $textResult;
    }
    
    function getResponseApi($urlApi): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $urlApi);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
      
        $output = curl_exec($ch);
        if($output === FALSE) 
        {
            $textJson = "cURL Error: ".curl_error($ch);
        }
        else
        {
            $textJson = getDataFromJson($output);
        }
 
        curl_close($ch);
        return $textJson;
    }
?>