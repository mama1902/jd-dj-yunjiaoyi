<?php
namespace CtpWareSkupoolGetSkuPoolList;
class ApiSkuPoolListParam18{

    private $params=array();

    function __construct(){
        $this->params["@type"]="com.jd.ctp.ware.rpc.skupool.ApiSkuPoolListParam";
    }
        
    private $pageSize;
    
    public function setPageSize($pageSize){
        $this->params['pageSize'] = $pageSize;
    }

    public function getPageSize(){
        return $this->pageSize;
    }
            
    private $skuPoolType;
    
    public function setSkuPoolType($skuPoolType){
        $this->params['skuPoolType'] = $skuPoolType;
    }

    public function getSkuPoolType(){
        return $this->skuPoolType;
    }
            
    private $scrollId;
    
    public function setScrollId($scrollId){
        $this->params['scrollId'] = $scrollId;
    }

    public function getScrollId(){
        return $this->scrollId;
    }
    
    function getInstance(){
        return $this->params;
    }

}

?>