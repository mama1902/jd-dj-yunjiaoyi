<?php
class CtpAfsOperateApplyGetReturnTypeRequest
{


	private $apiParas = array();
	
	public function getApiMethodName(){
	  return "jingdong.ctp.afs.operate.apply.getReturnType";
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
    private  $ctpProtocol;

    public function setCtpProtocol($ctpProtocol){
        $this->apiParas['ctpProtocol'] = $ctpProtocol;
    }
    public function getCtpProtocol(){
        return $this->apiParas['ctpProtocol'];
    }
    private  $afsReturnTypeParam;

    public function setAfsReturnTypeParam($afsReturnTypeParam){
        $this->apiParas['afsReturnTypeParam'] = $afsReturnTypeParam;
    }
    public function getAfsReturnTypeParam(){
        return $this->apiParas['afsReturnTypeParam'];
    }
}

?>