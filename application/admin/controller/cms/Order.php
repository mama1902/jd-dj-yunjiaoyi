<?php

namespace app\admin\controller\cms;

use app\admin\model\cms\OrderReturns;
use app\common\controller\Backend;
use app\common\logic\jd\constant\enum;
use app\common\logic\jd\dto\createAfsApply;
use app\common\logic\jd\dto\getAfsServiceDetail;
use app\common\logic\jd\dto\getApplyReason;
use app\common\logic\jd\dto\getIsCanApplyInfo;
use app\common\logic\jd\dto\postBackLogisticsBillParam;
use app\common\logic\jd\JdZeus;
use fast\Random;
use think\Console;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\Db;
use think\Validate;

/**
 * 订单管理
 *
 * @icon fa fa-cny
 */
class Order extends Backend
{

    /**
     * Order模型对象
     * @var \app\admin\model\cms\Order
     */
    protected $model = null;
    protected $searchFields = 'id,orderid,out_orderid,archives_id,amount,waybillCode,status_y';
    protected $noNeedRight = ['edit2'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\cms\Order;
        $this->view->assign("statusList", $this->model->getStatusList());
    }

    /**
     * 查看
     */
    public function index()
    {
        $verder_list = \app\admin\model\Shopinfo::column('id,station_name');
        $merchant_list = \app\admin\model\Shopinfo::column('id,merchant_id');
        $merchant_name = \app\admin\model\Merchant::column('id,merchant_name');
        $articemodel = new \app\admin\model\cms\Archives;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            $this->relationSearch = true;
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->with(['user', 'archives'])
                ->where($where)
                ->order($sort, $order)
                ->count();
            $list = $this->model
                ->with(['user', 'archives'])
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            foreach ($list as &$item) {
                $item->user->visible(['id', 'username', 'nickname', 'avatar']);
                $item->archives->visible(['id', 'title', 'diyname']);
                $storename = $verder_list[$item['vender_id']];
                $merchant_id = $merchant_list[$item['vender_id']];

                $item['merchant_name'] = $merchant_name[$merchant_id];
                $item['store_name'] = $storename;


                $titlehtml = $logisticss = [];
                $title = json_decode($item['title'], true);
                $ii = 0;
                $size = count($title);
                foreach ($title as $titlei) {
                    $skuinfo = $articemodel->where(['sku' => $titlei['sku_id']])->find();
                    $ii++;
                    $titlehtml[] = ' sku:  ' . $titlei['sku_id'] . '  单价: ' . $titlei['sku_jd_price'] . ' ,原价: ' . $titlei['dprice'] . '  ' . $titlei['sku_name'] . '  *' . $titlei['sku_count'];
                    $logisticss[] = '';

                }
                $item['titlehtml'] = $titlehtml;
                $item['logisticss'] = $logisticss;

            }
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }

        return $this->view->fetch();
    }


    /**
     * 查看
     */
    public function indexk()
    {

        $merchant_list = \app\admin\model\Shopinfo::column('id,merchant_id');
        $merchant_name = \app\admin\model\Merchant::column('id,merchant_name');
        $articemodel = new \app\admin\model\cms\Archives;
        if ($this->auth->merchant_id) {
            $verder_list = \app\admin\model\Shopinfo::where('merchant_id', $this->auth->merchant_id)->column('id,station_name');
        } else {
            $verder_list = \app\admin\model\Shopinfo::column('id,station_name');
        }

        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            $this->relationSearch = true;
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $total = $this->model
                ->with(['user', 'archives'])
                ->where($where)
                ->where('order.vender_id', 'in', array_keys($verder_list))
                ->order($sort, $order)
                ->count();
            $list = $this->model
                ->with(['user', 'archives'])
                ->where($where)
                ->where('order.vender_id', 'in', array_keys($verder_list))
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            foreach ($list as &$item) {
                $item->user->visible(['id', 'username', 'nickname', 'avatar']);
                $item->archives->visible(['id', 'title', 'diyname']);
                $storename = $verder_list[$item['vender_id']];
                $merchant_id = $merchant_list[$item['vender_id']];

                $item['merchant_name'] = $merchant_name[$merchant_id];
                $item['store_name'] = $storename;


                $titlehtml = $logisticss = [];
                $title = json_decode($item['title'], true);
                $ii = 0;
                $size = count($title);
                foreach ($title as $titlei) {
                    $skuinfo = $articemodel->where(['sku' => $titlei['sku_id']])->find();
                    $ii++;
                    $titlehtml[] = ' sku:  ' . $titlei['sku_id'] . '  单价: ' . $titlei['sku_jd_price'] . ' ,原价: ' . $titlei['dprice'] . '  ' . $titlei['sku_name'] . '  *' . $titlei['sku_count'];
                    $logisticss[] = '';

                }
                $item['titlehtml'] = $titlehtml;
                $item['logisticss'] = $logisticss;

            }
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }

        return $this->view->fetch();
    }


    public function edit2($ids)
    {
        $order = \addons\cms\model\Order::where('id', $ids)
            ->order('id', 'desc')
            ->find();
        if (empty($order)) {
            $this->error(__('No Results were found'));
        }
        $arr = json_decode($order['logisticsName'], true);

        $redis = new \app\common\model\Redis;
        $cachekey = $order['out_orderid'] . "-orderlogic";

        if (empty($redis->get($cachekey))) {
            $out = Console::call('AdaRepair', ['orderlogic', '--money', $order['out_orderid']]);
        }
        $out = json_decode($redis->get($cachekey), true);
        foreach ($arr as $key => $item) {

            $arr[$key]['linfo'] = $out;

        }
        $order['logisticsName'] = $arr;

        $this->view->assign("item", $order);

        return $this->view->fetch();

    }


    public function returns($ids)
    {
        $jdZeus = new JdZeus();
        $order = \addons\cms\model\Order::where('id', $ids)
            ->order('id', 'desc')
            ->find();
        if (empty($order)) {
            $this->error(__('No Results were found'));
        }
        $orderReturnsModel = new OrderReturns();
        $orderId = $order['bre_child'] ? $order['bre_child'] : $order['out_orderid'];
        $goods_arr = json_decode($order['title'], true);
        if ($this->request->isPost()) {
            $param = $this->request->param();
            if (!$param['skuid']) {
                $this->error(__('请选择退换货商品'));
            }
            if ($param['returns_num'] <= 0) {
                $this->error(__('请选择退换货商品数量'));
            }
            if (!$param['apply_type']) {
                $this->error(__('请选择退换货类型'));
            }
            if (!$param['reason_id']) {
                $this->error(__('请选择退换货原因'));
            }
            if (!$param['apply_reason_name']) {
                $this->error(__('请填写退换描述'));
            }

            $afsType = $param['apply_type'];
            $skuid = $param['skuid'];
            $reason_id = $param['reason_id'];
            $reason_msg = $param['apply_reason_name'];
            $createAfsApply = new createAfsApply();
            $createAfsApply->orderId = $orderId;
            $createAfsApply->afsType = $afsType;
            $createAfsApply->applyReasonId = $reason_id;
            $createAfsApply->applyReasonName = $reason_msg;
            $createAfsApply->channelAfsApplyId = time() . rand(10000, 99999);
            $goodsNameMap = [];
            foreach ($goods_arr as $vv) {
                $goodsNameMap[$vv['sku_id']] = $vv['sku_name'];
            }
            $SkuQuantity = new \SkuQuantity();
            $SkuQuantity->setSkuName($goodsNameMap[$skuid] ?? "未知商品");
            $SkuQuantity->setSkuId($skuid);
            $SkuQuantity->setQuantity($param['returns_num']);
            $SkuQuantity->setAfsDetailType(10);
            $createAfsApply->skuQuantity = $SkuQuantity;
            $res = $jdZeus->createAfsApply($createAfsApply);
            if (intval($res['success']) == 1) {
                $orderReturnsModel->insert([
                    'order_id' => $createAfsApply->orderId,
                    'afs_type' => $createAfsApply->afsType,
                    'reason_id' => $createAfsApply->applyReasonId,
                    'reason_msg' => $createAfsApply->applyReasonName,
                    'apply_id' => $createAfsApply->channelAfsApplyId,
                    'afs_apply_id' => $res['data']['afsApplyId'],
                    'skuid' => $skuid,
                    'returns_num' => $param['returns_num'],
                    'createtime' => time(),
                    'updatetime' => time()
                ]);
                $this->success(__("退换货申请成功"));
            } else {
                $this->error(__($res['errMsg']));
            }
        }
        $isCanApply = 0;
        foreach ($goods_arr as $k => $v) {
            //云交易商品是否可以售后
            $getIsCanApplyInfo = new getIsCanApplyInfo();
            $getIsCanApplyInfo->orderId = $orderId;
            $getIsCanApplyInfo->skuId = $v['sku_id'];
            $res = $jdZeus->getIsCanApplyInfo($getIsCanApplyInfo);
            $goods_arr[$k]['canApply'] = $goods_arr[$k]['appliedNum'] = 0;
            $afsTypeName = [];
            $goods_arr[$k]['remark'] = "";
            $goods_arr[$k]['can_count'] = $v['sku_count'];
            if (intval($res['success']) == 1) {
                $goods_arr[$k]['canApply'] = $res['data']['canApply'];
                $goods_arr[$k]['appliedNum'] = $res['data']['appliedNum'];
                if (isset($res['data']['afsSupportedTypes'])) {
                    $goods_arr[$k]['afsTypeName'] = $res['data']['afsSupportedTypes'];
                }
                $goods_arr[$k]['can_count'] = $v['sku_count'] - $res['data']['appliedNum'];
                if (intval($res['data']['canApply']) == 1) {
                    $isCanApply = 1;
                } else {
                    $goods_arr[$k]['remark'] = $res['data']['cannotApplyTip'];
                }
            } else {
                $goods_arr[$k]['remark'] = $res['errMsg'];
            }
        }
//        echo "<pre>";
//        print_r($goods_arr);exit;
        $order['goods_arr'] = $goods_arr;
        $order['isCanApply'] = $isCanApply;
        //获取售后原因
        $reasonList = [];
        $this->view->assign("item", $order);
        $this->view->assign("reason_list", $reasonList);
        $this->view->assign("orderId", $orderId);
        $this->view->assign("ids", $ids);
        //查询售后情况
        $orderReturnLog = $orderReturnsModel->where("order_id", $orderId)->select();
        foreach ($orderReturnLog as &$item) {
            $item->text_type = enum::getTextType($item->afs_type);
            $item->approve_result = 0;
            if($item->afs_service_id) {
                $getAfsServiceDetail = new getAfsServiceDetail();
                $getAfsServiceDetail->afsServiceId = $item->afs_service_id;
                $res = $jdZeus->getAfsServiceDetail($getAfsServiceDetail);
                if ($res && intval($res['success']) == 1) {
                    $item->approve_result = $res['data']['approveResult'];
                }
            }
        }
        $this->view->assign("orderReturnLog", $orderReturnLog);
        return $this->view->fetch();

    }

    public function getreason()
    {
        if ($this->request->isAjax()) {
            $jdZeus = new JdZeus();
            $iscan = new getApplyReason();
            $iscan->orderId = $this->request->param("order_id");
            $iscan->skuId = $this->request->param("skuid");
            $iscan->afsType = $this->request->param("afsType");
            $ress = $jdZeus->getApplyReason($iscan);
            $reasonList = [];
            if (isset($ress['success']) && intval($ress['success']) == 1) {
                $reasonList = $ress['data'];
            }
            $this->success("查询成功", "", $reasonList);
        }
    }

    public function returns_send_logistics()
    {
        $ids = $this->request->param("id");
        $orderReturnsModel = new OrderReturns();
        $orderLogs = $orderReturnsModel->where("id",$ids)->find();
        if ($this->request->isPost()) {
            $this->token();
            $jdZeus = new JdZeus();
            $postBackLogisticsBillParam = new postBackLogisticsBillParam();
            $postBackLogisticsBillParam->afsServiceId = $orderLogs['afs_service_id'];
            $postBackLogisticsBillParam->sendGoodsDate = $this->request->param("send_goods_date");
            $postBackLogisticsBillParam->logisticsCompany = $this->request->param("company_name");
            $postBackLogisticsBillParam->waybillCode = $this->request->param("waybill_code");
            $ress = $jdZeus->postBackLogisticsBillParam($postBackLogisticsBillParam);
            if (isset($ress['success']) && intval($ress['success']) == 1) {
                $orderReturnsModel->where("id",$ids)->update([
                    'send_goods_date' => $postBackLogisticsBillParam->sendGoodsDate,
                    'logistics_company' => $postBackLogisticsBillParam->logisticsCompany,
                    'waybill_code' => $postBackLogisticsBillParam->waybillCode,
                ]);
                $this->success();
            }
            $this->error("填写售后单失败");
        }
        $this->view->assign("ids",$ids);
        return $this->view->fetch();
    }
}
