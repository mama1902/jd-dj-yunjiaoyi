<?php
namespace CtpOrderPushOutPlatFormData;
class DataTransmissionParam13{

    private $params=array();

    function __construct(){
        $this->params["@type"]="com.jd.ctp.order.api.param.ApiDataTransmissionParam";
    }
        
    private $outPlatformShopId;
    
    public function setOutPlatformShopId($outPlatformShopId){
        $this->params['outPlatformShopId'] = $outPlatformShopId;
    }

    public function getOutPlatformShopId(){
        return $this->outPlatformShopId;
    }
            
    private $pin;
    
    public function setPin($pin){
        $this->params['pin'] = $pin;
    }

    public function getPin(){
        return $this->pin;
    }
            
    private $sourceType;
    
    public function setSourceType($sourceType){
        $this->params['sourceType'] = $sourceType;
    }

    public function getSourceType(){
        return $this->sourceType;
    }
            
    private $orderId;
    
    public function setOrderId($orderId){
        $this->params['orderId'] = $orderId;
    }

    public function getOrderId(){
        return $this->orderId;
    }
            
    private $outPlatformPayTime;
    
    public function setOutPlatformPayTime($outPlatformPayTime){
        $this->params['outPlatformPayTime'] = $outPlatformPayTime;
    }

    public function getOutPlatformPayTime(){
        return $this->outPlatformPayTime;
    }
            
    private $outPlatformParentOrderId;
    
    public function setOutPlatformParentOrderId($outPlatformParentOrderId){
        $this->params['outPlatformParentOrderId'] = $outPlatformParentOrderId;
    }

    public function getOutPlatformParentOrderId(){
        return $this->outPlatformParentOrderId;
    }
    
    function getInstance(){
        return $this->params;
    }

}

?>