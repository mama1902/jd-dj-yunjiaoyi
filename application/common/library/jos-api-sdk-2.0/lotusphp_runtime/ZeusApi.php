<?php
/**
* Class ZeusApi 宙斯接口调用类
*/
use think\Env;
class ZeusApi
{
    public static $appKey = '7DD808DD0931DB8';    //  你的Key
    public static $appScret = 'cb2d656530ff4e5';   //  你的Secret
    private $app_token_json = '{"access_token":"","expires_in":86400,"refresh_token":"27a5d353a770418c927967dfb4367be5qznt","scope":"snsapi_base","open_id":"_Z6U2W9DWUOsC40qlZ_DlwwH8qCsqYIXchxRcswhq2Y","uid":"0642035069","time":1684684948658,"token_type":"bearer","code":0,"xid":"o*AATiJ1UqS5m894wzXxBC10BnODMxOJaHUoWuJXNS5zev2eSZ9pnPihM7tg_Zr4Zt9xPPYl6Y"}'; //  第一次需要手动授权获取京东Token然后粘贴到这里  c4dd9526076b46b7b74e587c1b4d3a2bkzdk/27a5d353a770418c927967dfb4367be5qznt
    public static $baseurl = "https://api.jd.com/routerjson?"; // https://api-dev.jd.com/routerjson https://api.jd.com/routerjson?
    /**
     * 获取宙斯接口数据
     * @param string $apiUrl 要获取的api
     * @param string $param_json 该api需要的参数，使用json格式，默认为 {}
     * @param string $version 版本可选为 2.0
     * @param bool $get 是否使用get，默认为post方式
     * @return mixed    京东返回的json格式的数据
     */
    public function GetZeusApiData($apiUrl = '', $param_json = array(), $version = '1.0', $get = 0, $forcedebug = 0)
    {
        $API['access_token'] = $this->refreshAccessToken(); //  生成的access_token，30天一换
        $API['app_key'] = self::$appKey;
        $API['method'] = $apiUrl;
        $API['360buy_param_json'] = $param_json;
        $API['timestamp'] = date('Y-m-d H:i:s', time());
        $API['v'] = $version;
        ksort($API);    //  排序
        $str = '';      //  拼接的字符串
        foreach ($API as $k => $v) $str .= $k . $v;
        $sign = strtoupper(md5(self::$appScret . $str . self::$appScret));    //  生成签名    MD5加密转大写
        if ($get) {
            //  用get方式拼接URL
            $url = self::$baseurl;
            foreach ($API as $k => $v)
                $url .= urlencode($k) . '=' . $v . '&';  //  把参数和值url编码
            $url .= 'sign=' . $sign;
            $res = self::curl_get($url);
        } else {
            //  用post方式获取数据
            $url = self::$baseurl;
            $API['sign'] = $sign;
            $res = self::curl_post($url, $API);

        }

        if ($forcedebug && Env::get('app.sqldebug') ) {
            $logmodel = new \app\admin\model\debug\Data;

            // 日志记录
            $cachekey = md5($param_json . $apiUrl . $res);
            $logid = $logmodel->where(['key' => $cachekey])->value('id');
            if (!$logid) {
                $datetime = date("Y-m-d H:i:s");
                $logmodel->save(['api_method' => $apiUrl, 'response_content' => $res, 're' => $param_json, 'key' => $cachekey, 'created_time' => $datetime, 'update_time' => $datetime]);
            }
        }
        return $res;
    }

//  刷新accessToken
    public function refreshAccessToken()
    {
        $filePath = dirname(dirname(__FILE__)) . '/Config/ZeusToken.config';     //  Token文本保存路径
        if (file_exists($filePath)) {
            $handle = fopen($filePath, 'r');
            $tokenJson = fread($handle, 8142);
        } else {
//  插入默认的token
            fwrite(fopen($filePath, 'w'), $this->app_token_json);
            $tokenJson = $this->app_token_json;
        }

        if (substr($tokenJson, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) {
            $tokenJson = substr($tokenJson, 3);
        }
        $res = json_decode(trim($tokenJson), true);   //  解析不了可能是文本出了问题
//  判断
        if ($res['code'] == 0) {
            if ($res['expires_in'] * 1000 + $res['time'] < self::getMillisecond() - 86400000) {    //  access_token失效前一天
//  获取刷新token的url
                $refreshUrl = "https://oauth.jd.com/oauth/token?";
                $refreshUrl .= '&client_id=' . self::$appKey;
                $refreshUrl .= '&client_secret=' . self::$appScret;
                $refreshUrl .= '&grant_type=refresh_token';
                $refreshUrl .= '&refresh_token=' . $res['refresh_token'];
//  获取新的token数据
                $newAccessTokenJson = self::curl_get($refreshUrl);
//  写入文本
                fwrite(fopen($filePath, 'w'), $newAccessTokenJson);
//  解析成数组
                $newAccessTokenArr = json_decode($newAccessTokenJson, true);
                $accessToken = $newAccessTokenArr['access_token'];
            } else {
                $accessToken = $res['access_token'];
            }
            return $accessToken;
        } else {
//  如果refresh_token过期，将会返回错误码code:2011;msg:refresh_token过期
            return $res['msg'];
        }
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
