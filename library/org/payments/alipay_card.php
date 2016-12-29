
<?php

function alipay($card){
    $url="https://ccdcapi.alipay.com/validateAndCacheCardInfo.json?_input_charset=utf-8&cardNo=".$card."&cardBinCheck=true";

    $data=file_get_contents($url);

    $json=json_decode($data);

    $arr = object_array($json);

    return $arr;
}

function object_array($array)
{
    if(is_object($array))
    {
        $array = (array)$array;
    }
    if(is_array($array))
    {
        foreach($array as $key=>$value)
        {
            $array[$key] = object_array($value);
        }
    }
    return $array;
}

//echo alipay("4063661347368727");
?>