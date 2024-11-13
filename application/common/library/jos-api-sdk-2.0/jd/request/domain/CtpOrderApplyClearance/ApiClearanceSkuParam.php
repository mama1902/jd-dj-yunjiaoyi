<?php
namespace CtpOrderApplyClearance;
class ApiClearanceSkuParam{

    private $params=array();

    function __construct(){
        $this->params["@type"]="com.jd.ctp.order.api.param.ApiClearanceSkuParam";
    }
        
    private $totalTax;
    
    public function setTotalTax($totalTax){
        $this->params['totalTax'] = $totalTax;
    }

    public function getTotalTax(){
        return $this->totalTax;
    }
            
    private $totalPrice;
    
    public function setTotalPrice($totalPrice){
        $this->params['totalPrice'] = $totalPrice;
    }

    public function getTotalPrice(){
        return $this->totalPrice;
    }
            
    private $price;
    
    public function setPrice($price){
        $this->params['price'] = $price;
    }

    public function getPrice(){
        return $this->price;
    }
            
    private $num;
    
    public function setNum($num){
        $this->params['num'] = $num;
    }

    public function getNum(){
        return $this->num;
    }
            
    private $skuCurrency;
    
    public function setSkuCurrency($skuCurrency){
        $this->params['skuCurrency'] = $skuCurrency;
    }

    public function getSkuCurrency(){
        return $this->skuCurrency;
    }
            
    private $skuId;
    
    public function setSkuId($skuId){
        $this->params['skuId'] = $skuId;
    }

    public function getSkuId(){
        return $this->skuId;
    }
    
    function getInstance(){
        return $this->params;
    }

}

?>