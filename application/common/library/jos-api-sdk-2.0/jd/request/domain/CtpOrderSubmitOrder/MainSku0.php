<?php

class MainSku0{

    private $params=array();

    function __construct(){
        $this->params["@type"]="com.jd.ctp.order.api.bean.Sku";
    }
        
    private $skuId;
    
    public function setSkuId($skuId){
        $this->params['skuId'] = $skuId;
    }

    public function getSkuId(){
        return $this->skuId;
    }
            
    private $skuPrice;
    
    public function setSkuPrice($skuPrice){
        $this->params['skuPrice'] = $skuPrice;
    }

    public function getSkuPrice(){
        return $this->skuPrice;
    }
            
    private $quantity;
    
    public function setQuantity($quantity){
        $this->params['quantity'] = $quantity;
    }

    public function getQuantity(){
        return $this->quantity;
    }
            
    private $skuName;
    
    public function setSkuName($skuName){
        $this->params['skuName'] = $skuName;
    }

    public function getSkuName(){
        return $this->skuName;
    }
            
    private $freightPrice;
    
    public function setFreightPrice($freightPrice){
        $this->params['freightPrice'] = $freightPrice;
    }

    public function getFreightPrice(){
        return $this->freightPrice;
    }
            
    private $taxPrice;
    
    public function setTaxPrice($taxPrice){
        $this->params['taxPrice'] = $taxPrice;
    }

    public function getTaxPrice(){
        return $this->taxPrice;
    }
            
    private $taxType;
    
    public function setTaxType($taxType){
        $this->params['taxType'] = $taxType;
    }

    public function getTaxType(){
        return $this->taxType;
    }
            
    private $discountPrice;
    
    public function setDiscountPrice($discountPrice){
        $this->params['discountPrice'] = $discountPrice;
    }

    public function getDiscountPrice(){
        return $this->discountPrice;
    }
            
    private $xnztExtendInfo;
    
    public function setXnztExtendInfo($xnztExtendInfo){
        $this->params['xnztExtendInfo'] = $xnztExtendInfo;
    }

    public function getXnztExtendInfo(){
        return $this->xnztExtendInfo;
    }
    
    function getInstance(){
        return $this->params;
    }

}

?>