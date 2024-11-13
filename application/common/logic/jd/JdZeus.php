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

/**
 * 京东云交易接口
 */
class JdZeus
{
    /*
     * @var \JdClient
     */
    public $jdclent = null;

    /*
     * @var \ZeusApi
     */
    public $ZeusApi = null;

    public function __construct($jdclent = null, $zeusApi = null)
    {
        if ($jdclent != null) {
            $this->jdclent = $jdclent;
        }
        if ($zeusApi != null) {
            $this->ZeusApi = $zeusApi;
        }
        if (empty($this->ZeusApi)) {

            ini_set("memory_limit", "-1");

            /**
             * 找到lotusphp入口文件，并初始化lotusphp
             * lotusphp是一个第三方php框架，其主页在：lotusphp.googlecode.com
             */

            /**
             * JD SDK 入口文件
             * 请不要修改这个文件，除非你知道怎样修改以及怎样恢复
             */

            /**
             * 定义常量开始
             * 在include("JdSdk.php")之前定义这些常量，不要直接修改本文件，以利于升级覆盖
             */
            /**
             * SDK工作目录
             * 存放日志，JD缓存数据
             */
            if (!defined("JD_SDK_WORK_DIR")) {
                define("JD_SDK_WORK_DIR", APP_PATH . 'common/library/jos-api-sdk-2.0/' . "logs" . DIRECTORY_SEPARATOR);
            }
            /**
             * @var $requiestobj \ZeusApi
             * 是否处于开发模式
             * 在你自己电脑上开发程序的时候千万不要设为false，以免缓存造成你的代码修改了不生效
             * 部署到生产环境正式运营后，如果性能压力大，可以把此常量设定为false，能提高运行速度（对应的代价就是你下次升级程序时要清一下缓存）
             */
            if (!defined("JD_SDK_DEV_MODE")) {
                define("JD_SDK_DEV_MODE", true);
            }

            $lotusHome = APP_PATH . 'common/library/jos-api-sdk-2.0/' . "lotusphp_runtime" . DIRECTORY_SEPARATOR;
            include_once($lotusHome . "Lotus.php");
            include_once($lotusHome . "ZeusApi.php");
            include_once($lotusHome . "DJApi.php");
            $lotus = new \Lotus;
            $lotus->option["autoload_dir"] = APP_PATH . 'common/library/jos-api-sdk-2.0/' . "jd";
            $lotus->devMode = JD_SDK_DEV_MODE;
            $lotus->defaultStoreDir = JD_SDK_WORK_DIR;
            $lotus->init();


            $requiestobj = new \ZeusApi();
            $c = new \JdClient();
            $c->appKey = \ZeusApi::$appKey;
            $c->appSecret = \ZeusApi::$appScret;
            $c->accessToken = $requiestobj->refreshAccessToken();
            $c->serverUrl = \ZeusApi::$baseurl;
            $this->jdclent = $c;
            $this->ZeusApi = $requiestobj;
        }

    }

    /*
     * 查询订单是否可申请售后API
     * @param getIsCanApplyInfo $getIsCanApplyInfo 申请参数
     *
     */
    public function getIsCanApplyInfo(getIsCanApplyInfo $getIsCanApplyInfo)
    {
        $ctpProtocol17 = $this->getCtpProtocol17("canapply");
        $canApplyInfoParam = new \CanApplyInfoParam();
        $canApplyInfoParam->setPin("jd_461d06fc0cb6c");
        $canApplyInfoParam->setSkuId($getIsCanApplyInfo->skuId);
        $canApplyInfoParam->setOrderId($getIsCanApplyInfo->orderId);
        $req = new \CtpAfsOperateApplyGetIsCanApplyInfoRequest();
        $req->setCtpProtocol($ctpProtocol17->getInstance());
        $req->setCanApplyInfoParam($canApplyInfoParam->getInstance());
        $resp2 = $this->ZeusApi->GetZeusApiData($req->getApiMethodName(), $req->getApiParas(), '1.0', 0, 1);
        $resp2 = json_decode($resp2, true);
        $responce = $resp2['jingdong_ctp_afs_operate_apply_getIsCanApplyInfo_responce'] ?? [];
        return $responce['result'] ?? [];
    }

    /*
     * 获取售后申请原因列表API
     * @param getApplyReason $getApplyReason 申请参数
     *
     */
    public function getApplyReason(getApplyReason $getApplyReason)
    {
        $ctpProtocol17 = $this->getCtpProtocol17("applyreason");
        $applyReasonParam = new \ApplyReasonParam();
        $applyReasonParam->setOrderId($getApplyReason->orderId);
        $applyReasonParam->setSkuId($getApplyReason->skuId);
        $applyReasonParam->setPin("jd_461d06fc0cb6c");
        $applyReasonParam->setAfsType($getApplyReason->afsType);
        $req = new \CtpAfsOperateApplyGetApplyReasonRequest();
        $req->setCtpProtocol($ctpProtocol17->getInstance());
        $req->setApplyReasonParam($applyReasonParam->getInstance());
        $resp2 = $this->ZeusApi->GetZeusApiData($req->getApiMethodName(), $req->getApiParas(), '1.0', 0, 1);
        $resp2 = json_decode($resp2, true);
        $responce = $resp2['jingdong_ctp_afs_operate_apply_getApplyReason_responce'] ?? [];
        return $responce['result'] ?? [];
    }

    /*
     * 生成售后单
     * @param createAfsApply $createAfsApply 申请参数
     *
     */
    public function createAfsApply(createAfsApply $createAfsApply)
    {
        $ctpProtocol17 = $this->getCtpProtocol17("createafsapply");
        $param = new \AfsApplyParam();
        $param->setApplyReasonName($createAfsApply->applyReasonName);
        $param->setApplyReasonId($createAfsApply->applyReasonId);
        $param->setChannelAfsApplyId($createAfsApply->channelAfsApplyId);
        $param->setPin("jd_461d06fc0cb6c");
        $param->setAfsType($createAfsApply->afsType);
        $param->setOrderId($createAfsApply->orderId);
        $param->setSkuQuantity($createAfsApply->skuQuantity);
        $param->setPickWareType($createAfsApply->pickWareType);
        $req = new \CtpAfsOperateApplyCreateAfsApplyRequest();
        $req->setCtpProtocol($ctpProtocol17->getInstance());
        $req->setAfsApplyParam($param->getInstance());
        $resp2 = $this->ZeusApi->GetZeusApiData($req->getApiMethodName(), $req->getApiParas(), '1.0', 0, 1);
        $resp2 = json_decode($resp2, true);
        Log::record("fun createapply: end" . json_encode($resp2));
        $responce = $resp2['jingdong_ctp_afs_operate_apply_createAfsApply_responce'] ?? [];
        return $responce['result'] ?? [];
    }

    /*
     * 售后服务单取消API
     * @param cancelAfsApply $cancelAfsApply
     */
    public function cancelAfsService(cancelAfsApply $cancelAfsApply)
    {
        $ctpProtocol17 = $this->getCtpProtocol17("cancelafsservice");
        $param = new \CancelAfsServiceParam();
        $param->setAfsServiceId($cancelAfsApply->afsServiceId);
        $param->setPin("jd_461d06fc0cb6c");
        $req = new \CtpAfsServicenbillCancelAfsServiceRequest();
        $req->setCtpProtocol($ctpProtocol17->getInstance());
        $req->setCancelAfsServiceParam($param->getInstance());
        $resp2 = $this->ZeusApi->GetZeusApiData($req->getApiMethodName(), $req->getApiParas(), '1.0', 0, 1);
        $resp2 = json_decode($resp2, true);
        return $resp2;
    }

    /*
     * 获取售后服务单回寄地址API
     * @param getLogisticsAddress $getLogisticsAddress
     */
    public function getLogisticsAddress(getLogisticsAddress $getLogisticsAddress)
    {
        $ctpProtocol17 = $this->getCtpProtocol17("getLogisticsAddress");
        $param = new \LogisticsAddressParam();
        $param->setPin("jd_461d06fc0cb6c");
        $param->setAfsServiceId($getLogisticsAddress->afsServiceId);
        $req = new \CtpAfsLogisticsGetLogisticsAddressRequest();
        $req->setCtpProtocol($ctpProtocol17->getInstance());
        $req->setLogisticsAddressParam($param->getInstance());
        $resp2 = $this->ZeusApi->GetZeusApiData($req->getApiMethodName(), $req->getApiParas(), '1.0', 0, 1);
        $resp2 = json_decode($resp2, true);
        return $resp2;
    }


    /*
     * 获取售后服务单详情API
     * @param getAfsServiceDetail $getAfsServiceDetail
     */
    public function getAfsServiceDetail(getAfsServiceDetail $getAfsServiceDetail)
    {
        $ctpProtocol17 = $this->getCtpProtocol17("getAfsServiceDetail");
        $param = new \AfsServiceDetailParam();
        $param->setAfsServiceId($getAfsServiceDetail->afsServiceId);
        $param->setPin("jd_461d06fc0cb6c");
        $req = new \CtpAfsServicenbillGetAfsServiceDetailRequest();
        $req->setCtpProtocol($ctpProtocol17->getInstance());
        $req->setAfsServiceDetailParam($param->getInstance());
        $resp2 = $this->ZeusApi->GetZeusApiData($req->getApiMethodName(), $req->getApiParas(), '1.0', 0, 1);
        $resp2 = json_decode($resp2, true);
        $responce = $resp2['jingdong_ctp_afs_servicenbill_getAfsServiceDetail_responce'] ?? [];
        return $responce['result'] ?? [];
    }

    /*
     * 回传客户发货信息API
     * @param getAfsServiceDetail $postBackLogisticsBillParam
     */
    public function postBackLogisticsBillParam(postBackLogisticsBillParam $postBackLogisticsBillParam)
    {
        $ctpProtocol17 = $this->getCtpProtocol17("postBackLogisticsBillParam");
        $param = new \LogisticsBillParam();
        $param->setAfsServiceId($postBackLogisticsBillParam->afsServiceId);
        $param->setPin("jd_461d06fc0cb6c");
        $param->setLogisticsCompany($postBackLogisticsBillParam->logisticsCompany);
        $param->setWaybillCode($postBackLogisticsBillParam->waybillCode);
        $param->setSendGoodsDate($postBackLogisticsBillParam->sendGoodsDate);
        $req = new \CtpAfsLogisticsPostBackLogisticsBillParamRequest();
        $req->setCtpProtocol($ctpProtocol17->getInstance());
        $req->setLogisticsBillParam($param->getInstance());
        $resp2 = $this->ZeusApi->GetZeusApiData($req->getApiMethodName(), $req->getApiParas(), '1.0', 0, 1);
        $resp2 = json_decode($resp2, true);
        $responce = $resp2['jingdong_ctp_afs_logistics_postBackLogisticsBillParam_responce'] ?? [];
        return $responce['result'] ?? [];
    }

    /*
     * 取消订单
     * @param cancelOrder $cancelOrder
     */
    public function cancelOrder(cancelOrder $cancelOrder)
    {
        $req = new \CtpOrderCancelOrderRequest;
        $orderid = $cancelOrder->orderId;

        $req->setAppKey($this->jdclent->appKey);
        $req->setCustomerId();
        $req->setChannelId();
        $req->setOrderId($orderid);
        $req->setTraceId('cancelorder' . time());
        $req->setPin('湖北高霍开发');
        $req->setCancelReasonCode(5);// 其他原因
        $req->setCancelReasonType(2); //用户取消
        $req->setCancelType(1);// 订单取消

        $resp = $this->ZeusApi->GetZeusApiData($req->getApiMethodName(), $req->getApiParas(), '1.0', 0, 1);
        $resp = json_decode($resp, true);
        Log::record("fun cancelorder: end");
        //  3：取消成功
        return $resp;
    }

    /*
     * 获取CtpProtocol17
     * @return \CtpProtocol17
     */
    protected function getCtpProtocol17($traceType = "")
    {
        $ctpProtocol17 = new \CtpProtocol17();
        $ctpProtocol17->setAppKey($this->jdclent->appKey);
        $ctpProtocol17->setCustomerId(381129369);
        $ctpProtocol17->setChannelId(25005217);
        $ctpProtocol17->setOpName('湖北高霍开发');
        $ctpProtocol17->setTraceId($traceType . time());
        return $ctpProtocol17;
    }
}