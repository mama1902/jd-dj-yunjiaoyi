<?php

namespace app\common\logic\jd\constant;

class enum
{

    //退换
    const ReturnsType = 10;

    //换货
    const HuanType = 20;


    //客户发货
    const ApproveResultSend = 33;

    //上门取件
    const ApproveResultHome = 31;

    //上门换新
    const ApproveResultHuan = 31;

    public static function getTextType($type)
    {
        if ($type == self::HuanType) {
            return "换货";
        }
        return "退货";
    }
}