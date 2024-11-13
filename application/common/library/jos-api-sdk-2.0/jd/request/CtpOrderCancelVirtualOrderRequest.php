<?php
class CtpOrderCancelVirtualOrderRequest
{


	private $apiParas = array();
	
	public function getApiMethodName(){
	  return "jingdong.ctp.order.cancelVirtualOrder";
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

                                                                        		                                    	                   			private $refundType;
    	                        
	public function setRefundType($refundType){
		$this->refundType = $refundType;
         $this->apiParas["refundType"] = $refundType;
	}

	public function getRefundType(){
	  return $this->refundType;
	}

                        	                   			private $phoneNumber;
    	                        
	public function setPhoneNumber($phoneNumber){
		$this->phoneNumber = $phoneNumber;
         $this->apiParas["phoneNumber"] = $phoneNumber;
	}

	public function getPhoneNumber(){
	  return $this->phoneNumber;
	}

                        	                   			private $pin;
    	                        
	public function setPin($pin){
		$this->pin = $pin;
         $this->apiParas["pin"] = $pin;
	}

	public function getPin(){
	  return $this->pin;
	}

                        	                   			private $orderId;
    	                        
	public function setOrderId($orderId){
		$this->orderId = $orderId;
         $this->apiParas["orderId"] = $orderId;
	}

	public function getOrderId(){
	  return $this->orderId;
	}

                        	                   			private $transactionNumber;
    	                        
	public function setTransactionNumber($transactionNumber){
		$this->transactionNumber = $transactionNumber;
         $this->apiParas["transactionNumber"] = $transactionNumber;
	}

	public function getTransactionNumber(){
	  return $this->transactionNumber;
	}

                        	                   			private $operationType;
    	                        
	public function setOperationType($operationType){
		$this->operationType = $operationType;
         $this->apiParas["operationType"] = $operationType;
	}

	public function getOperationType(){
	  return $this->operationType;
	}

                        	                   			private $refundAmount;
    	                        
	public function setRefundAmount($refundAmount){
		$this->refundAmount = $refundAmount;
         $this->apiParas["refundAmount"] = $refundAmount;
	}

	public function getRefundAmount(){
	  return $this->refundAmount;
	}

                            }





        
 

