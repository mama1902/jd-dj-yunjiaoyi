<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/23
 * Time: 12:35
 */

namespace app\admin\command;

use app\common\logic\jd\dto\createAfsApply;
use app\common\logic\jd\dto\getApplyReason;
use app\common\logic\jd\dto\getIsCanApplyInfo;
use app\common\logic\jd\JdZeus;
use CtpAfsOperateApplyGetIsCanApplyInfo\CanApplyInfoParam;
use think\Console;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\Db;
use think\Log;
use think\Env;

class Action extends Command
{

    /**
     * @var \JdClient
     */
    protected $jdclent = null;
    /**
     * @var \ZeusApi
     */
    protected $ZeusApi = null;
    /**
     * @var \JddClient
     */
    protected $jdd = null;

    /**
     * @var \app\admin\model\cms\Archives
     */
    protected $model = null;

    /**
     * @var array
     */
    protected $jddshop = [];

    /**
     * @var int
     */
    protected $merchant_id = 0;


    const CACHETIME = 78900;

    public static $RELOADTIME = 3600;


    /**
     * @var number
     */
    protected $shop_id = null;

    protected function configure()
    {
        $this->setName('AdaRepair')
            ->addArgument('type', Argument::OPTIONAL, '类型', '')
            ->addOption('money', null, Option::VALUE_REQUIRED, '123')
            ->addOption('mo', null, Option::VALUE_REQUIRED, '是否同步到家')
            ->setDescription('Command run Controller Action!');
    }

    protected function execute(Input $input, Output $output)
    {
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

        $type = $input->getArgument('type');
        if ($type) {
            if (is_callable(array($this, $type))) {
                call_user_func_array(array($this, $type), [$input, $output]);
            } else {
                $output->write('no callable  ' . $type);
            }

        }

    }

    //  php think AdaRepair Autotask --money=18053149010
    protected function Autotask(Input $input, Output $output)
    {
        $value = $input->getOption('money');
        $ob = new \addons\vip\controller\Autotask;
        $ob->index();
        die;

    }


    //  php think AdaRepair Autosynsprice --money=lxt
    protected function Autosynsprice(Input $input, Output $output)
    {
        $lock_filename = RUNTIME_PATH . '/log/lock.log';
        if (file_exists($lock_filename)) {
            echo "请稍后再试2" . date('Y-m-d H:i') . ".\r\n.";
            return false;
        }
        mylog("pic error", 1, 'lock');
        $this->Autosynspricechild();
        unlink($lock_filename);

    }


    protected function Autosynspricechild($scroll = '')
    {

        $req = new \CtpWareSkuGetSkuListRequest;
        $pol = new \CtpProtocol17();
        $redis = new \app\common\model\Redis;

        $pol->setAppKey($this->jdclent->appKey);
        $pol->setCustomerId();
        $pol->setChannelId();
        $pol->setOpName('采购');
        $pol->setTraceId('gaohuo_Autosynsprice' . time());

        if (empty($scroll)) {
            $redis->set("stata-allskus", 0);
            $redis->set("stata-npimg-allskus", 0);
        }

        $req->setCtpProtocol($pol->getInstance());
        $p = new \ApiSkuListParam17();
        $p->setOrderBy("modified:desc");
        if (!$scroll) {
            $p->setPageSize(100);
        }
        $p->setScrollId($scroll);
        $req->setApiSkuListParam($p->getInstance());

        $req->getApiParas();

        //$resp = $c->execute($req, $c->accessToken);
        $catemodel = new \app\admin\model\cms\Channel;


        // 到家请求
        $this->djdao();
        $req2 = new \verificationUpdateToken();


        $resp = $this->ZeusApi->GetZeusApiData($req->getApiMethodName(), $req->getApiParas(), '1.0', 0, 1);
        $resp = json_decode($resp, true);
        if (!empty($resp['jingdong_ctp_ware_sku_getSkuList_responce']['result']['data']['entries'])) {
            foreach ($resp['jingdong_ctp_ware_sku_getSkuList_responce']['result']['data']['entries'] as $item) {


                $redis->getRedis()->incr(Env::get('database.database')."stata-allskus");



                $this->model = new \app\admin\model\cms\Archives;

                if (mb_strlen($item['categoryName']) > 50) {
                    $item['categoryName'] = mb_substr($item['categoryName'], 0, 50);
                }

                try {
                    $cateid = $catemodel->where(['name' => $item['categoryName'], 'model_id' => 2])->value('id');
                    $catedata = ['name' => $item['categoryName'], 'model_id' => 2, 'parent_id' => 2, 'type' => 'list', 'channeltpl' => 'channel_product.html', 'listtpl' => 'list_product.html', 'showtpl' => 'show_product.html', 'diyname' => 'w' . $item['categoryId']];
                    if (!$cateid) {
                        $catemodel->save($catedata);
                        //$cateid = $catemodel->getLastInsID();
                        $cateid = $catemodel->where(['name' => $item['categoryName'], 'model_id' => 2])->value('id');
                    } else {
                        $catemodel->where(['id' => $cateid])->update($catedata);
                    }

                    if (empty($item['imgUrl'])) {
                        $redis->getRedis()->incr(Env::get('database.database')."stata-npimg-allskus");
                        continue;
                    }

                    if (mb_strlen($item['skuName']) > 150) {
                        $item['skuName'] = mb_substr($item['skuName'], 0, 150);
                    }

                    $skudata = ['sku' => $item['skuId'], 'title' => $item['skuName'], 'model_id' => 2, 'channel_id' => $cateid];
                    // 图片存储改为异步
                    $addtaskdata = [
                        'imgUrl' => $item['imgUrl'],
                        'images' => '',
                        'skuId' => $item['skuId'],
                        'id' => 0,
                        'skudata' => $skudata,
                    ];
                    $redis->QueueRedis(Env::get('database.database')."-downoadimages", json_encode($addtaskdata)  );


                    // 到家品牌推荐refursh
                    /*
                    if ($catedata = $redis->get(Env::get('database.database')."cttepinfo-" . urlencode($skudata['title']))) {
                    } else {
                        //查询类目和品牌
                        $req2->setApiPath("/pms/getSkuCateBrandBySkuName");
                        $req2->setOperrams([
                            'productName' => $skudata['title'],
                            'fields' => ['brand', 'category'],
                        ]);
                        $cateresp = $this->jdd->execute($req2, 1);
                        if (!empty($cateresp)) {
                            $redis->set(Env::get('database.database')."cttepinfo-" . urlencode($skudata['title']), $cateresp['data'], self::CACHETIME);
                        }
                    }
                    */

                    // echo $logid.'-'.$item['skuId']." try ..\r\n";
                } catch (\Exception $e) {
                    mylog("pic error", $item['skuId'] . '#' . $e->getMessage(), 'skusyn');
                }

            }
            // 游标
            if (!empty($resp['jingdong_ctp_ware_sku_getSkuList_responce']['result']['data']['scrollId'])) {
                $this->Autosynspricechild($resp['jingdong_ctp_ware_sku_getSkuList_responce']['result']['data']['scrollId']);
                return;
            }
        }

        echo date("Y-m-d H:i:s") . " cate list& sku list \r\n";

    }


    //  php think AdaRepair Syndskuimages --money=lxt
    protected function Syndskuimages(Input $input, Output $output)
    {
        ini_set('memory_limit','9256M');
        $redis = new \app\common\model\Redis;
        $req = new \CtpWareSkuGetSkuListRequest;
        $pol = new \CtpProtocol17();
        $pol->setAppKey($this->jdclent->appKey);
        $pol->setCustomerId();
        $pol->setChannelId();
        $pol->setOpName('采购');
        $pol->setTraceId('gaohuo_Autosynsprice' . time());

        $this->model = new \app\admin\model\cms\Archives;
        $detailp = new \SkuDetailParam16();
        $detailreq = new \CtpWareSkuGetSkuDetailRequest();
        $timeout = 1;

        while (true){
            $queueName = Env::get('database.database')."-downoadimages";
            if ($item = $redis->getQueueKey($queueName) ) {
                $item = json_decode($item, true);
                //echo print_r($item, true)."\r\n";

                if ( empty($item['skuId']) ) {
                    continue;
                }
                $logid = db('cms_archives')->where(['sku' => $item['skuId']])->value('id');
                $skudata = $item['skudata'];
                if (!$logid) {
                    $datetime = date("Y-m-d H:i:s");

                    db('cms_archives')->insert($skudata);
                    //$logid = $this->model->getLastInsID();
                    $logid = db('cms_archives')->where(['sku' => $item['skuId']])->value('id');

                } else {
                    db('cms_archives')->where(['id' => $logid])->update($skudata);
                }

                $item['id'] = $logid;

                $picfilename = basename($item['imgUrl']);
                $filename = ROOT_PATH . 'public/uploads/product/' . $picfilename;
                try {
                    if (!file_exists($filename)) {
                        GrabImage($item['imgUrl'], $filename);
                        resize_image($picfilename, $filename, 650, 650);
                    }
                } catch (\Exception $e) {
                    mylog("pic error", $item['skuId'] ?? '' . '#' . $e->getMessage(), 'skusyn');
                }
                $newpicurl = "http://111.229.37.112//uploads/product/" . $picfilename;
                // mylog("skusyn", $newpicurl . '-' . $item['skuId'] . " end ..", 'skusyn');

                $skudata = ['image' => $newpicurl, 'images' => $newpicurl];
                db('cms_archives')->where(['id' => $item['id']])->update($skudata);

                // sku 详情----------------------------------

                $detailreq->setCtpProtocol($pol->getInstance());
                $detailp->setSkuIdSet([$item['skuId']]);
                $detailp->setDetailAssemblyType(0);
                $detailreq->setSkuDetailParam($detailp->getInstance());

                $respdetail = $this->ZeusApi->GetZeusApiData($detailreq->getApiMethodName(), $detailreq->getApiParas(), '1.0', 0, 0);

                //存附表
                // $model = \app\admin\model\cms\Modelx::get(2);
                $model = ['table' => 'cms_addonproduct'];
                if ($model) {
                    $idd = $item['id'];
                    $re = db($model['table'])->where("id", $idd)->find();
                    if ($re) {
                        db($model['table'])->where("id", $idd)->update(['content' => $respdetail]);
                    } else {
                        db($model['table'])->insert(['content' => $respdetail, 'id' => $idd]);
                    }
                }
                $respdetail = null;
                // echo $item['id'].' - '.date("Y-m-d H:i:s")." downed image completed \r\n";
            }

            sleep(2);
        }

    }



    /**
     * 自动更新
     * php think AdaRepair auotosynspriceandstockall --money=1
     */
    protected function auotosynspriceandstockall(Input $input, Output $output)
    {

        $daojia = 1; // 是否同步到家， 默认同步
        $ids = ''; // 指定商品id更新， 默认全部
        $vender_id = 0; // 指定门店, 默认全部
        $redis = new \app\common\model\Redis;
        $vender = new \app\admin\model\Shopinfo;
        $page = $input->getOption('money');
        $verder_list = $vender->where('merchant_id', 'in', [2, 3, 4, 5])->limit(24)->page($page)->column('id,merchant_id');


        $lock_filename = RUNTIME_PATH . '/log/locksynstock.log';
        if (file_exists($lock_filename)) {
            // echo "初始化库存在进行，  请稍后再试".date('Y-m-d H:i').".\r\n.";
            //  return false;
        }

        $lock_filename = RUNTIME_PATH . '/log/stocklock.log';
        if (file_exists($lock_filename)) {
            // echo "stock请稍后再试---------------------------------------".date('Y-m-d H:i').".\r\n.";
            // return false;
        }
        mylog("pic error", 1, 'stocklock');
        echo "auotosynspriceandstockall start " . date('Y-m-d H:i') . ".\r\n.";


        // 库存更新  先找出各个门店需要更新的商品
        $this->model = new \app\admin\model\cms\Archives;
        $haved = [];

        foreach ($verder_list as $venderid => $merchant_id) {
            if (!empty($vender_id) && $venderid != $vender_id) {
                continue;
            }
            // 目前商家下  需要同步的 商品是一样的
            $kk = "daojia--" . $merchant_id;
            $listvals = $redis->getRedis()->hvals($kk);
            if (!empty($listvals)) {
                $ids = implode(',', $listvals);
                $size = count($listvals);
            } else {
                continue;
            }
            echo date("Y-m-d H:i:s ") . $venderid . '-' . $merchant_id . " --------------------------库存更新{$size} start \r\n";
            if (!empty($ids)) {
                $output = Console::call('AdaRepair', ['synsstockonlyqty', '--money', $daojia . '#' . $ids . '#' . $venderid]);
                if (empty($haved[$merchant_id])) {
                    $output = Console::call('AdaRepair', ['synsstockAndprice', '--money', '1#' . $ids . '#' . $venderid]);
                    $haved[$merchant_id] = 1;
                }
            }
            echo date("Y-m-d H:i:s ") . $venderid . '-' . $merchant_id . " --------------------------库存更新{$size}结束\r\n";
        }

        @unlink($lock_filename);
        echo "auotosynspriceandstockall {$page} end " . date('Y-m-d H:i') . ".\r\n.";

    }


    // 仅同步daojia
    protected function justsyntoDaojia(Input $input, Output $output)
    {
        $ids = explode('#', $input->getOption('money'));
        $mo = $ids[2] ?? '';
        $requiresyn = $ids[0] ?? '';
        $ids = $ids[1] ?? '';
        if ($mo && is_numeric($mo)) {
            $this->shop_id = $mo;
        }
        if (empty($this->shop_id)) {
            $this->shop_id = 0;
        }
        try {
            $this->daojiassynallsku(null, null, $ids);
        } catch (\Exception $e) {
            echo $e->getMessage() . $e->getTraceAsString();
        }

    }


    //  php think AdaRepair synsstockAndprice --money=1##0
    protected function synsstockAndprice(Input $input, Output $output)
    {
        try {
            $req = new \CtpWarePriceGetSkuPriceInfoListRequest;
            $pol = new \CtpProtocol17();
            $ids = explode('#', $input->getOption('money'));
            $mo = $ids[2] ?? '';
            $requiresyn = $ids[0] ?? '';
            $ids = $ids[1] ?? '';
            mylog('synsstockAndprice', "fun synsstockAndprice:" . $input->getOption('money') . ' start');
            if ($mo && is_numeric($mo)) {
                $this->shop_id = $mo;
            }
            if (empty($this->shop_id)) {
                $this->shop_id = 0;
            }

            $pol->setAppKey($this->jdclent->appKey);
            $pol->setCustomerId();
            $pol->setChannelId();
            $pol->setOpName('采购');
            $pol->setTraceId('synsstockAndprice' . time());

            $req->setCtpProtocol($pol->getInstance());
            $p = new \SkuPriceInfoParam14();
            $this->model = new \app\admin\model\cms\Archives;

            $limit = 40;
            for ($page = 1; $page <= 2000; $page++) {

                $result = $this->model->where('status', 'normal')
                    ->order('id', 'desc')
                    ->field('id,sku,price,stock,status,d_sta,is_daojia_update')
                    ->page($page)
                    ->limit($limit);
                if ($ids) {
                    $result->where('id', 'in', $ids);
                }
                $result = $result->select();

                if (empty($result)) {
                    break;
                } else {
                    $pricess = $stockss = $idarr = $dds = [];
                    $prices = array_column($result, 'price');
                    $stocks = array_column($result, 'stock');
                    $skus = array_column($result, 'sku');
                    $idss = array_column($result, 'id');
                    $dddata = array_column($result, 'is_daojia_update');
                    foreach ($skus as $key => $sku) {
                        $pricess[$sku] = $prices[$key];
                        $stockss[$sku] = $stocks[$key];
                        $idarr[$sku] = $idss[$key];
                        $dds[$sku] = $dddata[$key];
                    }
                    $p->setSkuIdSet($skus);
                    $req->setSkuPriceInfoParam($p->getInstance());
                    $resp = $this->ZeusApi->GetZeusApiData($req->getApiMethodName(), $req->getApiParas(), '1.0', 0, 1);
                    $resp = json_decode($resp, true);
                    if (!empty($resp['jingdong_ctp_ware_price_getSkuPriceInfoList_responce']['result']['data']['skuPriceList'])) {

                        foreach ($resp['jingdong_ctp_ware_price_getSkuPriceInfoList_responce']['result']['data']['skuPriceList'] as $item) {
                            $up = false;
                            if (empty($item['skuPrice'])) {
                                continue;// 无效sku
                            }
                            if ($pricess[$item['skuId']] != $item['skuPrice']) {
                                $this->model->where(['sku' => $item['skuId']])->update(['price' => number_format($item['skuPrice'], 2, '.', ''), 'updatetime' => time()]);
                                $up = true;
                            }
                            // 触发更新
                            $is_daojia_update = $dds[$item['skuId']] ? @json_decode($dds[$item['skuId']], true) : [];
                            if (empty($is_daojia_update[$this->shop_id])) {
                                $is_daojia_update[$this->shop_id] = [];
                            }

                            if ($requiresyn && (empty($is_daojia_update[$this->shop_id]['d_sta']) || $up)) {
                                // 同步商品数据取消
                                /// // $this->daojiassynallsku(null, null, $idarr[$item['skuId']]);
                            }

                            if ($this->shop_id && $up) {
                                $this->daojiagoodsPrice(['sku' => $item['skuId'], 'station_no' => '', 'price' => number_format($item['skuPrice'] * 100, 2, '.', '')]);
                            }


                        }
                    }

                }

            }
        } catch (\Exception $e) {
            echo $e->getTraceAsString() . '#' . $e->getMessage() . print_r($item ?? [], 1);
        }
        Log::record("fun synsstockAndprice: end");

    }

    //  php think AdaRepair childaddress  --money=
    private function childaddress($address = '')
    {
        $arr = [4744, 0, 0, 0];
        $addressarr = explode('-', $address);
        $req = new \CtpOrderGetChildAreaListRequest;
        $req->setAppKey($this->jdclent->appKey);
        $req->setCustomerId();
        $req->setChannelId();
        $req->setOpName('采购');
        $req->setTraceId('childaddress' . time());
        $req->setParentId($arr[0]);
        $resp = $this->ZeusApi->GetZeusApiData($req->getApiMethodName(), $req->getApiParas());
        $resp = json_decode($resp, true);
        if (!empty($resp['jingdong_ctp_order_getChildAreaList_responce']['result']['data'])) {

            foreach ($resp['jingdong_ctp_order_getChildAreaList_responce']['result']['data'] as $item) {
                if (strstr($addressarr[0], $item['name'])) {
                    $arr[1] = $item['id'];
                    break;
                }

            }
        }


        $req->setTraceId('childaddress' . time());
        $req->setParentId($arr[1]);
        $resp = $this->ZeusApi->GetZeusApiData($req->getApiMethodName(), $req->getApiParas());
        $resp = json_decode($resp, true);
        if (!empty($resp['jingdong_ctp_order_getChildAreaList_responce']['result']['data'])) {
            foreach ($resp['jingdong_ctp_order_getChildAreaList_responce']['result']['data'] as $item) {
                if (strstr($addressarr[1], $item['name'])) {
                    $arr[2] = $item['id'];
                    break;
                }

            }
        }

        $req->setTraceId('childaddress' . time());
        $req->setParentId($arr[2]);
        $resp = $this->ZeusApi->GetZeusApiData($req->getApiMethodName(), $req->getApiParas());
        $resp = json_decode($resp, true);
        if (!empty($resp['jingdong_ctp_order_getChildAreaList_responce']['result']['data'])) {
            foreach ($resp['jingdong_ctp_order_getChildAreaList_responce']['result']['data'] as $item) {
                if (strstr($addressarr[2], $item['name'])) {
                    $arr[3] = $item['id'];
                    break;
                }

            }
        }

        $req->setTraceId('childaddress' . time());
        $req->setParentId($arr[3]);
        $resp = $this->ZeusApi->GetZeusApiData($req->getApiMethodName(), $req->getApiParas());
        $resp = json_decode($resp, true);
        if (!empty($resp['jingdong_ctp_order_getChildAreaList_responce']['result']['data'])) {
            foreach ($resp['jingdong_ctp_order_getChildAreaList_responce']['result']['data'] as $item) {
                if (strstr($addressarr[3], $item['name'])) {
                    $arr[4] = $item['id'];
                    break;
                }

            }
        }

        return $arr;


    }


    //  php think AdaRepair orderlogic  --money=
    protected function orderlogic(Input $input, Output $output)
    {
        $redis = new \app\common\model\Redis;
        $orderid = $input->getOption('money');
        $cachekey = $orderid . "-orderlogic";


        $req = new \CtpOrderGetLogisticsRequest;
        $req->setAppKey($this->jdclent->appKey);
        $req->setCustomerId();
        $req->setChannelId();
        $req->setPin("开发");
        $req->setTraceId('orderlogic' . time());
        $req->setOrderId($orderid);

        $resp = $this->ZeusApi->GetZeusApiData($req->getApiMethodName(), $req->getApiParas(), '1.0', 0, 1);

        $redis->set($cachekey, $resp, 600);

        $resp = json_decode($resp, true);

        return $resp;


    }


    //  php think AdaRepair synsstock  --money=
    protected function synsstock(Input $input, Output $output)
    {

        $lock_filename = RUNTIME_PATH . '/log/locksynsstock.log';
        if (file_exists($lock_filename)) {
            echo "请稍后再试3" . date('Y-m-d H:i') . ".\r\n.";
            return false;
        }
        // mylog("pic error", 1, 'locksynsstock');

        $req = new \CtpWareStockQueryAreaStockStateRequest;
        $pol = new \CtpProtocol17();
        $ids = explode('#', $input->getOption('money'));
        $mo = $ids[2] ?? '';
        $requiresyn = $ids[0] ?? '';
        $ids = $ids[1] ?? '';
        $shopmodel = new \app\admin\model\Shopinfo;
        mylog('synsstock', "fun synsstock:" . $input->getOption('money') . ' start');
        if ($mo && is_numeric($mo)) {
            $this->shop_id = $mo;
        }

        $pol->setAppKey($this->jdclent->appKey);
        $pol->setCustomerId();
        $pol->setChannelId();
        $pol->setOpName('采购');
        $pol->setTraceId('synsstockAndprice' . time());

        $req->setCtpProtocol($pol->getInstance());

        $config = get_addon_config('cms');
        $arrstocks = [0 => '无货', 1 => '有货', 2 => '采购中'];

        $padreess = new \Address19();
        $address = $config['stoktdefault'];
        if (empty($this->shop_id)) {
            $this->shop_id = 0;
        } else {
            $shopinfo = $shopmodel->where(['id' => $this->shop_id])->find();
            if (!empty($shopinfo)) {
                $address = $shopinfo['station_address'];
            }
        }
//        $addressarr = $this->childaddress($address);
//        $padreess->setProvinceId($addressarr[0]);
//        $padreess->setCountyId($addressarr[1]);
//        $padreess->setCityId($addressarr[2]);
//        $padreess->setTownId($addressarr[3]);
        $padreess->setFullAddress(str_replace('-', '', $address));


        $pqtyrequest = new \SkuQuantity19();
        $pqtyrequest->setQuantity(1);


        $this->model = new \app\admin\model\cms\Archives;


        $limit = 200;
        for ($page = 1; $page <= 2000; $page++) {

            $result = $this->model->where('status', 'normal')
                ->order('id', 'desc')
                ->field('id,sku,price,stock,status,memo,d_sta,is_daojia_update')
                ->page($page)
                ->limit($limit);
            if ($ids) {
                $result->where('id', 'in', $ids);
            }

            $result = $result->select();

            if (empty($result)) {
                break;
            } else {
                $pricess = $stockss = $objsku = $idarr = $dds = [];
                $prices = array_column($result, 'price');
                $stocks = array_column($result, 'memo');
                $skus = array_column($result, 'sku');
                $idss = array_column($result, 'id');
                $dddata = array_column($result, 'is_daojia_update');
                foreach ($skus as $key => $sku) {
                    $pricess[$sku] = $prices[$key];
                    $stockss[$sku] = @json_decode($stocks[$key], true);
                    if (!is_array($stockss[$sku])) {
                        $stockss[$sku] = [];
                    }
                    if (empty($stockss[$sku][$this->shop_id])) {
                        $stockss[$sku][$this->shop_id] = '';
                    }
                    $idarr[$sku] = $idss[$key];
                    $dds[$sku] = $dddata[$key];

                    $pqtyrequest->setSkuId($sku);

                    $StockStateParam19 = new \StockStateParam19();
                    $StockStateParam19->setAddress($padreess);
                    $StockStateParam19->setSkuQuantityList([$pqtyrequest]);
                    $req->setStockStateParam($StockStateParam19->getInstance());

                    $resp = $this->ZeusApi->GetZeusApiData($req->getApiMethodName(), $req->getApiParas(), '1.0', 0, 1);
                    $resp = json_decode($resp, true);
                    if (!empty($resp['jingdong_ctp_ware_stock_queryAreaStockState_responce']['result']['stockStateList'])) {
                        foreach ($resp['jingdong_ctp_ware_stock_queryAreaStockState_responce']['result']['stockStateList'] as $item) {
                            $update = false;
                            if ($stockss[$sku][$this->shop_id] != $arrstocks[$item['areaStockState']]) {
                                $stockss[$sku][$this->shop_id] = $arrstocks[$item['areaStockState']];
                                mylog('synsstock', "jingdong_ctp_ware_stock_queryAreaStockState_responce:" . json_encode($stockss[$sku]) . ' start');
                                $this->model->where(['sku' => $item['skuQuantity']['skuId']])->update(['memo' => json_encode($stockss[$sku]), 'updatetime' => time()]);
                                // 触发更新
                                $update = true;
                            }
                            $sku = $item['skuQuantity']['skuId'];
                            $is_daojia_update = $dds[$sku] ? @json_decode($dds[$sku], true) : [];
                            if (empty($is_daojia_update[$this->shop_id])) {
                                $is_daojia_update[$this->shop_id] = [];
                            }
                            if ($requiresyn && ($update || empty($is_daojia_update[$this->shop_id]['d_sta']))) {
                                $this->daojiassynallsku(null, null, $idarr[$sku]);
                            }

                        }
                    }

                    // 其它几个地区也查下
                    continue;
                    $addresss = explode("\n", $config['stoktext']);
                    $addresinfo = [];
                    foreach ($addresss as $address) {
                        $padreess = new \Address19();
                        $addressarr = $this->childaddress($address);
                        $padreess->setProvinceId($addressarr[0]);
                        $padreess->setCountyId($addressarr[1]);
                        $padreess->setCityId($addressarr[2]);
                        $padreess->setTownId($addressarr[3]);
                        $padreess->setFullAddress(str_replace('-', '', $address));
                        $StockStateParam19->setAddress($padreess);
                        $StockStateParam19->setSkuQuantityList([$pqtyrequest]);
                        $req->setStockStateParam($StockStateParam19->getInstance());

                        $resp = $this->ZeusApi->GetZeusApiData($req->getApiMethodName(), $req->getApiParas());
                        $resp = json_decode($resp, true);
                        if (!empty($resp['jingdong_ctp_ware_stock_queryAreaStockState_responce']['result']['stockStateList'])) {
                            foreach ($resp['jingdong_ctp_ware_stock_queryAreaStockState_responce']['result']['stockStateList'] as $item) {
                                if (true || $stockss[$item['skuQuantity']['skuId']] != $item['skuQuantity']['quantity']) {
                                    $addresinfo[mb_substr($address, 0, 2)] = $arrstocks[$item['areaStockState']];
                                }
                            }
                        }
                    }
                    $this->model->where(['sku' => $sku])->update(['elsememo' => json_encode($addresinfo, JSON_UNESCAPED_UNICODE)]);

                }

            }

        }
        @unlink($lock_filename);
        Log::record("fun synsstock: end");

    }


    //  php think AdaRepair synsstockonlyqty  --money=
    protected function synsstockonlyqty(Input $input, Output $output)
    {
        $config = get_addon_config('cms');
        $ids = explode('#', $input->getOption('money'));
        $mo = $ids[2] ?? '';
        $requiresyn = $ids[0] ?? '';
        $ids = $ids[1] ?? '';
        $shopmodel = new \app\admin\model\Shopinfo;
        mylog('synsstockonlyqty', "fun synsstock:" . $input->getOption('money') . ' start');
        if ($mo && is_numeric($mo)) {
            $this->shop_id = $mo;
        }

        if (empty($this->shop_id) || empty($ids)) {
            // return false;
            $address = $config['stoktdefault'];
        } else {
            $shopinfo = $shopmodel->where(['id' => $this->shop_id])->find();
            $this->merchant_id = $shopinfo['merchant_id'] ?? 0;
            $address = $shopinfo['station_address'];
        }
        $redis = new \app\common\model\Redis;
        $this->model = new \app\admin\model\cms\Archives;
        $req = new \CtpWareStockQueryAreaStockStateRequest;
        $pol = new \CtpProtocol17();

        $pol->setAppKey($this->jdclent->appKey);
        $pol->setCustomerId(381);
        $pol->setChannelId(250);
        $pol->setOpName('采购');
        $pol->setTraceId('synsstockAndprice' . time());

        $req->setCtpProtocol($pol->getInstance());

        $config = get_addon_config('cms');
        $arrstocks = [0 => '无货', 1 => '有货', 2 => '采购中'];
        $padreess = new \Address19();

        $padreess->setFullAddress(str_replace('-', '', $address));
        $pqtyrequest = new \SkuQuantity19();
        $pqtyrequest->setQuantity(1);

        $limit = 200;
        for ($page = 1; $page <= 2000; $page++) {

            $result = $this->model->where('status', 'normal')
                ->order('id', 'desc')
                ->field('id,sku,price,stock,status,memo,d_sta,is_daojia_update')
                ->page($page)
                ->limit($limit);
            if ($ids) {
                $result->where('id', 'in', $ids);
            }

            $result = $result->select();

            if (empty($result)) {
                break;
            } else {
                $pricess = $stockss = $objsku = $idarr = $dds = [];
                $prices = array_column($result, 'price');
                $stocks = array_column($result, 'memo');
                $skus = array_column($result, 'sku');
                $idss = array_column($result, 'id');
                $dddata = array_column($result, 'is_daojia_update');
                foreach ($skus as $key => $sku) {
                    $pricess[$sku] = $prices[$key];
                    $stockss[$sku] = @json_decode($stocks[$key], true);
                    if (!is_array($stockss[$sku])) {
                        $stockss[$sku] = [];
                    }
                    if (empty($stockss[$sku][$this->shop_id])) {
                        $stockss[$sku][$this->shop_id] = '';
                    }
                    $idarr[$sku] = $idss[$key];
                    $dds[$sku] = $dddata[$key];

                    $pqtyrequest->setSkuId($sku);

                    $StockStateParam19 = new \StockStateParam19();
                    $StockStateParam19->setAddress($padreess);
                    $StockStateParam19->setSkuQuantityList([$pqtyrequest]);
                    $req->setStockStateParam($StockStateParam19->getInstance());

                    $resp = $this->ZeusApi->GetZeusApiData($req->getApiMethodName(), $req->getApiParas(), '1.0', 0, 1);
                    $resp = json_decode($resp, true);
                    if (!empty($resp['jingdong_ctp_ware_stock_queryAreaStockState_responce']['result']['stockStateList'])) {
                        foreach ($resp['jingdong_ctp_ware_stock_queryAreaStockState_responce']['result']['stockStateList'] as $item) {
                            $update = 0;
                            $sku = $item['skuQuantity']['skuId'];
                            $is_daojia_update = $dds[$sku] ? @json_decode($dds[$sku], true) : [];
                            if (empty($is_daojia_update[$this->shop_id])) {
                                $is_daojia_update[$this->shop_id] = [];
                            }
                            if ($stockss[$sku][$this->shop_id] != $arrstocks[$item['areaStockState']]) {
                                $stockss[$sku][$this->shop_id] = $arrstocks[$item['areaStockState']];
                                mylog('synsstock', "jingdong_ctp_ware_stock_queryAreaStockState_responce:" . json_encode($stockss[$sku]) . ' start');
                                $is_daojia_update[$this->shop_id]['updatetime'] = time();// 时间
                                $this->model->where(['sku' => $item['skuQuantity']['skuId']])->update(['memo' => json_encode($stockss[$sku]), 'is_daojia_update' => json_encode($is_daojia_update)]);
                                // 触发更新
                                $update = 1;
                            }
                            $update = 1;
                            $kk2 = "daojia--sku" . $this->merchant_id;
                            if (!($daojiasku = $redis->getRedis()->hGet($kk2, $sku))) {
                                $daojiasku = $is_daojia_update[$this->merchant_id]['daojia_sku_id'] ?? '';
                            }
                            // echo date("Y-m-d H:i:s ").$this->merchant_id .'-'.$this->shop_id. '-#'. ($is_daojia_update[$this->merchant_id]['d_sta'] ?? '') .'#'.$daojiasku. " --{$sku}-------{$update}-----开始库存同步到家 \r\n";
                            if ($requiresyn && ($update && !empty($is_daojia_update[$this->merchant_id]['d_sta']))) {

                                if (!empty($daojiasku)) {
                                    $stock = 20;
                                    if ($arrstocks[$item['areaStockState']] != '有货') {
                                        $stock = 0;
                                    }

                                    $res = $this->daojiagoodsstock(['sku' => $daojiasku, 'station_no' => $shopinfo['station_no'], 'qty' => $stock]);
                                    $state = 2;
                                    if (empty($res)) {
                                        continue;
                                    }
                                    if ($res['state'] == 1) {
                                        $state = 1;
                                    }
                                    Db::name("cms_stock_cron")
                                        ->where("station_no", $shopinfo['station_no'])
                                        ->where("sku_id", $daojiasku)
                                        ->update([
                                            'state' => $state,
                                            'msg' => $res['msg'],
                                            'updatetime' => time()
                                        ]);

                                }


                            }

                        }
                    }


                }

            }

        }
        Log::record("fun synsstockonlyqty: end");

    }


    /**  同步到云交易 成单接口
     * php think AdaRepair createorder  --money=1252
     **/
    protected function createorder($params = [])
    {
        $req = new \CtpOrderSubmitOrderRequest;
        $pol = new \CtpProtocol17();
        $config = get_addon_config('cms');

        mylog("createorder", json_encode($params), 'createorder');
        $redis = new \app\common\model\Redis;
        $lockkey = 'syncreateorder-' . $params['order_id'];
        if (!$redis->setLock($lockkey)) {
            echo "清稍后再试\r\n";
            return false;
        }
        try {

            $this->model = new \app\admin\model\cms\Archives;
            $shopmodel = new \app\admin\model\Shopinfo;
            $orderobj = new \addons\cms\model\Order;
            $shopinfo = $shopmodel->where(['station_no' => $params['station_no'] ?? ''])->value('id');
            if (empty($shopinfo)) {
                throw new \Exception("门店信息未找到:");
            }
            $params['vender_id'] = $shopinfo;

            $orderinfo = $orderobj->where(['orderid' => $params['order_id'] ?? ''])->value('id');
            if (!empty($orderinfo)) {
                throw new \Exception("订单已经创建过了:");
            }


            $pol->setAppKey($this->jdclent->appKey);
            $pol->setCustomerId();
            $pol->setChannelId();
            $pol->setOpName('开发');
            $pol->setTraceId('createorder' . time());

            $req->setProtocol($pol->getInstance());
            $padreess = new \Address0();
            $address = $params['buyer_full_address'];

            $Province = mb_substr($address, 0, 2);
            $cityname = $params['buyer_city_name'];
            $towncide = $params['buyer_country_name'];

            $addressarr = $this->childaddress($Province . '-' . $cityname . '-' . $towncide . '-' . $address);
            $padreess->setProvinceId($addressarr[0]);
            $padreess->setCountyId($addressarr[1]);
            $padreess->setCityId($addressarr[2]);
            $padreess->setTownId($addressarr[3]);
            $padreess->setFullAddress(str_replace('-', '', $address));

            $revier = new \Receiver0();
            $revier->setReceiverName($params['buyer_full_name']);
            if (strstr($params['buyer_mobile'], ',')) {
                $buyer_mobile = explode(',', $params['buyer_mobile']);
                $params['buyer_mobile'] = $buyer_mobile[0];
            }
            $revier->setReceiverMobile($params['buyer_mobile']);
            $revier->setReceiverEmail($params['buyer_mobile'] . "@jd.com");
            $revier->setZipCode("100001");
            $params['archives_id'] = [];
            $productlist = [];
            $subtotal = 0;

            foreach ($params['product'] as &$productitem) {

                $skuinfo = $this->model->where(['sku' => $productitem['sku_id']])->find();
                if (empty($skuinfo)) {
                    throw new \Exception("商品未找到:". $productitem['sku_id'] . ',');
                }
                $skuinfo['price'] = number_format($skuinfo['price'], 2, '.', '');
                $params['archives_id'][] = $skuinfo['id'];
                $pqtyrequest = new \Product0();
                $skuitem = new \MainSku0();
                $skuitem->setSkuId($productitem['sku_id']);
                $skuitem->setQuantity($productitem['sku_count']);
                $skuitem->setSkuPrice($skuinfo['price']);
                $skuitem->setSkuName($productitem['sku_name']);
                $productitem['dprice'] = $skuinfo['price'];
                $subtotal = bcadd($skuinfo['price'] * $productitem['sku_count'], $subtotal, 2);

                $pqtyrequest->setMainSku($skuitem);
                $productlist[] = $pqtyrequest;
            }


            $Param0 = new \Param0();
            $Param0->setChannelOrderId("GH" . date("YmdHis"));
            $Param0->setPin("湖北高霍开发");
            $Param0->setAutoCancelTime(8000000);
            $Param0->setAddress($padreess);
            $Param0->setProductList($productlist);
            $Param0->setOrderFee($subtotal);
            // 运费计算
            $pol2 = new \CtpProtocol17();
            $req2 = new \CtpOrderGetFreightFeeRequest;
            $params2 = new \ApiFreightFeeParam();
            $pol2->setAppKey($this->jdclent->appKey);
            $pol2->setCustomerId();
            $pol2->setChannelId();
            $pol2->setOpName('湖北高霍开发');
            $pol2->setTraceId('yunfei' . time());
            $req2->setProtocol($pol2->getInstance());

            $params2->setPin("湖北高霍开发");
            $params2->setAddress($padreess);
            $params2->setPaymentType(2);
            $params2->setOrderFee($subtotal);
            $productlist = [];
            foreach ($params['product'] as $productitemtmp) {
                $skuitem = new \Sku10();
                $skuitem->setSkuId($productitemtmp['sku_id']);
                $skuitem->setQuantity($productitemtmp['sku_count']);
                $skuitem->setSkuPrice($productitemtmp['dprice']);
                $skuitem->setSkuName($productitemtmp['sku_name']);

                $productlist[] = $skuitem;
            }
            $params2->setSkuList($productlist);
            $req2->setApiFreightFeeParam($params2->getInstance());
            $resp2 = $this->ZeusApi->GetZeusApiData($req2->getApiMethodName(), $req2->getApiParas(), '1.0', 0, 1);
            $resp2 = json_decode($resp2, true);
            $Param0->setFreightFee($resp2['jingdong_ctp_order_getFreightFee_responce']['result']['data']['freightFee'] ?? 0);  // 运费  $params['order_freight_money']

            $Param0->setReceiver($revier);
            $Param0->setPaymentType(2);
            $Param0->setShipmentType(4);
            $Param0->setChannelOrderSource("OTHER");
            $Param0->setSendGoods(1);

            if (!empty($params['order_invoice'])) {
                $invioce = new \Invoice0();
                if (in_array($params['order_invoice']['invoice_type'], [0, 1])) {  // 0：个人、1：企业普票、2：企业专票

                    $invioce->setInvoiceType(3);// 电子票
                    $invioceex = new \ElectronicInvoice0();
                    $invioceex->setSelectedInvoiceTitle($params['order_invoice']['invoice_type'] == 1 ? 5 : 4);//  4：个人 5：公司
                    $invioceex->setElectCompanyName($params['order_invoice']['invoice_itle']);
                    $invioceex->setElectCode($params['order_invoice']['invoice_duty_no']);
                    $invioceex->setInvoiceConsigneeEmail($params['order_invoice']['invoice_mail']);
                    $invioceex->setInvoiceConsigneePhone($params['order_invoice']['invoice_tel_no']);

                    $invioce->setElectronicInvoice($invioceex);
                }

                $Param0->setInvoice($invioce);
            } else {
                $invioce = new \Invoice0();
                $invioce->setInvoiceType(3);// 电子票
                $invioceex = new \ElectronicInvoice0();
                $invioceex->setSelectedInvoiceTitle(4);
                $invioceex->setInvoiceConsigneeEmail('587565@qq.com');
                $invioceex->setInvoiceConsigneePhone('18550377888');
                $invioce->setElectronicInvoice($invioceex);
                $Param0->setInvoice($invioce);

            }
            $Param0->setUserIp("127.0.0.1");
            // $Param0->setDiscountFee($params['order_discount_money']);
            $req->setParam($Param0->getInstance());
            $resp = $this->ZeusApi->GetZeusApiData($req->getApiMethodName(), $req->getApiParas(), '1.0', 0, 1);
            $resp = json_decode($resp, true);
            if (empty($resp) || empty($resp['jingdong_ctp_order_submitOrder_responce']) || empty($resp['jingdong_ctp_order_submitOrder_responce']['result']['data'])) {
                throw new \Exception("订单生成失败:" . $resp['jingdong_ctp_order_submitOrder_responce']['result']['errMsg'] ?? '');
            }
            $params['out_orderid'] = $resp['jingdong_ctp_order_submitOrder_responce']['result']['data']['orderId'] ?? '';
            $params['out_orderid2'] = $resp['jingdong_ctp_order_submitOrder_responce']['result']['data']['channelOrderId'] ?? '';
            $params['subtotal'] = $subtotal;
            $response = \addons\cms\library\Order::submit2($params, $config['defaultpaytype']);
            $redis->delLock($lockkey);
            return ['status' => 200, 'msg' => '成功'];
        } catch (\Exception $e) {
            $redis->delLock($lockkey);
            mylog("createorder", $params['order_id'] . " createorder error:" . $e->getMessage() . '@' . $e->getFile() . '#' . $e->getLine(), 'createorder');
            return ['status' => 500, 'msg' => $e->getMessage() . $e->getLine()];
        }
        mylog("createorder", "fun createorder: end", 'createorder');
    }


    //  php think AdaRepair cancelorder  --money=1252
    protected function cancelorder(Input $input, Output $output)
    {
        $req = new \CtpOrderCancelOrderRequest;
        $orderid = $input->getOption('money');
        Log::record("fun cancelorder: " . $orderid);
        $this->model = new \app\admin\model\cms\Archives;
        if (empty($orderid)) {
            throw new \Exception("订单未找到:");
        }

        $req->setAppKey($this->jdclent->appKey);
        $req->setCustomerId();
        $req->setChannelId();
        $req->setOrderId($orderid);
        $req->setTraceId('cancelorder' . time());
        $req->setPin('高霍开发');
        $req->setCancelReasonCode(5);// 其他原因
        $req->setCancelReasonType(2); //用户取消
        $req->setCancelType(1);// 订单取消

        $resp = $this->ZeusApi->GetZeusApiData($req->getApiMethodName(), $req->getApiParas(), '1.0', 0, 1);
        $resp = json_decode($resp, true);

        Log::record("fun cancelorder: end");

        //  3：取消成功


        return $resp;
    }


    /**
     * 同步所有商品和分类
     * php think AdaRepair daojiassynallsku  --money=
     * @ids  指定商品id进行同步
     */
    protected function daojiassynallsku($input, $output, $ids = '')
    {
        $config = get_addon_config('cms');
        $redis = new \app\common\model\Redis;

        if ($input && is_numeric($input)) {
            $this->shop_id = $input;
        }
        if (empty($this->shop_id)) {
            //throw new \Exception("门店信息未找到:");
        }
        if ($shopInfo = $redis->get("shopinfo-" . $this->shop_id)) {
            $shopInfo = json_decode($shopInfo, true);
        } else {
            $shopInfo = Db::name("cms_shopinfo")
                ->where('id', $this->shop_id)
                ->find();
            $redis->set("shopinfo-" . $this->shop_id, json_encode($shopInfo), self::CACHETIME);
        }
        $merchantId = $shopInfo['merchant_id'] ?? 0;
        $this->merchant_id = $merchantId;
        $this->djdao();
        if ($shoplist = $redis->get("shoplist-" . $merchantId)) {
            $shoplist = json_decode($shoplist, true);
        } else {
            $shoplist = Db::name("cms_shopinfo")
                ->where('merchant_id', $merchantId)
                ->select();
            $redis->set("shoplist-" . $merchantId, json_encode($shoplist), self::CACHETIME);
        }
        $bili = $config['priceratio'];
        $req = new \verificationUpdateToken();
        mylog("daojiassynallsku", "fun :" . $ids . " start", 'daojia');
        $this->model = new \app\admin\model\cms\Archives;
        if (($brandss = $redis->get("brands-" . $merchantId))) {
            $brandss = json_decode($brandss, true);
        } else {
            $page = 1;
            $brandss = [];
            while ($brandssi = $this->querybrandinfo($page)) {
                $brandss = array_merge($brandss, $brandssi);
                $page++;
            }

            if (!empty($brandss)) {
                $redis->set("brands-" . $merchantId, json_encode($brandss), self::CACHETIME);
            }
        }
        mylog("daojiassynallsku", "------------brands :" . var_export($brandss, 1), 'daojia');

        $result = $this->model::alias('t')
            ->join('cms_channel ta', 'ta.id = t.channel_id', 'left')
            ->join('cms_channel pta', 'ta.parent_id = pta.id', 'left')
            ->join('j_cms_addonproduct p', 'p.id = t.id', 'left')
            ->where('t.status', 'normal')
            //->where('t.is_daojia_update',1)
            ->order('t.id', 'desc')
            ->field('t.*,ta.name,ta.jd_cate_id,p.content,pta.jd_cate_id as parent_jd_cate_id')
            ->limit(10000);
        if ($ids) {
            $result->where('t.id', 'in', $ids);
        }
        $result = $result->select();
        if ($result) {
            foreach ($result as $item) {

                // 缓存判断，时间内不要重推daojia
                if (($uptime = $redis->getRedis()->hGet("nosku--" . $merchantId, $item['sku'])) && ((time() - $uptime) < self::$RELOADTIME)) {
                    //continue;
                }
                // 缓存判断，时间内不要重推daojia
                if (($uptime = $redis->getRedis()->hGet("sku--" . $merchantId, $item['sku'])) && ((time() - $uptime) < self::$RELOADTIME)) {
                    // continue;
                }
                // 库存
                $memo = json_decode($item['memo'], true);
                if (!is_array($memo)) {
                    mylog("daojiassynallsku", "memo error : 查询库存异常" . $item['sku'], 'daojia');
                    continue;
                }
                $cur_memo = $memo[$this->shop_id] ?? ($memo[0] ?? '');
                if (empty($cur_memo)) {
                    continue;
                }
                $stock = 20;
                if ($cur_memo != '有货') {
                    $stock = 0;
                }


                if (false && ($cates = $redis->get("querycategory-" . $merchantId))) {
                    $cates = json_decode($cates, true);
                } else {
                    $cates = $this->querycategory();
                    if (!empty($cates)) {
                        $redis->set("querycategory-" . $merchantId, json_encode($cates), self::CACHETIME);
                    } else {
                        $cates = $redis->get("querycategory-" . $merchantId);
                        $cates = json_decode($cates, true);
                    }
                }
                mylog("daojiassynallsku", "querycategory :" . var_export($cates, 1), 'daojia');
                $catename = str_replace('/', '', $item['name']);
                if ($cates && isset($cates[$catename]) && $cates[$catename]) {
                    $cateid = intval($cates[$catename]);
                } else {
                    // 添加分类
                    $req->setApiPath("/pms/addShopCategory");
                    $req->setOperrams([
                        'pid' => intval($item['parent_jd_cate_id']),
                        'shopCategoryName' => $catename,
                    ]);
                    $resp = $this->jdd->execute($req, 1);
                    if (empty($resp)) {
                        continue;
                    }
                    $catedata = json_decode($resp['data'], true);
                    if ($resp['code'] == 0 && !empty($catedata['result']['id'])) {
                        $cateid = $catedata['result']['id'];
                        $cates[$merchantId] = $cateid;
                        Db::name('cms_channel')
                            ->where('id', $item['channel_id'])
                            ->update([
                                'jd_cate_id' => json_encode($cates)
                            ]);
                    } else {
                        mylog("sku", $item['sku'] . "detail 添加分类错误原因" . json_encode($resp, JSON_UNESCAPED_UNICODE) . ", 跳过", 'daojia');
                    }
                }

                $req = new \verificationUpdateToken();
                $this->model = new \app\admin\model\cms\Archives;
                if (false && ($catedata = $redis->get($merchantId . "cttepinfo-" . urlencode($item['title'])))) {
                    $catedata = json_decode($catedata, true);
                } else {
                    //查询类目和品牌
                    $req->setApiPath("/pms/getSkuCateBrandBySkuName");
                    $req->setOperrams([
                        'productName' => $item['title'],
                        'fields' => ['brand', 'category'],
                    ]);
                    $cateresp = $this->jdd->execute($req, 1);
                    if (empty($cateresp)) {
                        continue;
                    }
                    $catedata = json_decode($cateresp['data'], true);
                    if (!empty($catedata)) {
                        $redis->set($merchantId . "cttepinfo-" . urlencode($item['title']), $cateresp['data'], self::CACHETIME);
                    }
                }
                $brandId = 0;
                $categoryId = 0;// 算出来的推荐分类可能为0
                if (!empty($cateresp) && $cateresp['code'] == 0 && isset($catedata['result']['brandId']) && $catedata['result']['brandId']) {
                    $brandId = $catedata['result']['brandId'];
                    $categoryId = $catedata['result']['categoryId'];
                }

                if (empty($brandId)) {
                    foreach ($brandss as $keyi => $idi) {
                        if ($keyi && mb_strpos($item['title'], $keyi) !== false) {
                            $brandId = $idi;
                            break;
                        }
                    }
                    mylog("sku", $item['sku'] . "  brandId 信息未提供,计算之后为 " . $brandId . var_export($brandss, 1), 'daojia');
                    if (!$brandId) {
                        continue;
                    }
                }
                if (empty($categoryId)) {
                    /*foreach ($cates as $keyi => $idi) {
                        if ($keyi && mb_strpos($item['title'], $keyi) !== false) {
                            $categoryId = $idi;
                            break;
                        }
                    }*/
                    mylog("sku", $item['sku'] . "  categoryId 信息未提供,计算之后为 " . $categoryId, 'daojia');
                    if (!$categoryId) {
                        continue;
                    }
                }

                $content = json_decode($item['content'], true);
                if (empty($content) || empty($content['jingdong_ctp_ware_sku_getSkuDetail_responce'])) {
                    mylog("sku", $item['sku'] . "detail 信息未提供, 跳过", 'daojia');
                    continue;
                }
                $jdinfo = $content['jingdong_ctp_ware_sku_getSkuDetail_responce']['result']['data'] ?? '';
                if (empty($jdinfo)) {
                    mylog("sku", $item['sku'] . "detail 信息未提供, 跳过--", 'daojia');
                    continue;
                }

                $skuBaseInfo = $jdinfo[0]['skuBaseInfo'];
                $skuBigFieldInfo = $jdinfo[0]['skuBigFieldInfo'];
                //产品图
                $imageInfos = $jdinfo[0]['imageInfos'];
                $images = [];
                if (!empty($imageInfos)) {
                    foreach ($imageInfos as $info) {
                        if (count($images) >= 6) {
                            continue;//不能超过6张
                        }
                        $newimage = $this->createNewImage($info['path'], $item['sku']);
                        if ($newimage) {
                            $images[] = $newimage;
                        }
                    }
                }
                //产品图
                $newContent = "";
                if (isset($skuBigFieldInfo['pcCssContent'])) {
                    $content = $skuBigFieldInfo['pcCssContent'];
                    //提取里面的图片
                    $reg = "/background-image:url\([\s\S]*?\)/";
                    preg_match_all($reg, $content, $matches);

                    if (isset($matches[0]) && $matches[0]) {
                        foreach ($matches[0] as $img) {
                            $newImage = str_replace('background-image:url(', '', $img);
                            $newImage = str_replace(')', '', $newImage);
                            $newContent .= '<img src="' . $newImage . '" >';
                        }
                    }
                }
                $upcCode = explode(";", $skuBaseInfo['upcCode']);
                //提前内容图片
                $param = [
                    'traceId' => 'ghaddSku' . time(),
                    'outSkuId' => $item['sku'],
                    'skuName' => mb_substr($item['title'], 0, 50),
                    'skuPrice' => floor(floatval($item['price'] * $bili) * 100),
                    'shopCategories' => $cateid ?? 0,
                    'categoryId' => $categoryId ?? 0,
                    'brandId' => $brandId ?? 0,
                    'weight' => $skuBaseInfo['weight'],
                    'images' => $images,
                    'fixedStatus' => $item['status'] == 'normal' ? 1 : 2,
                    'isSale' => $item['status'] == 'normal' ? true : false,
                    'returnLabel' => "2", //支持7天无理由退货(一次性包装破损不支持)
                    'upc' => $upcCode[0] ?? '',
                    'productDesc' => $newContent,
                ];

                $is_daojia_update = $item['is_daojia_update'] ? @json_decode($item['is_daojia_update'], true) : [];
                if (empty($is_daojia_update[$this->merchant_id])) {
                    $is_daojia_update[$this->merchant_id] = [];
                }
                //错误的upc  默认一个
                if ((strpos($skuBaseInfo['upcCode'], '0000') !== false) || (($param['upc'] == $item['sku']) && (isset($is_daojia_update[$this->merchant_id]['failmsg']) && mb_strpos($is_daojia_update[$this->merchant_id]['failmsg'], "商品条码信息不能为空") === false))) {
                    $param['upc'] = "";
                }
                if (isset($is_daojia_update[$this->merchant_id]['failmsg']) && mb_strpos($is_daojia_update[$this->merchant_id]['failmsg'], "商品条码信息不正确") === false) {
                    if ($upcCode) {
                        $count1 = count($upcCode) - 1;
                        $rand = mt_rand(0, $count1);
                        $param['upc'] = $upcCode[$rand];
                    }
                }

                if (empty($param['categoryId']) || (isset($is_daojia_update[$this->merchant_id]['failmsg']) && mb_strpos($is_daojia_update[$this->merchant_id]['failmsg'], "无理由退货key") === false)) {
                    unset($param['returnLabel']);
                }
                if (empty($is_daojia_update[$this->merchant_id]['d_sta'])) {
                    $req->setApiPath("/pms/addSku");

                    $req->setOperrams($param);
                    if (floatval($item['price']) == 0) {
                        continue;
                    }
                    $resp = $this->jdd->execute($req, 1);
                    if (empty($resp)) {
                        continue;
                    }
                    $data = json_decode($resp['data'], true);
                    if (!empty($data['result']['resultCode']) && (in_array($data['result']['resultCode'], ['1100001']) || strpos($data['result']['failedDetail'], 'skuId已存在') !== false)) {
                        $daojia_sku_id = $data['result']['skuId'];
                        $is_daojia_update[$this->merchant_id] = ['d_sta' => 1, 'updatetime' => time(), 'daojia_sku_id' => $daojia_sku_id];
                        $redis->getRedis()->hSet("sku--" . $merchantId, $item['sku'], time());
                        $this->model->where(['sku' => $item['sku']])->update(['is_daojia_update' => json_encode($is_daojia_update, JSON_UNESCAPED_UNICODE), 'daojiasku' => $daojia_sku_id]);
                        if ($stock > 0 && $daojia_sku_id) { //加入库存同步计划任务
                            $insertAll = [];
                            foreach ($shoplist as $shop) {

                                $init_firstqty_sku = Db::name("cms_stock_cron")->where('station_no', $shop['station_no'])->where('sku_id', $daojia_sku_id)->find();
                                if (empty($init_firstqty_sku)) {
                                    $insertAll[] = [
                                        'sku_id' => $daojia_sku_id,
                                        'station_no' => $shop['station_no'],
                                        'qty' => $stock,
                                        'createtime' => time(),
                                        'merchant_id' => $merchantId
                                    ];
                                }

                            }
                            if (!empty($insertAll)) {
                                Db::name("cms_stock_cron")->insertAll($insertAll);
                            }
                        }
                    } else {
                        $failmsg = $data['result']['failedDetail'] ?? '';
                        $is_daojia_update[$this->merchant_id] = ['d_sta' => 0, 'updatetime' => time(), 'failmsg' => $data['result']['failedDetail'] ?? ""];
                        $this->model->where(['sku' => $item['sku']])->update(['is_daojia_update' => json_encode($is_daojia_update, JSON_UNESCAPED_UNICODE)]);
                        $redis->getRedis()->hSet("nosku--" . $merchantId, $item['sku'], time());
                        if (mb_strpos($failmsg, "类目ID不能为空") !== false) {
                            unset($cates[$merchantId]);
                            Db::name('cms_channel')
                                ->where('id', $item['channel_id'])
                                ->update([
                                    'jd_cate_id' => json_encode($cates)
                                ]);
                        }
                    }
                } else {
                    unset($param['images'], $param['productDesc']);
                    $req->setApiPath("/pms/updateSku");
                    $req->setOperrams($param);
                    if (floatval($item['price']) == 0) {
                        continue;
                    }
                    $resp = $this->jdd->execute($req, 1);
                    if (empty($resp)) {
                        continue;
                    }
                    $data = json_decode($resp['data'], true);
                    if ($resp['code'] == 0 && !empty($data['result']['resultCode']) && (in_array($data['result']['resultCode'], ['1100001']))) {
                        $daojia_sku_id = $data['result']['skuId'];

                        $redis->getRedis()->hSet("sku--" . $merchantId, $item['sku'], time());

                        $is_daojia_update[$this->merchant_id] = ['d_sta' => 1, 'updatetime' => time(), 'daojia_sku_id' => $daojia_sku_id];
                        $this->model->where(['sku' => $item['sku']])->update(['is_daojia_update' => json_encode($is_daojia_update, JSON_UNESCAPED_UNICODE), 'daojiasku' => $daojia_sku_id]);
                    } else {
                        $redis->getRedis()->hSet("nosku--" . $merchantId, $item['sku'], time());
                        $is_daojia_update[$this->merchant_id] = ['d_sta' => 0, 'updatetime' => time(), 'failmsg' => $data['result']['failedDetail'] ?? ""];
                        $this->model->where(['sku' => $item['sku']])->update(['is_daojia_update' => json_encode($is_daojia_update, JSON_UNESCAPED_UNICODE)]);
                    }
                }
                mylog("daojiassynallsku", "fun :" . $item['sku'] . '#' . json_encode($is_daojia_update, JSON_UNESCAPED_UNICODE) . " end", 'daojia');
                $this->addLog([
                    'request' => $param,
                    'response' => $resp
                ]);

            } // end for
        }
    }

    /**
     * 查询分类信息
     * php think AdaRepair querycategory  --money=
     *
     */
    protected function querycategory()
    {
        $this->djdao();
        $req = new \verificationUpdateToken();

        $this->model = new \app\admin\model\cms\Archives;
        //首先查询类目信息
        $req->setApiPath("/pms/queryCategoriesByOrgCode");
        $req->setOperrams([
            'fields' => "ID,PID,SHOP_CATEGORY_NAME,SORT",
        ]);
        $resp = $this->jdd->execute($req, 1);
        if (empty($resp)) {
            return [];
        }
        if ($resp['code'] != 0) {
            return [];
        }
        $data = json_decode($resp['data'], true);
        if ($data['code'] != 0) {
            return [];
        }
        $cates = [];
        if (!empty($data['result']) && is_array($data['result'])) {
            foreach ($data['result'] as $rs) {
                $cates[$rs['shopCategoryName']] = $rs['id'];
            }
        }
        return $cates;
    }

    /**
     * 查询品牌信息
     * php think AdaRepair querycategory  --money=
     *
     */
    protected function querybrandinfo($page = 1, $pagesize = 50)
    {
        $this->djdao();
        $req = new \verificationUpdateToken();

        $this->model = new \app\admin\model\cms\Archives;
        //首先查询类目信息
        $req->setApiPath("/pms/queryPageBrandInfo");
        $req->setOperrams([
            'fields' => "BRAND_ID,BRAND_NAME,BRAND_STATUS,BRAND_ZH_NAME,BRAND_EN_NAME",
            'pageNo' => $page,
            'pageSize' => $pagesize,
        ]);
        $resp = $this->jdd->execute($req, 1);
        if (empty($resp)) {
            return [];
        }
        if ($resp['code'] != 0) {
            return [];
        }
        $data = json_decode($resp['data'], true);
        if ($data['code'] != 0) {
            return [];
        }
        $cates = [];
        if (!empty($data['result']['result'])) {
            foreach ($data['result']['result'] as $rs) {
                $cates[$rs['brandZhName']] = $rs['id'];// brandZhName
            }
        }
        return $cates;
    }


    protected function addLog($data)
    {
        file_put_contents('syngoods.txt', json_encode($data, JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND);
    }


    /**
     * 到家图片上传反馈
     * php think AdaRepair daojiassynallskuimages  --money=
     *
     */
    protected function daojiassynallskuimages(Input $input, Output $output)
    {

        $ids = '';
        $this->djdao();
        $req = new \verificationUpdateToken();

        $this->model = new \app\admin\model\cms\Archives;
        //首先查询类目信息
        $req->setApiPath("/order/queryListBySkuIds");
        $req->setOperrams([
            'skuIds' => "2124638740",
        ]);
        $resp = $this->jdd->execute($req);

    }

    //  php think AdaRepair djdao  --money=
    private function djdao()
    {
        if (empty($this->merchant_id)) {
            $this->merchant_id = 2;
        }

        if (isset($this->jddshop[$this->merchant_id]) && $this->jddshop[$this->merchant_id]) {
            $this->jdd = $this->jddshop[$this->merchant_id];
        } else {
            $lotusHome = APP_PATH . 'common/library/jddaojia/' . DIRECTORY_SEPARATOR;
            include_once($lotusHome . "jdSdk.php");


            $c = new \JddClient;
            $apijdd = new \DJApi;
            $c->connectUrl = "https://openapi.jddj.com/djapi";//平台的接口地址
            $merchant = Db::name("merchant")
                ->where('id', $this->merchant_id)
                ->where('merchant_state', 1)
                ->field("*")
                ->find();
            if ($merchant) {
                $c->appkey = $merchant['app_key'];//应用的app_key
                $c->appsecret = $merchant['app_secret'];//应用的app_secret
                $c->token = $merchant['token'];//应用的token
            } else {
                $c->appkey = \DJApi::$appKey;//应用的app_key
                $c->appsecret = \DJApi::$appScret;//应用的app_secret
                $c->token = $apijdd->refreshAccessToken();//应用的token
            }
            $this->jdd = $c;
            $this->jddshop[$this->merchant_id] = $c;
        }
    }

    /**
     * 获取到家门店编码列表接口
     * php think AdaRepair daojiastorevendorid  --money=
     *
     */
    protected function daojiastorevendorid(Input $input, Output $output)
    {
        $merchantList = Db::name("merchant")->where('merchant_state', 1)->select();
        foreach ($merchantList as $merchant) {
            $ids = '';
            $this->merchant_id = $merchant['id'];
            $this->djdao();
            $req = new \verificationUpdateToken();

            //首先查询类目信息
            $req->setApiPath("/store/getStationsByVenderId");
            $req->setOperrams([
                'test' => 1
            ]);
            $resp = $this->jdd->execute($req);
            if ($resp['code'] != 0) {
                return false;
            }
            $data = json_decode($resp['data'], true);
            if ($data['code'] != 0) {
                return false;
            }
            foreach ($data['result'] as $no) {
                $this->getstoreinfobystationno(null, null, $no);
            }
        }
    }

    /**
     * 获取到家门店编码列表接口
     * php think AdaRepair getstoreinfobystationno  --money=
     *
     */
    protected function getstoreinfobystationno($input, $output, $storeNo = "12858973")
    {

        $ids = '';
        $this->djdao();
        $req = new \verificationUpdateToken();

        //首先查询类目信息
        $req->setApiPath("/storeapi/getStoreInfoByStationNo");
        $req->setOperrams([
            'StoreNo' => $storeNo
        ]);
        $resp = $this->jdd->execute($req);
        if ($resp['code'] != 0) {
            return false;
        }
        $data = json_decode($resp['data'], true);
        if ($data['code'] != 0) {
            return false;
        }
        $exist = Db::name("cms_shopinfo")->where("station_no", $data['result']['stationNo'])->find();
        $param = [
            'vender_id' => $data['result']['venderId'],
            'vender_name' => $data['result']['venderName'],
            'station_name' => $data['result']['stationName'],
            'station_no' => $data['result']['stationNo'],
            'station_address' => $data['result']['stationAddress'],
            'json_data' => json_encode($data['result'], JSON_UNESCAPED_UNICODE),
            'updatetime' => time(),
            'merchant_id' => $this->merchant_id,
        ];
        if ($exist) {
            Db::name("cms_shopinfo")->where('id', $exist['id'])->update($param);
        } else {
            $param['createtime'] = time();
            Db::name("cms_shopinfo")->insert($param);
        }
    }

    /**
     * 订单列表查询接口
     * php think AdaRepair orderquery  --money=
     *
     */
    protected function orderquery(
        $input,
        $output,
        $orderId = "",
        $beginTime = "00-00-00 00:00:00",
        $endTime = "00-00-00 00:00:00",
        $pageNo = 1
    )
    {
        if ($endTime == "00-00-00 00:00:00") {
            $beginTime = date('Y-m-d H:i:s', strtotime("-5 hour"));
            $endTime = date('Y-m-d H:i:s');
        }
        $this->djdao();
        $req = new \verificationUpdateToken();

        //首先查询类目信息
        $req->setApiPath("/order/es/query");
        $param = [
            'pageSize' => 50,
            'pageNo' => $pageNo,
            'orderPurchaseTime_begin' => $beginTime,
            'orderPurchaseTime_end' => $endTime
        ];
        if ($orderId) {
            $param['orderId'] = $orderId;
        }
        $req->setOperrams($param);
        $resp = $this->jdd->execute($req, 1);
        if (intval($resp['code']) != 0) {
            return [];
        }
        $encryptData = $this->decrypt($resp['encryptData']);
        if ($encryptData['code'] != 0 && $encryptData['success'] != 1) {
            return [];
        }
        $result = json_decode($encryptData['result'], true);
        return $result;
    }

    /**
     * 同步订单到临时表
     * php think AdaRepair synordertemp  --money=
     *
     */
    protected function synordertemp(Input $input, Output $output, $beginTime = "00-00-00 00:00:00", $endTime = "00-00-00 00:00:00")
    {
        if ($endTime == "00-00-00 00:00:00") {
            $beginTime = date('Y-m-d H:i:s', strtotime("-2 hour"));
            $endTime = date('Y-m-d H:i:s');
        }
        $merchantList = Db::name("merchant")->where('merchant_state', 1)->select();
        foreach ($merchantList as $item) {
            $this->merchant_id = $item['id'];
            $this->djdao();
            for ($page = 1; $page <= 1; $page++) {
                $result = $this->orderquery(null, null, "", $beginTime, $endTime, $page);
                if (!$result || empty($result['resultList'])) {
                    var_dump($result);
                    echo "temp order 无数据\r\n";
                    continue;
                }
                if ($page > $result['totalPage']) {
                    echo "temp order 无数据\r\n";
                    continue;
                }
                $insertAll = [];
                echo $this->merchant_id . ' has  orders ' . count($result['resultList']) . " " . date('Y-m-d H:i') . ".\r\n.";
                foreach ($result['resultList'] as $order) {
                    echo $order['orderId'] . " try insert temp \r\n";
                    $exist = Db::name("cms_order_temp")->where("order_id", $order['orderId'])->find();
                    if ($exist) {
                        continue;
                    }
                    $insertAll[] = [
                        'order_id' => $order['orderId'],
                        'order_status' => $order['orderStatus'],
                        'order_time' => $order['orderPurchaseTime'],
                        'order_json' => json_encode($result['resultList'], JSON_UNESCAPED_UNICODE),
                        'createtime' => time(),
                        'updatetime' => time(),
                        'merchant_id' => $item['id']
                    ];
                }
                if ($insertAll) {
                    Db::name("cms_order_temp")->insertAll($insertAll);
                }
            }
        }

    }

    /**
     * 接单接口
     * php think AdaRepair orderacceptoperate  --money=
     *
     */
    protected function orderacceptoperate($input, $output, $orderId = "2314336432000074")
    {

        $ids = '';
        $this->djdao();
        $req = new \verificationUpdateToken();

        //首先查询类目信息
        $req->setApiPath("/ocs/orderAcceptOperate");
        $req->setOperrams([
            'orderId' => $orderId,
            'isAgreed' => true,
            'operator' => "高霍"
        ]);
        $resp = $this->jdd->execute($req);
        if (intval($resp['code']) != 0) {
            return false;
        }
        return true;
    }

    /**
     * 订单妥投接口
     * @param $orderId 订单号
     * @param $operTime 操作时间  2016-01-07 09:40:00
     *
     * php think AdaRepair deliveryendorder  --money=
     *
     */
    protected function deliveryendorder($input, $output = null, $orderId = '', $operTime = '')
    {

        $ids = explode('#', $input->getOption('money'));
        $vender_id = $ids[1] ?? '';
        $orderId = $ids[0] ?? '';
        $operTime = date("Y-m-d H:i:s");

        $shopInfo = Db::name("cms_shopinfo")
            ->where('id', $vender_id)
            ->find();

        $merchantId = $shopInfo['merchant_id'] ?? 0;
        $this->merchant_id = $merchantId;
        $this->djdao();
        $req = new \verificationUpdateToken();

        //首先查询类目信息
        $req->setApiPath("/ocs/deliveryEndOrder");
        $req->setOperrams([
            'orderId' => $orderId,
            'operTime' => $operTime,
            'operPin' => "高霍"
        ]);
        $resp = $this->jdd->execute($req, 1);
        if (intval($resp['code']) != 0) {
            return false;
        }
        return true;
    }

    /**
     * 订单发货
     * php think AdaRepair orderserllerdelivery  --money=
     *
     */
    protected function orderserllerdelivery($input, $output, $orderId = "2314336432000074")
    {

        $ids = '';
        $this->djdao();
        $req = new \verificationUpdateToken();

        //首先查询类目信息
        $req->setApiPath("/bm/open/api/order/OrderSerllerDelivery");
        $req->setOperrams([
            'orderId' => $orderId,
            'operator' => "高霍"
        ]);
        $resp = $this->jdd->execute($req);
        if (intval($resp['code']) != 0) {
            return false;
        }
        return true;
    }


    /**
     * 计划任务同步库存
     * php think AdaRepair synstock  --money=
     *
     */
    protected function synstock($input, $output)
    {

        $d_merchant_id = $input->getOption('money');
        $lock_filename = RUNTIME_PATH . '/log/locksynstock.log';
        if (file_exists($lock_filename)) {
            echo "请稍后再试1" . date('Y-m-d H:i') . ".\r\n.";
            return false;
        }
        mylog("pic error", 1, 'locksynstock');

        $limit = 50;
        $verder_list = \app\admin\model\Shopinfo::column('station_no,merchant_id');
        foreach ($verder_list as $station_no => $merchant_id) {
            if ($d_merchant_id && $merchant_id != $d_merchant_id) {
                continue;
            }
            $pagess = 0;
            for ($page = 1; $page <= 2000; $page++) {
                $list = Db::name("cms_stock_cron")
                    ->where("state", 0)
                    ->where("merchant_id", $merchant_id)
                    ->where("station_no", $station_no)
                    ->limit($limit)
                    ->page($page)
                    ->select();
                if ($list) {
                    $skuStockList = [];
                    foreach ($list as $item) {
                        $res = $this->daojiagoodsstock([
                            'sku' => $item['sku_id'],
                            'station_no' => $item['station_no'],
                            'qty' => $item['qty']
                        ], $item['merchant_id']);
                        $state = 2;
                        if (empty($res)) {
                            echo $item['sku_id'] . " station_no:" . $item['station_no'] . " :$state continue \r\n";
                            continue;
                        }

                        if ($res['state'] == 1) {
                            $state = 1;
                        }
                        Db::name("cms_stock_cron")
                            ->where("id", $item['id'])
                            ->update([
                                'state' => $state,
                                'msg' => $res['msg'],
                                'updatetime' => time()
                            ]);
                        echo $item['sku_id'] . " station_no:" . $item['station_no'] . " :$state end \r\n";
                    }

                } else {
                    break;
                }

            }

        }
        @unlink($lock_filename);

    }

    /**
     * 库存同步到家
     * php think AdaRepair daojiagoodsstock  --money=
     *
     */
    protected function daojiagoodsstock($param, $merchantId = 0)
    {

        if ($merchantId <= 0) {
            $shopInfo = Db::name("cms_shopinfo")
                ->where('id', $this->shop_id)
                ->find();
            $merchantId = $shopInfo['merchant_id'] ?? 0;
            $this->merchant_id = $merchantId;
        }


        $result = ['state' => 0, 'msg' => ""];
        //同步库存
        $this->djdao();
        $req = new \verificationUpdateToken();

        $this->model = new \app\admin\model\cms\Archives;
        //查询类目和品牌
        $req->setApiPath("/update/currentQty");
        $req->setOperrams([
            'skuId' => $param['sku'],
            'stationNo' => $param['station_no'],
            'currentQty' => $param['qty'] //默认5个库存
        ]);
        $qtyresp = $this->jdd->execute($req, 1);
        if (empty($qtyresp)) {
            return false;
        }
        //同步库存
        if ($qtyresp['code'] != 0) {
            $result['msg'] = $qtyresp['msg'];
            return $result;
        }
        $qtydata = json_decode($qtyresp['data'], true);
        if (intval($qtydata['retCode']) != 0) {
            $result['msg'] = $qtydata['retMsg'];
            return $result;
        }
        $result['state'] = 1;
        return $result;
    }

    /**
     * 价格同步到家
     * php think AdaRepair daojiagoodsstock  --money=
     *
     */
    protected function daojiagoodsPrice($param, $merchantId = 0)
    {
        $redis = new \app\common\model\Redis;
        if ($merchantId <= 0) {
            $shopInfo = Db::name("cms_shopinfo")
                ->where('id', $this->shop_id)
                ->find();
            $merchantId = $shopInfo['merchant_id'] ?? 0;
            $this->merchant_id = $merchantId;
        }

        $kk2 = "daojia--sku" . $this->merchant_id;
        if (!($daojiasku = $redis->getRedis()->hGet($kk2, $param['sku']))) {
            return false;
        }
        $param['sku'] = $daojiasku;

        $result = ['state' => 0, 'msg' => ""];
        //同步库存
        $this->djdao();
        $req = new \verificationUpdateToken();

        $this->model = new \app\admin\model\cms\Archives;
        //查询类目和品牌
        $req->setApiPath("/price/updateStationPrice");
        $req->setOperrams([
            'skuId' => $param['sku'],
            'stationNo' => $shopInfo['station_no'],
            'price' => $param['price'] // 单位分
        ]);
        $qtyresp = $this->jdd->execute($req, 1);
        if (empty($qtyresp)) {
            return false;
        }
        //同步库存
        if ($qtyresp['code'] != 0) {
            $result['msg'] = $qtyresp['msg'];
            return $result;
        }
        $qtydata = json_decode($qtyresp['data'], true);
        if (intval($qtydata['retCode']) != 0) {
            $result['msg'] = $qtydata['retMsg'];
            return $result;
        }
        $result['state'] = 1;
        return $result;
    }

    /**
     * 发货同步到家
     * php think AdaRepair daojiaorderdelivery  --money=
     *
     */
    protected function daojiaorderdelivery($input)
    {
        $ids = explode('#', $input->getOption('money'));
        $orderid = $ids[0] ?? '';
        $shipsliver = $ids[1] ?? '';
        $shop_id = $ids[2] ?? '';
        $merchantId = 0;
        $this->shop_id = $shop_id;

        if ($merchantId <= 0) {
            $shopInfo = Db::name("cms_shopinfo")
                ->where('id', $this->shop_id)
                ->find();
            $merchantId = $shopInfo['merchant_id'] ?? 0;
            $this->merchant_id = $merchantId;
        }

        $result = ['state' => 0, 'msg' => ""];
        //同步库存
        $this->djdao();
        $req = new \verificationUpdateToken();

        $this->model = new \app\admin\model\cms\Archives;
        //查询类目和品牌
        $req->setApiPath("/ocs/bandThirdDeliverNoApiPlatform");
        $req->setOperrams([
            'orderId' => "$orderid",
            'deliveryBillNo' => "$shipsliver",
            'thirdDeliveryCompany' => "11" // （1：顺丰速运；2：EMS；3：百世汇通；4：申通快递；5：中通快递；6：圆通速递；7：国通快递；8：韵达速递：9：天天快递；10：全峰快递；11：京东物流；12：快捷快递；13：优速物流；14：宅急送；15：闪送；16：达达快送；17：其它）
        ]);
        $qtyresp = $this->jdd->execute($req, 1);
        if (empty($qtyresp)) {
            return false;
        }
        //同步库存
        if ($qtyresp['code'] != 0) {
            $result['msg'] = $qtyresp['msg'];
            return $result;
        }
        $qtydata = json_decode($qtyresp['data'], true);
        if (isset($qtydata['retCode'])  &&      intval($qtydata['retCode']) != 0) {
            $result['msg'] = $qtydata['retMsg'];
            return $result;
        }
        $result['state'] = 1;
        return $result;
    }

    /**
     * 批量更新库存
     * php think AdaRepair batchupdatecurrentqtys  --money=
     *  sku_stock_list  sku_id,stock_qty
     */
    protected function batchupdatecurrentqtys($param)
    {

        $result = ['state' => 0, 'msg' => ""];
        //同步库存
        $this->djdao();
        $req = new \verificationUpdateToken();

        $this->model = new \app\admin\model\cms\Archives;
        $skuStockList = [];
//        foreach ($param['sku_stock_list'] as $list){
//            $skuStockList[] = [
//                'outSkuId' => $list['sku_id'],
//                'stockQty' => $list['stock_qty']
//            ];
//        }
        //查询类目和品牌
        $req->setApiPath("/stock/batchUpdateCurrentQtys");
        $req->setOperrams([
            'userPin' => "test",
            'stationNo' => $param['station_no'],
            'skuStockList' => $param['sku_stock_list'] //多个传数组
        ]);
        $qtyresp = $this->jdd->execute($req, 1);
        mylog("batchupdatecurrentqtys", "同步库存skuid :" . $param['station_no'] . " 失败原因:" . json_encode($qtyresp, JSON_UNESCAPED_UNICODE) . " start", 'daojia');
        //同步库存
        if ($qtyresp['code'] != 0) {
            $result['msg'] = $qtyresp['msg'];
            return $result;
        }
        $qtydata = json_decode($qtyresp['data'], true);
        if (intval($qtydata['retCode']) != 0) {
            $result['msg'] = $qtydata['retMsg'];
            return $result;
        }
        $result['state'] = 1;
        return $result;
    }

    /**
     * 异步生成高霍订单  1个小时后没有删除订单就同步修改订单
     * php think AdaRepair syncreateorder  --money=
     *
     */
    protected function syncreateorder($input, $output)
    {
        $startdate = date("Y-m-d H:i:s", strtotime("-5 minute"));

        $config = get_addon_config('cms');
        $orderpush = $config['orderpush'];
        if (empty($orderpush)) {
            return false;
        }
        $merchantList = Db::name("merchant")->where('merchant_state', 1)->column('id');

        $orderlist = Db::name("cms_order_temp")
            ->whereIn("merchant_id", $merchantList)
            ->whereIn("order_status", [41000])
            ->where('is_create', 0)
            ->whereRaw("order_time < '$startdate'")
            ->select();
        echo Db::name("cms_order_temp")->getLastSql() . "\r\n";
        if (empty($orderlist)) {
            return;
        }
        echo 'temp  orders ' . count($orderlist) . " " . date('Y-m-d H:i') . ".\r\n.";
        $beginTime = date('Y-m-d H:i:s', strtotime("-2 day"));
        $endTime = date('Y-m-d H:i:s');
        foreach ($orderlist as $order) {
            $this->merchant_id = $order['merchant_id'];
            echo $order['order_id'] . " try  syncreateorder \r\n";
            $jdorder = $this->orderquery(null, null, $order['order_id'], $beginTime, $endTime);
            if (empty($jdorder) || $jdorder['totalCount'] <= 0) {
                mylog("orderquery", json_encode($jdorder, JSON_UNESCAPED_UNICODE));
                echo $order['order_id'] . " 到家query error \r\n";
                continue;
            }
            $jdorderinfo = $jdorder['resultList'][0];
            if (in_array($jdorderinfo['orderStatus'], [20010, 20020, 20030, 20040, 31000])) {//取消订单
                Db::name("cms_order_temp")
                    ->where("id", $order['id'])
                    ->update([
                        'order_status' => $jdorderinfo['orderStatus'],
                        'msg' => '订单用户前端已取消'
                    ]);
                continue;
            }

            //print_r($jdorderinfo);
            //同步订单到高霍
            $orderparam = [
                'order_id' => $jdorderinfo['orderId'], //京东订单编号
                'order_status' => '33040', //订单状态  配送中
                'order_create_time' => $jdorderinfo['orderStartTime'], //订单创建时间
                'order_pay_time' => $jdorderinfo['orderPurchaseTime'], //订单付款时间
                'order_start_delivery_time' => $jdorderinfo['orderPreStartDeliveryTime'], //预计送达开始时间（最快开始配送时间）
                'order_end_delivery_time' => $jdorderinfo['orderPreEndDeliveryTime'], //预计送达结束时间（最晚配送完成时间）
                'buyer_full_name' => $jdorderinfo['buyerFullName'], //收货人名称
                'buyer_full_address' => $jdorderinfo['buyerFullAddress'], //收货人地址
                'buyer_mobile' => $jdorderinfo['buyerMobile'], //收货人手机号
                'order_total_money' => $this->price_format($jdorderinfo['orderTotalMoney']), //订单商品销售价总金额
                'order_discount_money' => $this->price_format($jdorderinfo['orderDiscountMoney']), //订单级别优惠商品金额
                'order_freight_money' => $this->price_format($jdorderinfo['orderFreightMoney']), //运费金额
                'order_buyer_payable_money' => $this->price_format($jdorderinfo['orderBuyerPayableMoney']), //用户应付金额
                'buyer_city_name' => $jdorderinfo['buyerCityName'], //收货人市名称
                'buyer_country_name' => $jdorderinfo['buyerCountryName'], //收货人县(区)名称
                'orderer_name' => $jdorderinfo['ordererName'] ?? "", //订购人姓名
                'orderer_mobile' => $jdorderinfo['ordererMobile'] ?? "", //订购人电话
                'station_no' => $jdorderinfo['deliveryStationNo'],//到家门店编码
            ];
            $products = [];
            // 白名单处理
            $skip = new \app\admin\model\Skip;
            $shopmodel = new \app\admin\model\Shopinfo;
            $orderobj = new \addons\cms\model\Order;
            $merchant_id = $shopmodel->where(['station_no' => $orderparam['station_no'] ?? ''])->value('merchant_id');
            $skipskus = $skip->where(['merchant_id' => $merchant_id])->column('sku');
            if (empty($skipskus)) {
                $skipskus = [];
            }
            $skipitems = [];


            foreach ($jdorderinfo['product'] as $product) {
                $item = [
                    'sku_id' => isset($product['skuIdIsv']) ? $product['skuIdIsv'] : '',//到家商品编码
                    'sku_name' => $product['skuName'],//商品的名称
                    'sku_jd_price' => $this->price_format($product['skuJdPrice']),//到家商品销售价
                    'sku_purchase_price' => isset($product['skuPurchasePrice']) ? $this->price_format($product['skuPurchasePrice']) : 0.00,//采购价(仅京东天选有)
                    'sku_count' => $product['skuCount'],//下单数量
                    'upc_code' => isset($product['upcCode']) ? $product['upcCode'] : '',//商品upc码
                    'sku_store_price' => isset($product['skuStorePrice']) ? $this->price_format($product['skuStorePrice']) : 0.00,//到家商品门店价
                    'sku_cost_price' => isset($product['skuCostPrice']) ? $this->price_format($product['skuCostPrice']) : 0.00,//到家商品成本价
                    'sku_costume_property' => $product['skuCostumeProperty'] ?? "",//商品规格，多规格之间用英文分号;分隔
                ];
                if (in_array($item['sku_id'], $skipskus)) {
                    $skipitems[] = $item;
                } else {
                    $products[] = $item;
                }
            }
            if (!empty($skipitems)) {
                if ($order['is_create'] == 0) {
                    Db::name("cms_order_temp")
                        ->where("id", $order['id'])
                        ->update([
                            'is_create' => 3,
                            'skip_skus' => json_encode($skipitems, JSON_UNESCAPED_UNICODE),
                            'msg' => "遇到白名单sku,待客服人工确认后推送"
                        ]);
                    continue;
                } else if ($order['is_create'] == 3) {

                    continue;
                }

            }

            $orderparam['product'] = $products;
            //发票信息
            if (isset($jdorderinfo['orderInvoice'])) {
                $orderparam['order_invoice'] = [
                    'invoice_form_type' => $jdorderinfo['orderInvoice']['invoiceFormType'] ?? 0,//发票类型：0.纸质发票1.电子发票
                    'invoice_itle' => $jdorderinfo['orderInvoice']['invoiceTitle'] ?? "",//发票抬头
                    'invoice_duty_no' => $jdorderinfo['orderInvoice']['invoiceDutyNo'] ?? "",//发票税号
                    'invoice_mail' => $jdorderinfo['orderInvoice']['invoiceMail'] ?? "",//发票邮箱地址
                    'invoice_money' => isset($jdorderinfo['orderInvoice']['invoiceMoney']) ? $this->price_format($jdorderinfo['orderInvoice']['invoiceMoney']) : "",//发票金额
                    'invoice_type' => $jdorderinfo['orderInvoice']['invoiceType'] ?? "",//发票抬头类型(0：个人、1：企业普票、2：企业专票)
                    'invoice_money_detail' => $jdorderinfo['orderInvoice']['invoiceMoneyDetail'] ?? "",//发票金额描述
                    'invoice_address' => $jdorderinfo['orderInvoice']['invoiceAddress'] ?? "",//公司注册地址
                    'invoice_tel_no' => $jdorderinfo['orderInvoice']['invoiceTelNo'] ?? "",//公司注册电话
                    'invoice_bank_name' => $jdorderinfo['orderInvoice']['invoiceBankName'] ?? "",//公司开户银行名称
                    'invoice_account_no' => $jdorderinfo['orderInvoice']['invoiceAccountNo'] ?? "",//公司开户银行账户
                    'invoice_content' => $jdorderinfo['orderInvoice']['invoiceContent'] ?? "",//发票内容
                ];
            } else {
                $orderparam['order_invoice'] = [];
            }

            //生成高霍系统订单
            $iscreate = $this->createorder($orderparam);
            if (isset($iscreate['status'])) {
                if ($iscreate['status'] == 200) {
                    $msg = '';
                    //先接单
                    $acceptres = $this->orderacceptoperate(null, null, $order['order_id']);
                    if (!$acceptres) {
                        $msg .= '仓库确认失败,';
                    }
                    //发货
                    $delivery = $this->orderserllerdelivery(null, null, $order['order_id']);
                    if (!$delivery) {
                        $msg .= '发货失败';
                    }

                    Db::name("cms_order_temp")
                        ->where("id", $order['id'])
                        ->update([
                            'order_status' => 33040,
                            'is_create' => 1,
                            'msg' => $msg
                        ]);

                } else {
                    Db::name("cms_order_temp")
                        ->where("id", $order['id'])
                        ->update([
                            'is_create' => 2,
                            'msg' => $iscreate['msg']
                        ]);
                }
            }
            //sleep(10);
        } // endfor
    }


    /**
     * 计划任务同步到家sku
     * php think AdaRepair synDaojiaskus  --money=
     *
     */
    protected function synDaojiaskus($input, $output)
    {

        $redis = new \app\common\model\Redis;
        $merchantList = Db::name("merchant")->where('merchant_state', 1)->select();
        $limit = 50;

        $this->model = new \app\admin\model\cms\Archives;
        $skukk = [];
        $skus = $this->model->where('status', 'normal')
            ->order('id', 'desc')
            ->field('id,sku,price,stock,status,memo,d_sta,is_daojia_update')
            ->whereNotNull("is_daojia_update")
            ->whereRaw("is_daojia_update <> ''")
            ->select();
        foreach ($skus as $arr) {
            $is_daojia_update = json_decode($arr['is_daojia_update'], true);
            foreach ($is_daojia_update as $ai) {
                if (!empty($ai['daojia_sku_id'])) {
                    $skukk[$ai['daojia_sku_id']] = $arr['id'];
                    break;
                }

            }

        }
        foreach ($merchantList as $merchant) {
            $this->merchant_id = $merchant['id'];
            $kk = "daojia--" . $this->merchant_id;
            $kk2 = "daojia--sku" . $this->merchant_id;
            $search_after_skuid = '';
            for ($page = 1; $page <= 2000; $page++) {
                $apigrams = ['pageNo' => $page, 'pageSize' => $limit];
                if ($search_after_skuid) {
                    $apigrams['search_after_skuid'] = $search_after_skuid;
                }
                $list = $this->querygoodslist(null, null, $apigrams, $this->merchant_id);
                $search_after_skuid = $list['search_after_skuid'] ?? '';
                $list = $list['list'] ?? [];

                if (!empty($list)) {
                    foreach ($list as $item) {
                        $id = $this->model->where(['sku' => $item['out_sku_id']])->value('id');
                        $this->model->where(['id' => $id])->update(['daojiasku' => $item['sku_id']]);
                        if (empty($redis->getRedis()->hGet($kk, $item['out_sku_id']))) {
                            $redis->getRedis()->hSet($kk, $item['out_sku_id'], $id);
                        }
                        if (empty($redis->getRedis()->hGet($kk2, $item['out_sku_id']))) {
                            $redis->getRedis()->hSet($kk2, $item['out_sku_id'], $item['sku_id']);
                        }
                    }
                } else {
                    break;
                }

            }

        }
        echo date("Y-m-d H:i:s") . " synDaojiaskus  end \r\n";
    }


    /**
     * 获取到家商品列表
     * php think AdaRepair querygoodslist  --money=
     *
     */
    protected function querygoodslist($input = null, $output = null, $param = [], $merchantId = 0)
    {
        $this->merchant_id = $merchantId;
        //同步库存
        $this->djdao();
        $req = new \verificationUpdateToken();
        if (!isset($param['pageNo']) || $param['pageNo'] <= 0) {
            $param['pageNo'] = 1;
        }
        if (!isset($param['pageSize']) || $param['pageSize'] <= 0) {
            $param['pageSize'] = 10;
        }
        $apiParam = [];
        $apiParam['pageNo'] = $param['pageNo'];
        $apiParam['pageSize'] = $param['pageSize'];
        if (isset($param['skuName'])) {
            $apiParam['skuName'] = $param['skuName'];
        }
        if (isset($param['upcCode'])) {
            $apiParam['upcCode'] = $param['upcCode'];
        }
        if (isset($param['skuId'])) {
            $apiParam['skuId'] = $param['skuId'];
        }
        //查询递进skuId 说明：pageNo=1时，searchAfterSkuId非必填；pageNo!=1时，必填，取上页调用结果返回的searchAfterSkuId值
        if ($apiParam['pageNo'] > 1 && isset($param['search_after_skuid'])) {
            $apiParam['searchAfterSkuId'] = $param['search_after_skuid'];
        }
        $this->model = new \app\admin\model\cms\Archives;
        $req->setApiPath("/pms/querySkuInfoList");
        $req->setOperrams($apiParam);
        $qtyresp = $this->jdd->execute($req);

        if (empty($qtyresp)) {
            return false;
        }
        //同步库存
        if ($qtyresp['code'] != 0) {
            return [];
        }
        $qtydata = json_decode($qtyresp['data'], true);
        if (intval($qtydata['code']) != 0) {
            return [];
        }
        $res = ['count' => $qtydata['result']['count'], 'search_after_skuid' => $qtydata['result']['searchAfterSkuId'], 'list' => []];
        $result = json_decode($qtydata['result']['result'], true);
        if (!empty($result)) {
            foreach ($result as $item) {
                $res['list'][] = [
                    'sku_id' => $item['skuId'] ?? "", //到家商品编码
                    'out_sku_id' => $item['outSkuId'] ?? "", // 商家商品编码
                    'category_id' => $item['categoryId'] ?? 0, //到家类目编码
                    'brand_id' => $item['brandId'] ?? 0, //到家品牌编码
                    'shop_categories' => $item['shopCategories'] ?? [], //商家店内分类编码,
                    'sku_name' => $item['skuName'] ?? "", //商品名称
                    'upc_code' => $item['upcCode'] ?? "", //商品UPC编码
                    'fixed_status' => $item['fixedStatus'], //商家商品上下架状态(1:上架;2:下架;4:删除;)
                ];
            }
        }
        return $res;
    }

    /**
     * 修改 channel
     * php think AdaRepair updatechannel  --money=
     *
     */
    protected function updatechannel($input, $output)
    {
        $channellist = Db::name("cms_channel")
            ->whereRaw("jd_cate_id <> ''")
            ->select();
        foreach ($channellist as $channel) {
            $cates = [];
            $cates[1] = $channel['jd_cate_id'];
            Db::name("cms_channel")
                ->where('id', $channel['id'])
                ->update([
                    'jd_cate_id' => json_encode($cates)
                ]);
        }
    }

    /**
     * 查询订单是否可申请售后API
     * php think AdaRepair getIsCanApplyInfo  --money=
     *
     */
    protected function getIsCanApplyInfo($input, $output)
    {
        $iscan = new getIsCanApplyInfo();
        $iscan->orderId = "284808155942";
        $iscan->skuId = "100046550074";
        $jdZeus = new JdZeus($this->jdclent,$this->ZeusApi);
        $res = $jdZeus->getIsCanApplyInfo($iscan);
        return $res;
    }

    /**
     * 获取售后申请原因列表API
     * php think AdaRepair getApplyReason  --money=
     *
     */
    protected function getApplyReason($input, $output)
    {
        $iscan = new getApplyReason();
        $iscan->orderId = "283035753464";
        $iscan->skuId = "100004553486";
        $iscan->afsType = 10;
        $jdZeus = new JdZeus($this->jdclent,$this->ZeusApi);
        $res = $jdZeus->getApplyReason($iscan);
        return $res;
    }

    /**
     * 获取售后申请原因列表API
     * php think AdaRepair createAfsApply  --money=
     *
     */
    protected function createAfsApply($input, $output)
    {
        $iscan = new createAfsApply();
        $iscan->orderId = "284808155942";
        $iscan->applyReasonName = "测试";
        $iscan->afsType = 10;
        $jdZeus = new JdZeus($this->jdclent,$this->ZeusApi);
        $res = $jdZeus->getApplyReason($iscan);
        return $res;
    }

    //生成本地新图
    protected function createNewImage($imageurl, $sku, $width = 650, $height = 650)
    {
        $picfilename = basename($imageurl);
        $filename = ROOT_PATH . 'public/uploads/product/' . $sku . '_' . $picfilename;
        try {
            if (!file_exists($filename)) {
                GrabImage($imageurl, $filename);
                resize_image($picfilename, $filename, $width, $height);
            }
        } catch (\Exception $e) {
            mylog("pic error", $imageurl . '#' . $e->getMessage());
            return '';
        }
        $newpicurl = "http://111.229.37.112//uploads/product/" . $sku . '_' . $picfilename;
        return $newpicurl;
    }

    //解析京东到家加密
    protected function decrypt($encrypted)
    {
        $secret = $this->jdd->appsecret;
        $k = substr($secret, 0, 16);
        $v = substr($secret, 16);
        $ciphertext_dec = base64_decode($encrypted);
        $decrypted = openssl_decrypt($ciphertext_dec, 'AES-128-CBC', $k, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $v);
        return json_decode(rtrim(rtrim($decrypted, chr(0)), chr(7)), true);
    }

    protected function price_format($money)
    {
        return round($money / 100, 2);
    }
}