<?php
class CtpOrderPushOutPlatFormDataWithSkuRequest
{


	private $apiParas = array();
	
	public function getApiMethodName(){
	  return "jingdong.ctp.order.pushOutPlatFormDataWithSku";
	}
	
	public function getApiParas(){
	    if(empty($this->apiParas)){
            return "{}";
        }
        return json_encode($this->apiParas);
	}
	
	public function check(){
		
	}
	
	public function putOtherTextParam($key, $value){
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}

    private $version;

    public function setVersion($version){
        $this->version = $version;
    }

    public function getVersion(){
        return $this->version;
    }
                                                        		                                    	                   			private $traceId;
    	                        
	public function setTraceId($traceId){
		$this->traceId = $traceId;
         $this->apiParas["traceId"] = $traceId;
	}

	public function getTraceId(){
	  return $this->traceId;
	}

                        	                   			private $clientIp;
    	                        
	public function setClientIp($clientIp){
		$this->clientIp = $clientIp;
         $this->apiParas["clientIp"] = $clientIp;
	}

	public function getClientIp(){
	  return $this->clientIp;
	}

                        	                   			private $customerId;
    	                        
	public function setCustomerId($customerId){
		$this->customerId = $customerId;
         $this->apiParas["customerId"] = $customerId;
	}

	public function getCustomerId(){
	  return $this->customerId;
	}

                        	                   			private $clientPort;
    	                        
	public function setClientPort($clientPort){
		$this->clientPort = $clientPort;
         $this->apiParas["clientPort"] = $clientPort;
	}

	public function getClientPort(){
	  return $this->clientPort;
	}

                        	                   			private $appKey;
    	                        
	public function setAppKey($appKey){
		$this->appKey = $appKey;
         $this->apiParas["appKey"] = $appKey;
	}

	public function getAppKey(){
	  return $this->appKey;
	}

                        	                   			private $channelId;
    	                        
	public function setChannelId($channelId){
		$this->channelId = $channelId;
         $this->apiParas["channelId"] = $channelId;
	}

	public function getChannelId(){
	  return $this->channelId;
	}

                        	                   			private $venderId;
    	                        
	public function setVenderId($venderId){
		$this->venderId = $venderId;
         $this->apiParas["venderId"] = $venderId;
	}

	public function getVenderId(){
	  return $this->venderId;
	}

                                                                        		                                    	                   			private $outPlatformShopId;
    	                        
	public function setOutPlatformShopId($outPlatformShopId){
		$this->outPlatformShopId = $outPlatformShopId;
         $this->apiParas["outPlatformShopId"] = $outPlatformShopId;
	}

	public function getOutPlatformShopId(){
	  return $this->outPlatformShopId;
	}

                        	                        	                   			private $orderId;
    	                        
	public function setOrderId($orderId){
		$this->orderId = $orderId;
         $this->apiParas["orderId"] = $orderId;
	}

	public function getOrderId(){
	  return $this->orderId;
	}

                        	                   			private $outPlatformParentOrderId;
    	                        
	public function setOutPlatformParentOrderId($outPlatformParentOrderId){
		$this->outPlatformParentOrderId = $outPlatformParentOrderId;
         $this->apiParas["outPlatformParentOrderId"] = $outPlatformParentOrderId;
	}

	public function getOutPlatformParentOrderId(){
	  return $this->outPlatformParentOrderId;
	}

                        	                   			private $outPlatformSkuId;
    	                        
	public function setOutPlatformSkuId($outPlatformSkuId){
		$this->outPlatformSkuId = $outPlatformSkuId;
         $this->apiParas["outPlatformSkuId"] = $outPlatformSkuId;
	}

	public function getOutPlatformSkuId(){
	  return $this->outPlatformSkuId;
	}

                        	                   			private $skuId;
    	                        
	public function setSkuId($skuId){
		$this->skuId = $skuId;
         $this->apiParas["skuId"] = $skuId;
	}

	public function getSkuId(){
	  return $this->skuId;
	}

                        	                   			private $sourceType;
    	                        
	public function setSourceType($sourceType){
		$this->sourceType = $sourceType;
         $this->apiParas["sourceType"] = $sourceType;
	}

	public function getSourceType(){
	  return $this->sourceType;
	}

                        	                   			private $outPlatformSource;
    	                        
	public function setOutPlatformSource($outPlatformSource){
		$this->outPlatformSource = $outPlatformSource;
         $this->apiParas["outPlatformSource"] = $outPlatformSource;
	}

	public function getOutPlatformSource(){
	  return $this->outPlatformSource;
	}

                            }





        
 

