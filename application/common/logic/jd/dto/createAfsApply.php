<?php

namespace app\common\logic\jd\dto;

class createAfsApply
{
    //售后申请问题描述文字
    public $applyReasonName;

    //售后申请原因ID
    public $applyReasonId;

    //渠道售后服务单申请单号
    public $channelAfsApplyId;

    //用户期望的售后服务类型 10：退货 20：换货
    public $afsType;

    //京东订单号
    public $orderId;

    //申请商品信息（京东商品编号skuId及skuId对应的数量信息）
    public $skuQuantity;

    //取件方式上门取件NORMAL_PICKWARE(4)大家电上门取件MAJOR_NORMAL_PICKWARE(8)客户发货CUSTOMER_SEND_WARE(40)
    public $pickWareType = 4;
}