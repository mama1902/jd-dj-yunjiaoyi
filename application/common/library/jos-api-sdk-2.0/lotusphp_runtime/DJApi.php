<?php
/**
* Class DJApi 宙斯接口调用类
*/
class DJApi
{
    public static $appKey = 'd53e434947ab41d998995474025dbcf6';    //  你的Key
    public static $appScret = '74a796f1b97f40f9bba893cfd6a3528b';   //  你的Secret
    public static $app_token_json = 'd088b76f-9da5-4095-a3ce-adf8dc3db77e';
    public static $baseurl = "https://api.jd.com/routerjson?"; // https://api-dev.jd.com/routerjson https://api.jd.com/routerjson?




//  刷新accessToken
    public function refreshAccessToken($newtoken = '')
    {
        $filePath = dirname(dirname(__FILE__)) . '/Config/djToken.config';     //  Token文本保存路径


        if (file_exists($filePath) && empty($newtoken) ) {
            $handle = fopen($filePath, 'r');
            $tokenJson = fread($handle, 8142);
        } else {
            if ($newtoken) {
                fwrite(fopen($filePath, 'w'),   $newtoken);
                $tokenJson = $newtoken;
            } else {
                //  插入默认的token
                fwrite(fopen($filePath, 'w'),   $this->app_token_json);
                $tokenJson = self::$app_token_json;
            }
        }

        return $tokenJson;
    }

//  get请求
    private static function curl_get($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

//  post请求
    private static function curl_post($url, $curlPost)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

//  获取13位时间戳
    private static function getMillisecond()
    {
        list($t1, $t2) = explode(' ', microtime());
        return sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }
}
