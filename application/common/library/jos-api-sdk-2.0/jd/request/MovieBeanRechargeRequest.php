<?php
class MovieBeanRechargeRequest
{


	private $apiParas = array();
	
	public function getApiMethodName(){
	  return "jingdong.movieBeanRecharge";
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
                                                        		                                    	                   			private $xid;
    	                        
	public function setXid($xid){
		$this->xid = $xid;
         $this->apiParas["xid"] = $xid;
	}

	public function getXid(){
	  return $this->xid;
	}

                        	                   			private $agentId;
    	                        
	public function setAgentId($agentId){
		$this->agentId = $agentId;
         $this->apiParas["agentId"] = $agentId;
	}

	public function getAgentId(){
	  return $this->agentId;
	}

                        	                   			private $certPwd;
    	                        
	public function setCertPwd($certPwd){
		$this->certPwd = $certPwd;
         $this->apiParas["certPwd"] = $certPwd;
	}

	public function getCertPwd(){
	  return $this->certPwd;
	}

                        	                   			private $ip;
    	                        
	public function setIp($ip){
		$this->ip = $ip;
         $this->apiParas["ip"] = $ip;
	}

	public function getIp(){
	  return $this->ip;
	}

                        	                   			private $source;
    	                        
	public function setSource($source){
		$this->source = $source;
         $this->apiParas["source"] = $source;
	}

	public function getSource(){
	  return $this->source;
	}

                        	                   			private $certNum;
    	                        
	public function setCertNum($certNum){
		$this->certNum = $certNum;
         $this->apiParas["certNum"] = $certNum;
	}

	public function getCertNum(){
	  return $this->certNum;
	}

                        	                   			private $openIdBuyer;
    	                                                                        
	public function setOpenIdBuyer($openIdBuyer){
		$this->openIdBuyer = $openIdBuyer;
         $this->apiParas["open_id_buyer"] = $openIdBuyer;
	}

	public function getOpenIdBuyer(){
	  return $this->openIdBuyer;
	}

                        	                   			private $xidBuyer;
    	                                                            
	public function setXidBuyer($xidBuyer){
		$this->xidBuyer = $xidBuyer;
         $this->apiParas["xid_buyer"] = $xidBuyer;
	}

	public function getXidBuyer(){
	  return $this->xidBuyer;
	}

                            }





        
 

