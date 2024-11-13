<?php

header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
include "jdSdk.php";
//实例化JdClient类	/jd/JdClient.php
$c = new JddClient;
$c->connectUrl 	= "https://openo2o.jd.com/djapi";//平台的接口地址
$c->appkey 		= "66012d001c8e46deb178d85d4ff9ce9d";//应用的app_key
$c->appsecret 	= "1da6c434d91148f7966ce64ed6093c97";//应用的app_secret
$c->token 		= "fdc58d7f-bedf-4660-988e-79b303268534";//应用的token。

function OrderCancel($client) {
	//实例化OrderCancel类	/jd/request/OrderCancel.php
    $req = new OrderCancel;
    $req->setOrderId("100001021120119");
    $req->setOperPin("测试");
    $req->setOperRemark("备注");
    $req->setOperTime(date('Y-m-d H:i:s'));
    try 
    {
		echo "》starting:";
        $resp = $client->execute($req);
		echo "<br>";
		echo "》result:";
		echo "<br>";
		print_r($resp);
    } 
    catch (Exception $e) 
    {
        //TODO: error handler
        echo "<br>" . $e->getMessage();
        echo "<br>" . $e->getCode();
    }
}

OrderCancel($c);