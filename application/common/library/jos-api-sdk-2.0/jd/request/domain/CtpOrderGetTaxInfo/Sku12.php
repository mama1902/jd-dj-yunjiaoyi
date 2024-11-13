<?php
namespace CtpOrderGetTaxInfo;
class Sku12{

    private $params=array();

    function __construct(){
        $this->params["@type"]="com.jd.ctp.order.api.bean.Sku";
    }
        
    private $skuPrice;
    
    public function setSkuPrice($skuPrice){
        $this->params['skuPrice'] = $skuPrice;
    }

    public function getSkuPrice(){
        return $this->skuPrice;
    }
            
    private $freightPrice;
    
    public function setFreightPrice($freightPrice){
        $this->params['freightPrice'] = $freightPrice;
    }

    public function getFreightPrice(){
        return $this->freightPrice;
    }
            
    private $skuId;
    
    public function setSkuId($skuId){
        $this->params['skuId'] = $skuId;
    }

    public function getSkuId(){
        return $this->skuId;
    }
            
    private $quantity;
    
    public function setQuantity($quantity){
        $this->params['quantity'] = $quantity;
    }

    public function getQuantity(){
        return $this->quantity;
    }
    
    function getInstance(){
        return $this->params;
    }

}

?>