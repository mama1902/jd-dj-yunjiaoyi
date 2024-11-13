<?php
class CtpOrderPushOutPlatFormDataRequest
{


	private $apiParas = array();
	
	public function getApiMethodName(){
	  return "jingdong.ctp.order.pushOutPlatFormData";
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
    private  $protocol;

    public function setProtocol($protocol){
        $this->apiParas['protocol'] = $protocol;
    }
    public function getProtocol(){
        return $this->apiParas['protocol'];
    }
    private  $dataTransmissionParam;

    public function setDataTransmissionParam($dataTransmissionParam){
        $this->apiParas['dataTransmissionParam'] = $dataTransmissionParam;
    }
    public function getDataTransmissionParam(){
        return $this->apiParas['dataTransmissionParam'];
    }
}

?>