<?php

namespace app\admin\model\cms;

use think\Model;

class OrderReturns extends Model
{
    // 表名
    protected $name = 'cms_order_returns';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
}
