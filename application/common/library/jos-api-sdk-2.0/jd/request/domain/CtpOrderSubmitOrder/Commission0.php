<?php

class Commission0{

    private $params=array();

    function __construct(){
        $this->params["@type"]="com.jd.ctp.order.api.bean.Commission";
    }
        
    private $subCommissionId;
    
    public function setSubCommissionId($subCommissionId){
        $this->params['subCommissionId'] = $subCommissionId;
    }

    public function getSubCommissionId(){
        return $this->subCommissionId;
    }
            
    private $subCommissionName;
    
    public function setSubCommissionName($subCommissionName){
        $this->params['subCommissionName'] = $subCommissionName;
    }

    public function getSubCommissionName(){
        return $this->subCommissionName;
    }
            
    private $subCommissionFee;
    
    public function setSubCommissionFee($subCommissionFee){
        $this->params['subCommissionFee'] = $subCommissionFee;
    }

    public function getSubCommissionFee(){
        return $this->subCommissionFee;
    }
            
    private $subCommissionRatio;
    
    public function setSubCommissionRatio($subCommissionRatio){
        $this->params['subCommissionRatio'] = $subCommissionRatio;
    }

    public function getSubCommissionRatio(){
        return $this->subCommissionRatio;
    }
            
    private $jdSkuId;
    
    public function setJdSkuId($jdSkuId){
        $this->params['jdSkuId'] = $jdSkuId;
    }

    public function getJdSkuId(){
        return $this->jdSkuId;
    }
    
    function getInstance(){
        return $this->params;
    }

}

?>