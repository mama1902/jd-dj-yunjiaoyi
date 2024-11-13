<?php

namespace app\common\logic\jd;

use app\common\logic\jd\dto\cancelAfsApply;
use app\common\logic\jd\dto\cancelOrder;
use app\common\logic\jd\dto\createAfsApply;
use app\common\logic\jd\dto\getAfsServiceDetail;
use app\common\logic\jd\dto\getApplyReason;
use app\common\logic\jd\dto\getIsCanApplyInfo;
use app\common\logic\jd\dto\getLogisticsAddress;
use app\common\logic\jd\dto\postBackLogisticsBillParam;
use think\Log;

class JdMQ
{
    private $url;

    public function __construct()
    {
        $this->url = "https://jcq-shared-004.cn-north-1.jdcloud.com";
        $this->accessKey = "JDC_17385DA467D238758F1052C2A4E6";
        $this->secretKey = "FD139B23879B0B2466B7A25A20C5368B";
        $this->topic = "";
        $this->consumerGroupId = "";
    }

    public function ct_afs_create()
    {
        $topic = '568091687201$Default$open_message_ct_afs_create_7DD808DD0931DB8B26D573998B67DEB1';
        $size = 1;
        $consumerGroupId = 'open_message_627282396775';
        $url = $this->url . "/v2/messages?topic=" . $topic . "&consumerGroupId=" . $consumerGroupId . "&size=" . $size;
        $dateTime = gmdate("Y-m-d\TH:i:s\Z");
        $headers[] = 'accessKey: ' . $this->accessKey;
        $headers[] = 'dateTime: ' . $dateTime;
        $data['accessKey'] = $this->accessKey;
        $data['dateTime'] = $dateTime;
        $data['topic'] = $topic;
        $data['consumerGroupId'] = $consumerGroupId;
        $data['size'] = $size;
        $headers[] = 'signature: ' . $this->getSignature($data);
        $get = $this->postData($url, $headers);
        return json_decode($get, true);
    }

    public function afs_step_result()
    {
        $topic = '568091687201$Default$open_message_ct_afs_step_result_7DD808DD0931DB8B26D573998B67DEB1';
        $size = 1;
        $consumerGroupId = 'open_message_627282396775';
        $url = $this->url . "/v2/messages?topic=" . $topic . "&consumerGroupId=" . $consumerGroupId . "&size=" . $size;
        $dateTime = gmdate("Y-m-d\TH:i:s\Z");
        $headers[] = 'accessKey: ' . $this->accessKey;
        $headers[] = 'dateTime: ' . $dateTime;
        $data['accessKey'] = $this->accessKey;
        $data['dateTime'] = $dateTime;
        $data['topic'] = $topic;
        $data['consumerGroupId'] = $consumerGroupId;
        $data['size'] = $size;
        $headers[] = 'signature: ' . $this->getSignature($data);
        $get = $this->postData($url, $headers);
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
        curl_close($ch);
        // $handles = trim($handles, chr(239) . chr(187) . chr(191) . PHP_EOL);
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