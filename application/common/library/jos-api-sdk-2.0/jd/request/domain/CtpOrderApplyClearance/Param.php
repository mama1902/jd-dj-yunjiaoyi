<?php
namespace CtpOrderApplyClearance;
class Param{

    private $params=array();

    function __construct(){
        $this->params["@type"]="com.jd.ctp.order.api.param.ApiClearanceParam";
    }
        
    private $ebpName;
    
    public function setEbpName($ebpName){
        $this->params['ebpName'] = $ebpName;
    }

    public function getEbpName(){
        return $this->ebpName;
    }
            
    private $isSupervise;
    
    public function setIsSupervise($isSupervise){
        $this->params['isSupervise'] = $isSupervise;
    }

    public function getIsSupervise(){
        return $this->isSupervise;
    }
            
    private $orderId;
    
    public function setOrderId($orderId){
        $this->params['orderId'] = $orderId;
    }

    public function getOrderId(){
        return $this->orderId;
    }
            
    private $buyerIdType;
    
    public function setBuyerIdType($buyerIdType){
        $this->params['buyerIdType'] = $buyerIdType;
    }

    public function getBuyerIdType(){
        return $this->buyerIdType;
    }
            
    private $inspectionEcpCode;
    
    public function setInspectionEcpCode($inspectionEcpCode){
        $this->params['inspectionEcpCode'] = $inspectionEcpCode;
    }

    public function getInspectionEcpCode(){
        return $this->inspectionEcpCode;
    }
            
    private $freight;
    
    public function setFreight($freight){
        $this->params['freight'] = $freight;
    }

    public function getFreight(){
        return $this->freight;
    }
            
    private $taxTotal;
    
    public function setTaxTotal($taxTotal){
        $this->params['taxTotal'] = $taxTotal;
    }

    public function getTaxTotal(){
        return $this->taxTotal;
    }
            
    private $discount;
    
    public function setDiscount($discount){
        $this->params['discount'] = $discount;
    }

    public function getDiscount(){
        return $this->discount;
    }
            
    private $consName;
    
    public function setConsName($consName){
        $this->params['consName'] = $consName;
    }

    public function getConsName(){
        return $this->consName;
    }
            
    private $orderSum;
    
    public function setOrderSum($orderSum){
        $this->params['orderSum'] = $orderSum;
    }

    public function getOrderSum(){
        return $this->orderSum;
    }
            
    private $apiClearanceSkuInfoList;
                                        
    public function setApiClearanceSkuInfoList($apiClearanceSkuInfoList){
        $size = count($apiClearanceSkuInfoList);
        for ($i=0; $i<$size; $i++){
            $apiClearanceSkuInfoList [$i] = $apiClearanceSkuInfoList [$i] ->getInstance();
        }
        $this->params['apiClearanceSkuInfoList'] = $apiClearanceSkuInfoList;
    }
                                    
            
    private $buyerIdNumber;
    
    public function setBuyerIdNumber($buyerIdNumber){
        $this->params['buyerIdNumber'] = $buyerIdNumber;
    }

    public function getBuyerIdNumber(){
        return $this->buyerIdNumber;
    }
            
    private $insuredFee;
    
    public function setInsuredFee($insuredFee){
        $this->params['insuredFee'] = $insuredFee;
    }

    public function getInsuredFee(){
        return $this->insuredFee;
    }
            
    private $ebpCode;
    
    public function setEbpCode($ebpCode){
        $this->params['ebpCode'] = $ebpCode;
    }

    public function getEbpCode(){
        return $this->ebpCode;
    }
            
    private $currency;
    
    public function setCurrency($currency){
        $this->params['currency'] = $currency;
    }

    public function getCurrency(){
        return $this->currency;
    }
            
    private $payName;
    
    public function setPayName($payName){
        $this->params['payName'] = $payName;
    }

    public function getPayName(){
        return $this->payName;
    }
            
    private $delivery;
    
    public function setDelivery($delivery){
        $this->params['delivery'] = $delivery;
    }

    public function getDelivery(){
        return $this->delivery;
    }
            
    private $buyerRegNo;
    
    public function setBuyerRegNo($buyerRegNo){
        $this->params['buyerRegNo'] = $buyerRegNo;
    }

    public function getBuyerRegNo(){
        return $this->buyerRegNo;
    }
            
    private $consPhone;
    
    public function setConsPhone($consPhone){
        $this->params['consPhone'] = $consPhone;
    }

    public function getConsPhone(){
        return $this->consPhone;
    }
            
    private $orderCreateTime;
    
    public function setOrderCreateTime($orderCreateTime){
        $this->params['orderCreateTime'] = $orderCreateTime;
    }

    public function getOrderCreateTime(){
        return $this->orderCreateTime;
    }
            
    private $shouldPay;
    
    public function setShouldPay($shouldPay){
        $this->params['shouldPay'] = $shouldPay;
    }

    public function getShouldPay(){
        return $this->shouldPay;
    }
            
    private $consAddress;
    
    public function setConsAddress($consAddress){
        $this->params['consAddress'] = $consAddress;
    }

    public function getConsAddress(){
        return $this->consAddress;
    }
            
    private $discountNote;
    
    public function setDiscountNote($discountNote){
        $this->params['discountNote'] = $discountNote;
    }

    public function getDiscountNote(){
        return $this->discountNote;
    }
            
    private $buyerName;
    
    public function setBuyerName($buyerName){
        $this->params['buyerName'] = $buyerName;
    }

    public function getBuyerName(){
        return $this->buyerName;
    }
            
    private $paymentNo;
    
    public function setPaymentNo($paymentNo){
        $this->params['paymentNo'] = $paymentNo;
    }

    public function getPaymentNo(){
        return $this->paymentNo;
    }
            
    private $outTradeId;
    
    public function setOutTradeId($outTradeId){
        $this->params['outTradeId'] = $outTradeId;
    }

    public function getOutTradeId(){
        return $this->outTradeId;
    }
            
    private $buyerPhone;
    
    public function setBuyerPhone($buyerPhone){
        $this->params['buyerPhone'] = $buyerPhone;
    }

    public function getBuyerPhone(){
        return $this->buyerPhone;
    }
            
    private $inspectionEcpName;
    
    public function setInspectionEcpName($inspectionEcpName){
        $this->params['inspectionEcpName'] = $inspectionEcpName;
    }

    public function getInspectionEcpName(){
        return $this->inspectionEcpName;
    }
            
    private $payCode;
    
    public function setPayCode($payCode){
        $this->params['payCode'] = $payCode;
    }

    public function getPayCode(){
        return $this->payCode;
    }
            
    private $payMethod;
    
    public function setPayMethod($payMethod){
        $this->params['payMethod'] = $payMethod;
    }

    public function getPayMethod(){
        return $this->payMethod;
    }
            
    private $ebcCode;
    
    public function setEbcCode($ebcCode){
        $this->params['ebcCode'] = $ebcCode;
    }

    public function getEbcCode(){
        return $this->ebcCode;
    }
            
    private $ebcName;
    
    public function setEbcName($ebcName){
        $this->params['ebcName'] = $ebcName;
    }

    public function getEbcName(){
        return $this->ebcName;
    }
    
    function getInstance(){
        return $this->params;
    }

}

?>