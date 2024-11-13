<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/23
 * Time: 12:35
 */

namespace app\admin\command;

use think\Console;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\Db;
use think\Log;


class Jq extends Command
{

    /**
     * @var \app\common\library\Jq
     */
    protected $jdd = null;

    /**
     * @var \app\admin\model\cms\Archives
     */
    protected $model = null;

    /**
     * @var number
     */
    protected $shop_id = null;

    protected function configure()
    {
        $this->setName('AdaJq')
            ->addArgument('type', Argument::OPTIONAL, '类型', '')
            ->addOption('money', null, Option::VALUE_REQUIRED, '123')
            ->addOption('mo', null, Option::VALUE_REQUIRED, '是否同步到家')
            ->setDescription('Command run Controller Action!');
    }

    protected function execute(Input $input, Output $output)
    {
        if (empty($this->jdd)) {
            ini_set("memory_limit", "-1");
            $this->jdd = new \app\common\library\Jq();
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


    //  php think AdaJq Autotest --money=18053149010
    protected function Autotest(Input $input, Output $output)
    {
        $obj=  $this->jdd->switchMo(1);
        $re = $obj->postToCpData(['response' => 1]);
        var_dump($re);
    }


    //  php think AdaJq Autotask --money=18053149010
    protected function Autotask(Input $input, Output $output)
    {



        while(true) {

            $obj=  $this->jdd->switchMo(1);
            $pullResult = $obj->ct_order_create();

            if(  !empty($pullResult["result"]["ackIndex"]) ) {
                $obj->postToCpData(['response' => json_encode($pullResult)]);
                $ackResult=$obj->success_ct_order_create($pullResult["result"]["ackIndex"]);

            }

            sleep(3);


            $obj=  $this->jdd->switchMo(2);
            $pullResult = $obj->ct_order_create();

            if(  !empty($pullResult["result"]["ackIndex"]) ) {


                $obj->postToCpData(['response' => json_encode($pullResult)]);


                $ackResult=$obj->success_ct_order_create($pullResult["result"]["ackIndex"]);

            }

            sleep(3);

            $obj=  $this->jdd->switchMo(3);
            $pullResult = $obj->ct_order_create();

            if(  !empty($pullResult["result"]["ackIndex"]) ) {
                $obj->postToCpData(['response' => json_encode($pullResult)]);
                $ackResult=$obj->success_ct_order_create($pullResult["result"]["ackIndex"]);

            }


            $obj=  $this->jdd->switchMo(4);
            $pullResult = $obj->ct_order_create();

            if(  !empty($pullResult["result"]["ackIndex"]) ) {
                $obj->postToCpData(['response' => json_encode($pullResult)]);
                $ackResult=$obj->success_ct_order_create($pullResult["result"]["ackIndex"]);

            }

            sleep(10);


            $obj=  $this->jdd->switchMo(5);
            $pullResult = $obj->ct_order_create();

            if(  !empty($pullResult["result"]["ackIndex"]) ) {
                $obj->postToCpData(['response' => json_encode($pullResult)]);
                $ackResult=$obj->success_ct_order_create($pullResult["result"]["ackIndex"]);

            }

            sleep(10);


            $obj=  $this->jdd->switchMo(6);
            $pullResult = $obj->ct_order_create();

            if(  !empty($pullResult["result"]["ackIndex"]) ) {
                $obj->postToCpData(['response' => json_encode($pullResult)]);
                $ackResult=$obj->success_ct_order_create($pullResult["result"]["ackIndex"]);

            }

            sleep(10);


            $obj=  $this->jdd->switchMo(7);
            $pullResult = $obj->ct_order_create();

            if(  !empty($pullResult["result"]["ackIndex"]) ) {
                $obj->postToCpData(['response' => json_encode($pullResult)]);
                $ackResult=$obj->success_ct_order_create($pullResult["result"]["ackIndex"]);

            }

            sleep(10);

            $obj=  $this->jdd->switchMo(8);
            $pullResult = $obj->ct_order_create();

            if(  !empty($pullResult["result"]["ackIndex"]) ) {
                $obj->postToCpData(['response' => json_encode($pullResult)]);
                $ackResult=$obj->success_ct_order_create($pullResult["result"]["ackIndex"]);

            }

            sleep(10);

            $obj=  $this->jdd->switchMo(9);
            $pullResult = $obj->ct_order_create();

            if(  !empty($pullResult["result"]["ackIndex"]) ) {
                $obj->postToCpData(['response' => json_encode($pullResult)]);
                $ackResult=$obj->success_ct_order_create($pullResult["result"]["ackIndex"]);

            }



        }

    }


    //  php think AdaJq clearSyndaojia
    protected function clearSyndaojia(Input $input, Output $output)
    {
        $redis = new \app\common\model\Redis;

        $merchantIds = [1,2,3];
        foreach ($merchantIds as $merchantId) {

            $redis->getRedis()->del("sku--".$merchantId);
            $redis->getRedis()->del("nosku--".$merchantId);
        }
        $arr  =  [6,8,11];
        foreach ($arr as $key) {
            $redis->delLock($key."-syndaojiaing");
        }


    }


    //  php think AdaJq Syndaojia
    protected function Syndaojia(Input $input, Output $output)
    {
        $redis = new \app\common\model\Redis;
        $user_list = \app\admin\model\User::column('id,username');

        $timeout = 1;
        $limit = 100;
        while (true){
            foreach ($user_list as $id => $username) {
                $queueName = $id."-syndaojia";
                if ($item = $redis->getQueueKey($queueName) ) {
                    $item = json_decode($item, true);
                    $idsarr  = array_chunk($item['ids'], $limit);
                    $daojia = 1;
                    // 多进程处理----------------------------------
                    foreach ($idsarr as $page => $ids) {
                        $page++;
                        $pid = pcntl_fork();
                        if ($pid == -1) {
                            print "创建子进程失败 $page\n";
                            exit(-1);
                        } else if ($pid) {
                            // 父进程
                            pcntl_wait($status,WNOHANG);
                        } else {
                            // 增加监控 query
                            echo date("Y-m-d H:i:s"). $daojia . '#' .implode(',', $ids).'#'.$item['vender_id']." 开始同步--- \r\n";
                            Db::getConnection()->close()->free();
                            $output = Console::call('AdaRepair', ['justsyntoDaojia', '--money', $daojia . '#' .implode(',', $ids).'#'.$item['vender_id']]);
                            sleep(6);

                            exit(0);
                        }

                    }
                    while (pcntl_waitpid(0, $status) != -1) {
                        $status = pcntl_wexitstatus($status);
                        echo "Child $status completed \r\n";
                    }
                    $redis->delLock($queueName.'ing');
                    echo $queueName.' - '.date("Y-m-d H:i:s")." cur completed \r\n";
                }
            }
            sleep(2);
            $timeout++;
        }

    }


    //  php think AdaJq testdb
    protected function testdb(Input $input, Output $output)
    {
        $csn = 'mysql:host=sh-cynosdbmysql-grp-rbdgfg8m.sql.tencentcdb.com;dbname=gaohuo;port=24450;charset=utf8';
        echo "csn:" .$csn."\r\n";
        $i = 0;
        while ($i <= 50) {

            try{
                $time1 = microtime(true);
                $db = new \PDO($csn,'root', '548609XMa2');
                echo "mysql  ok ".(microtime(true)-$time1)." s .. \r\n";
            }catch(\Exception $e){
                echo 'mysql error: '.$e->getMessage()."\r\n";
            }

            $i++;
            sleep(2);
        }


    }

    //  php think AdaJq exportskus
    protected function exportskus(Input $input, Output $output)
    {
        $rows = [];
        $rows[] = [
            'col1' => 'title',
            'col2' => 'price',
            'col3' => 'sku',
            'col4' => 'category',
            'col5' => 'category2',
            'col6' => 'venderName',
            'col7' => 'brand',
        ];

        $this->model = new \app\admin\model\cms\Archives;
        $limit = 200;
        for ($page = 1; $page <= 2000; $page++) {
            $result = $this->model::alias('t')
                ->join('cms_channel ta', 'ta.id = t.channel_id', 'left')
                ->join('cms_channel pta', 'ta.parent_id = pta.id', 'left')
                ->join('j_cms_addonproduct p', 'p.id = t.id', 'left')
                ->where('t.status', 'normal')
                ->order('t.id', 'desc')
                ->field('t.*,ta.name,ta.jd_cate_id,p.content,pta.jd_cate_id as parent_jd_cate_id')
                ->page($page)
                ->limit($limit);
            $result = $result->select();

            if (empty($result)) {
                break;
            } else {
                foreach ($result as $item) {
                    $content = json_decode($item['content'], true);
                    if (empty($content) || empty($content['jingdong_ctp_ware_sku_getSkuDetail_responce'])) {
                        continue;
                    }
                    $jdinfo = $content['jingdong_ctp_ware_sku_getSkuDetail_responce']['result']['data'] ?? '';
                    $skuBaseInfo = $jdinfo[0]['skuBaseInfo']  ?? '';
                    if ( empty($skuBaseInfo) ) {
                        continue;
                    }
                    $rows[] = [
                        'col1' => $item['title'],
                        'col2' => $item['price'],
                        'col3' =>$item['sku'],
                        'col4' => $item['name'],
                        'col5' => $skuBaseInfo['categoryName1'].'/' .$skuBaseInfo['categoryName2']. '/' .$skuBaseInfo['categoryName'] ,
                        'col6' => $skuBaseInfo['venderName'] ?? '',
                        'col7' => '',
                    ];

                }

            }

        }
        $file = $this->createEmailfiles($rows);
        echo "导出成功，文件是 $file  \r\n";
    }




    // 生成email附件
    public function createEmailfiles($excelData, $filename = 'price.csv'){

        $filePath = ROOT_PATH . DS . 'public' . DS ;


        if ( !is_dir($filePath) ) {
            mkdir($filePath, 0755, true);
        }
        $fileName = $filePath. $filename;

        if (file_exists($fileName)) {
            $fp = fopen($fileName, 'r+');
        } else {
            $fp = fopen($fileName, 'a+');
        }
        fwrite($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($fp, $excelData[0]);

        foreach ($excelData as $key => &$row) {
            if ($key==0) {
                continue;
            }
            $row = $this->handleRowData($row);
            fputcsv($fp, $row);
        }

        fclose($fp);

        if (!file_exists($fileName)) {
            throw new \Exception($fileName . '文件生成失败');
        }

        return $fileName;
    }

    public function handleRowData($row)
    {
        foreach ($row as $key => $value) {
            $coding = mb_detect_encoding($value, array("ASCII", 'UTF-8', "GB2312", "GBK", 'BIG5', 'ISO-8859-1'));
            $row[$key] = mb_convert_encoding($value, 'UTF-8', $coding);
        }
        return $row;
    }

}