<?php

class OutPlatformOrderInfo0{

    private $params=array();

    function __construct(){
        $this->params["@type"]="com.jd.ctp.order.api.bean.OutPlatformOrderInfo";
    }
        
    private $outPlatformParentOrderId;
    
    public function setOutPlatformParentOrderId($outPlatformParentOrderId){
        $this->params['outPlatformParentOrderId'] = $outPlatformParentOrderId;
    }

    public function getOutPlatformParentOrderId(){
        return $this->outPlatformParentOrderId;
    }
            
    private $outPlatformOrderId;
    
    public function setOutPlatformOrderId($outPlatformOrderId){
        $this->params['outPlatformOrderId'] = $outPlatformOrderId;
    }

    public function getOutPlatformOrderId(){
        return $this->outPlatformOrderId;
    }
            
    private $outPlatformCreateTime;
    
    public function setOutPlatformCreateTime($outPlatformCreateTime){
        $this->params['outPlatformCreateTime'] = $outPlatformCreateTime;
    }

    public function getOutPlatformCreateTime(){
        return $this->outPlatformCreateTime;
    }
            
    private $outPlatformPayTime;
    
    public function setOutPlatformPayTime($outPlatformPayTime){
        $this->params['outPlatformPayTime'] = $outPlatformPayTime;
    }

    public function getOutPlatformPayTime(){
        return $this->outPlatformPayTime;
    }
            
    private $outPlatformShopId;
    
    public function setOutPlatformShopId($outPlatformShopId){
        $this->params['outPlatformShopId'] = $outPlatformShopId;
    }

    public function getOutPlatformShopId(){
        return $this->outPlatformShopId;
    }
            
    private $oaId;
    
    public function setOaId($oaId){
        $this->params['oaId'] = $oaId;
    }

    public function getOaId(){
        return $this->oaId;
    }
            
    private $outPlatformShopNameEn;
    
    public function setOutPlatformShopNameEn($outPlatformShopNameEn){
        $this->params['outPlatformShopNameEn'] = $outPlatformShopNameEn;
    }

    public function getOutPlatformShopNameEn(){
        return $this->outPlatformShopNameEn;
    }
            
    private $outPlatformShopNameCn;
    
    public function setOutPlatformShopNameCn($outPlatformShopNameCn){
        $this->params['outPlatformShopNameCn'] = $outPlatformShopNameCn;
    }

    public function getOutPlatformShopNameCn(){
        return $this->outPlatformShopNameCn;
    }
    
    function getInstance(){
        return $this->params;
    }

}

?>