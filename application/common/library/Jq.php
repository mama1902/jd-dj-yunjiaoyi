<?php

namespace app\common\library;

class Jq {
    private $url;

    public $posturl =  'http://111.229.37.112/api/cls/cp';

    public static $AURLs = [
       '25005217'  =>  'http://111.229.37.112/api/cls/cp',
        '25005674'  =>  'http://111.229.37.112:8080/api/cls/cp',
    ];

    public $mo = 1;
    public function __construct()
    {
        $this->url = "jcq-shared-004-httpsrv-nlb-FI.jvessel-open-hb.jdcloud.com:8080";
        $this->accessKey = "JDC_17385DA467D238758F1052C2A4E6";
        $this->secretKey = "FD139B23879B0B2466B7A25A20C5368B";
        $this->topic = "open_message_ct_sku_price_change_7DD808DD0931DB8B26D573998B67DEB1";
        $this->consumerGroupId="open_message_627282396775";
    }

    public function switchMo($mo)
    {
        $this->mo = $mo;
        switch ($mo) {
            case 1: // 价格
                $this->url = "jcq-shared-004-httpsrv-nlb-FI.jvessel-open-hb.jdcloud.com:8080";
                $this->topic = '568091687201$Default$open_message_ct_sku_price_change_7DD808DD0931DB8B26D573998B67DEB1';
                $this->consumerGroupId="open_message_627282396775";
             break;

            case 2: // 订单支付成功
                $this->url = "jcq-shared-004-httpsrv-nlb-FI.jvessel-open-hb.jdcloud.com:8080";
                $this->topic = '568091687201$Default$open_message_ct_order_pay_7DD808DD0931DB8B26D573998B67DEB1';
                $this->consumerGroupId="open_message_627282396775";
                break;
            case 3: // sku change
                $this->url = "jcq-shared-004-httpsrv-nlb-FI.jvessel-open-hb.jdcloud.com:8080";
                $this->topic = '568091687201$Default$open_message_ct_sku_change_7DD808DD0931DB8B26D573998B67DEB1';
                $this->consumerGroupId="open_message_627282396775";
                break;
            case 4: // 订单出库
                $this->url = "jcq-shared-004-httpsrv-nlb-FI.jvessel-open-hb.jdcloud.com:8080";
                $this->topic = '568091687201$Default$open_message_ct_order_stockout_7DD808DD0931DB8B26D573998B67DEB1';
                $this->consumerGroupId="open_message_627282396775";
                break;
            case 5: // 订单取消
                $this->url = "jcq-shared-004-httpsrv-nlb-FI.jvessel-open-hb.jdcloud.com:8080";
                $this->topic = '568091687201$Default$open_message_ct_order_cancel_7DD808DD0931DB8B26D573998B67DEB1';
                $this->consumerGroupId="open_message_627282396775";
                break;
            case 6: // 订单退款
                $this->url = "jcq-shared-004-httpsrv-nlb-FI.jvessel-open-hb.jdcloud.com:8080";
                $this->topic = '568091687201$Default$open_message_ct_order_refund_7DD808DD0931DB8B26D573998B67DEB1';
                $this->consumerGroupId="open_message_627282396775";
                break;
            case 7: // 订单完成
                $this->url = "jcq-shared-004-httpsrv-nlb-FI.jvessel-open-hb.jdcloud.com:8080";
                $this->topic = '568091687201$Default$open_message_ct_order_finish_7DD808DD0931DB8B26D573998B67DEB1';
                $this->consumerGroupId="open_message_627282396775";
                break;
            case 8: // 订单创建
                $this->url = "jcq-shared-004-httpsrv-nlb-FI.jvessel-open-hb.jdcloud.com:8080";
                $this->topic = '568091687201$Default$open_message_ct_order_create_7DD808DD0931DB8B26D573998B67DEB1';
                $this->consumerGroupId="open_message_627282396775";
                break;
            case 9: // 订单收货
                $this->url = "jcq-shared-004-httpsrv-nlb-FI.jvessel-open-hb.jdcloud.com:8080";
                $this->topic = '568091687201$Default$open_message_ct_order_delivered_7DD808DD0931DB8B26D573998B67DEB1';
                $this->consumerGroupId="open_message_627282396775";
                break;

        }

        return $this;
    }

    public function ct_order_create()
    {
        $topic = $this->topic;
        $size = 1;
        $consumerGroupId = $this->consumerGroupId;
        $url = $this->url . "/v2/messages?topic=" . $topic . "&consumerGroupId=" . $consumerGroupId . "&size=". $size;
        $dateTime = gmdate("Y-m-d\TH:i:s\Z");
        $headers[] = 'accessKey: ' . $this->accessKey;
        $headers[] = 'dateTime: ' . $dateTime;
        $data['accessKey'] = $this->accessKey;
        $data['dateTime'] = $dateTime;
        $data['topic'] = $topic;
        $data['consumerGroupId'] = $consumerGroupId;
        $data['size'] = $size;
        $headers[] = 'signature: ' . $this->getSignature($data);

//        print("\n");
//        print($url);
//        print("\n");
//        print_r($data);
//        print_r($headers);
//        print("\n");
//        print("\n");


        $get = $this->postData($url, $headers);

        $re =  json_decode($get, true);


         if ( !empty($re['result']['messages'][0]['messageBody'])) {
            $data = json_decode($re['result']['messages'][0]['messageBody'], true);
            $channelId = $data['channelId'];
            $this->posturl = self::$AURLs[$channelId] ?? $this->posturl;
         }

        return $re;
    }


    public function success_ct_order_create($ackIndex)
    {
        $topic = $this->topic;
        $consumerGroupId = $this->consumerGroupId;
        $url = $this->url . "/v2/ack";

        $dateTime = gmdate("Y-m-d\TH:i:s\Z");

        $post['topic'] = $topic;
        $post['consumerGroupId'] = $consumerGroupId;
        $post['ackAction'] = 'SUCCESS';
        $post['ackIndex'] = $ackIndex;

        $data['accessKey'] = $this->accessKey;
        $data['dateTime'] = $dateTime;

        $headers[] = 'Content-Type: application/json';
        $headers[] = 'accessKey: ' . $this->accessKey;
        $headers[] = 'dateTime: ' . $dateTime;

        $headers[] = 'signature: ' . $this->getSignature(array_merge($data, $post));
/**
        print("\n");
        print($url);
        print("\n");
        print_r($post);
        print_r($data);
        print_r($headers);
        print("\n");
        print("\n");
 */

        $get = $this->postData($url, $headers, $post);
        return json_decode($get, true);
    }
    protected function getSignature($params)
    {
        ksort($params);
        $buff = "";
        $signSource = "";
        foreach ($params as $k => $v) {
            $buff .= $k . "=" . $v . "&";
        }
        if (strlen($buff) > 0) {
            $signSource = substr($buff, 0, strlen($buff) - 1);
        }
        return $this->hmac_sha1($signSource, $this->secretKey);
    }
    protected function postData($url, $headers = array(), $data = '')
    {
        $ch = curl_init();
        $timeout = 300;
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($data) {
            $data_string = json_encode($data);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        if (count($headers) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $handles = curl_exec($ch);
        $errorMsg = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  //curl's http code
        if (strlen($handles) > 1 || !empty($errorMsg) ) {
            mylog("Autotask ".$this->mo, var_export(['url' => $url, 'data_string' => $data_string ?? '', 'code' => $httpCode, 'errormsg' => $errorMsg, 'response' => $handles], 1), 'AdaJq');
        }
        curl_close($ch);
        // $handles = trim($handles, chr(239) . chr(187) . chr(191) . PHP_EOL);
        return $handles;
    }


    public function postToCpData( $data_string = '', $mo = '')
    {
        $headers = [];
        $url = $this->posturl.($mo ?: $this->mo);
        $ch = curl_init();
        $timeout = 300;
        // curl_setopt($ch, CURLOPT_PORT, 8080);
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        if (count($headers) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $handles = curl_exec($ch);
        $errorMsg = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  //curl's http code

        curl_close($ch);echo $url.'-----------'.$handles."\r\n";

        return $handles;
    }



    function hmac_sha1($str, $key)
    {
        $signature = "";
        if (function_exists('hash_hmac')) {
            $signature = base64_encode(hash_hmac("sha1", $str, $key, true));
        } else {
            $blocksize = 64;
            $hashfunc = 'sha1';
            if (strlen($key) && $blocksize) {
                $key = pack('H*', $hashfunc($key));
            }
            $key = str_pad($key, $blocksize, chr(0x00));
            $ipad = str_repeat(chr(0x36), $blocksize);
            $opad = str_repeat(chr(0x5c), $blocksize);
            $hmac = pack(
                'H*',
                $hashfunc(
                    ($key ^ $opad) . pack(
                        'H*',
                        $hashfunc(
                            ($key ^ $ipad) . $str
                        )
                    )
                )
            );
            $signature = base64_encode($hmac);
        }
        return $signature;
    }
}

/*
$jcq = new \app\common\library\Jq();

while(true) {
    $pullResult = $jcq->ct_order_create();
    print_r($pullResult);

    if($pullResult["result"]["ackIndex"]){
        $ackResult=$jcq->success_ct_order_create($pullResult["result"]["ackIndex"]);
        print_r($ackResult);
    }
}
*/

?>

