<?php

namespace app\common\logic\jd\dto;

class postBackLogisticsBillParam
{
    //京东售后服务id
    public $afsServiceId;

    //物流公司
    public $logisticsCompany = "京东物流";

    //运单号
    public $waybillCode;

    //发货时间
    public $sendGoodsDate;
}