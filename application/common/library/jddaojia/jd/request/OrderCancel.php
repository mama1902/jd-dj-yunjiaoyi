<?php

class OrderCancel
{
    private $orderId;	
    private $operPin;	
    private $operRemark;	
    private $operTime;	
	private $apiParams = array();

	public function setOrderId($orderId)
	{
		$this->orderId = $orderId;
		$this->apiParams["orderId"] = $orderId;
	}
	public function setOperPin($operPin)
	{
		$this->operPin = $operPin;
		$this->apiParams["operPin"] = $operPin;
	}
	public function setOperRemark($operRemark)
	{
		$this->operRemark = $operRemark;
		$this->apiParams["operRemark"] = $operRemark;
	}
	public function setOperTime($operTime)
	{
		$this->operTime = $operTime;
		$this->apiParams["operTime"] = $operTime;
	}
	public function getApiPath()
	{
		return "/orderStatus/cancelAndRefund";
	}

	public function getApiParas()
	{
		return $this->apiParams;
	}
/**
 *检查参数是否正确，是否满足平台规范。根据业务需求和文档规范自行书写
 */
	public function check()
	{
		return true;
	}
}

