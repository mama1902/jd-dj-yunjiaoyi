<?php
class SkuPriceInfoParam14{

    private $params=array();

    function __construct(){
        $this->params["@type"]="com.jd.ctp.ware.rpc.sku.ApiSkuPriceInfoParam";
    }
        
    private $skuIdSet;
                        
    public function setSkuIdSet($skuIdSet){
        $this->params['skuIdSet'] = $skuIdSet;
    }
                    
    
    function getInstance(){
        return $this->params;
    }

}

?>