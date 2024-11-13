<?php
class CanApplyInfoParam{

    private $params=array();

    function __construct(){
        $this->params["@type"]="com.jd.ctp.aftersales.rpc.jos.apply.CanApplyInfoParam";
    }
        
    private $pin;
    
    public function setPin($pin){
        $this->params['pin'] = $pin;
    }

    public function getPin(){
        return $this->pin;
    }
            
    private $skuId;
    
    public function setSkuId($skuId){
        $this->params['skuId'] = $skuId;
    }

    public function getSkuId(){
        return $this->skuId;
    }
            
    private $orderId;
    
    public function setOrderId($orderId){
        $this->params['orderId'] = $orderId;
    }

    public function getOrderId(){
        return $this->orderId;
    }
            
    private $afsDetailType;
    
    public function setAfsDetailType($afsDetailType){
        $this->params['afsDetailType'] = $afsDetailType;
    }

    public function getAfsDetailType(){
        return $this->afsDetailType;
    }
    
    function getInstance(){
        return $this->params;
    }

}

?>