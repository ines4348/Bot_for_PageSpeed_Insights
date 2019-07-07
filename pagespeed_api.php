<?php

    const PARAMETR_MOBILE = "&strategy=mobile";
    const PARAMETR_DESCTOP = "&strategy=desktop";
    const PERFORMANCE = "Производительность: ";
    const MOBILE = "Мобильный телефон \n";
    const DESCTOP = "Компьютер \n";
    const NEWLINE = "\n";

    function getDataFromJson($responseJson): string
    {
        $textJson = json_decode($responseJson);
        $textResult = PERFORMANCE . $textJson->lighthouseResult->categories->performance->score;
        return $textResult;
    }

    function getResultFromApi($urlApi): string
    {
        $textResult = MOBILE . getResponseApi($urlApi . PARAMETR_MOBILE) . NEWLINE;
        $textResult = $textResult . DESCTOP . getResponseApi($urlApi . PARAMETR_DESCTOP);
        return $textResult;
    }
    
    function getResponseApi($urlApi): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $urlApi);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
      
        $output = curl_exec($ch);
        if($output === false) 
        {
            $textFromJson = "cURL Error: ".curl_error($ch);
        }
        else
        {
            $textFromJson = getDataFromJson($output);
        }
 
        curl_close($ch);
        return $textFromJson;
    }
?>