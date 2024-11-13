<?php
namespace CtpAfsOperateApplyCreateAfsApply;
class PickWareAddress{

    private $params=array();

    function __construct(){
        $this->params["@type"]="com.jd.ctp.aftersales.rpc.jos.apply.AddressInfo";
    }
        
    private $provinceId;
    
    public function setProvinceId($provinceId){
        $this->params['provinceId'] = $provinceId;
    }

    public function getProvinceId(){
        return $this->provinceId;
    }
            
    private $cityId;
    
    public function setCityId($cityId){
        $this->params['cityId'] = $cityId;
    }

    public function getCityId(){
        return $this->cityId;
    }
            
    private $countyId;
    
    public function setCountyId($countyId){
        $this->params['countyId'] = $countyId;
    }

    public function getCountyId(){
        return $this->countyId;
    }
            
    private $townId;
    
    public function setTownId($townId){
        $this->params['townId'] = $townId;
    }

    public function getTownId(){
        return $this->townId;
    }
            
    private $fullAddress;
    
    public function setFullAddress($fullAddress){
        $this->params['fullAddress'] = $fullAddress;
    }

    public function getFullAddress(){
        return $this->fullAddress;
    }
            
    private $addressDetail;
    
    public function setAddressDetail($addressDetail){
        $this->params['addressDetail'] = $addressDetail;
    }

    public function getAddressDetail(){
        return $this->addressDetail;
    }
    
    function getInstance(){
        return $this->params;
    }

}

?>