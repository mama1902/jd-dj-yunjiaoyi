<?php
class CtpDataCommitResultRequest
{


	private $apiParas = array();
	
	public function getApiMethodName(){
	  return "jingdong.ctp.data.commitResult";
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
                                                        		                                    	                   			private $viewerNumsOver1min;
    	                                                                                    
	public function setViewerNumsOver1min($viewerNumsOver1min){
		$this->viewerNumsOver1min = $viewerNumsOver1min;
         $this->apiParas["viewer_nums_over_1min"] = $viewerNumsOver1min;
	}

	public function getViewerNumsOver1min(){
	  return $this->viewerNumsOver1min;
	}

                        	                   			private $playTimes25;
    	                                                                        
	public function setPlayTimes25($playTimes25){
		$this->playTimes25 = $playTimes25;
         $this->apiParas["play_times_25"] = $playTimes25;
	}

	public function getPlayTimes25(){
	  return $this->playTimes25;
	}

                        	                   			private $playTimesCompleted;
    	                                                                        
	public function setPlayTimesCompleted($playTimesCompleted){
		$this->playTimesCompleted = $playTimesCompleted;
         $this->apiParas["play_times_completed"] = $playTimesCompleted;
	}

	public function getPlayTimesCompleted(){
	  return $this->playTimesCompleted;
	}

                        	                   			private $qcPlanId;
    	                                                                        
	public function setQcPlanId($qcPlanId){
		$this->qcPlanId = $qcPlanId;
         $this->apiParas["qc_plan_id"] = $qcPlanId;
	}

	public function getQcPlanId(){
	  return $this->qcPlanId;
	}

                        	                   			private $orderCustPrice;
    	                                                                        
	public function setOrderCustPrice($orderCustPrice){
		$this->orderCustPrice = $orderCustPrice;
         $this->apiParas["order_cust_price"] = $orderCustPrice;
	}

	public function getOrderCustPrice(){
	  return $this->orderCustPrice;
	}

                        	                   			private $clickRatio;
    	                                                            
	public function setClickRatio($clickRatio){
		$this->clickRatio = $clickRatio;
         $this->apiParas["click_ratio"] = $clickRatio;
	}

	public function getClickRatio(){
	  return $this->clickRatio;
	}

                        	                   			private $orderOrdGmv;
    	                                                                        
	public function setOrderOrdGmv($orderOrdGmv){
		$this->orderOrdGmv = $orderOrdGmv;
         $this->apiParas["order_ord_gmv"] = $orderOrdGmv;
	}

	public function getOrderOrdGmv(){
	  return $this->orderOrdGmv;
	}

                        	                   			private $viewerSkuClickTimes;
    	                                                                                    
	public function setViewerSkuClickTimes($viewerSkuClickTimes){
		$this->viewerSkuClickTimes = $viewerSkuClickTimes;
         $this->apiParas["viewer_sku_click_times"] = $viewerSkuClickTimes;
	}

	public function getViewerSkuClickTimes(){
	  return $this->viewerSkuClickTimes;
	}

                        	                   			private $viewerCommentTimes;
    	                                                                        
	public function setViewerCommentTimes($viewerCommentTimes){
		$this->viewerCommentTimes = $viewerCommentTimes;
         $this->apiParas["viewer_comment_times"] = $viewerCommentTimes;
	}

	public function getViewerCommentTimes(){
	  return $this->viewerCommentTimes;
	}

                        	                   			private $transNum;
    	                                                            
	public function setTransNum($transNum){
		$this->transNum = $transNum;
         $this->apiParas["trans_num"] = $transNum;
	}

	public function getTransNum(){
	  return $this->transNum;
	}

                        	                   			private $viewerYinglangAmount;
    	                                                                        
	public function setViewerYinglangAmount($viewerYinglangAmount){
		$this->viewerYinglangAmount = $viewerYinglangAmount;
         $this->apiParas["viewer_yinglang_amount"] = $viewerYinglangAmount;
	}

	public function getViewerYinglangAmount(){
	  return $this->viewerYinglangAmount;
	}

                        	                   			private $playTimes50;
    	                                                                        
	public function setPlayTimes50($playTimes50){
		$this->playTimes50 = $playTimes50;
         $this->apiParas["play_times_50"] = $playTimes50;
	}

	public function getPlayTimes50(){
	  return $this->playTimes50;
	}

                        	                   			private $playTimes75;
    	                                                                        
	public function setPlayTimes75($playTimes75){
		$this->playTimes75 = $playTimes75;
         $this->apiParas["play_times_75"] = $playTimes75;
	}

	public function getPlayTimes75(){
	  return $this->playTimes75;
	}

                        	                   			private $transCost;
    	                                                            
	public function setTransCost($transCost){
		$this->transCost = $transCost;
         $this->apiParas["trans_cost"] = $transCost;
	}

	public function getTransCost(){
	  return $this->transCost;
	}

                        	                   			private $cost;
    	                        
	public function setCost($cost){
		$this->cost = $cost;
         $this->apiParas["cost"] = $cost;
	}

	public function getCost(){
	  return $this->cost;
	}

                        	                   			private $tShowCost;
    	                                                                        
	public function setTShowCost($tShowCost){
		$this->tShowCost = $tShowCost;
         $this->apiParas["t_show_cost"] = $tShowCost;
	}

	public function getTShowCost(){
	  return $this->tShowCost;
	}

                        	                   			private $playTimes3second;
    	                                                                        
	public function setPlayTimes3second($playTimes3second){
		$this->playTimes3second = $playTimes3second;
         $this->apiParas["play_times_3second"] = $playTimes3second;
	}

	public function getPlayTimes3second(){
	  return $this->playTimes3second;
	}

                        	                   			private $exoDeviceId;
    	                                                                        
	public function setExoDeviceId($exoDeviceId){
		$this->exoDeviceId = $exoDeviceId;
         $this->apiParas["exo_device_id"] = $exoDeviceId;
	}

	public function getExoDeviceId(){
	  return $this->exoDeviceId;
	}

                        	                   			private $jhPlanId;
    	                                                                        
	public function setJhPlanId($jhPlanId){
		$this->jhPlanId = $jhPlanId;
         $this->apiParas["jh_plan_id"] = $jhPlanId;
	}

	public function getJhPlanId(){
	  return $this->jhPlanId;
	}

                        	                   			private $showTimes;
    	                                                            
	public function setShowTimes($showTimes){
		$this->showTimes = $showTimes;
         $this->apiParas["show_times"] = $showTimes;
	}

	public function getShowTimes(){
	  return $this->showTimes;
	}

                        	                   			private $viewerNew;
    	                                                            
	public function setViewerNew($viewerNew){
		$this->viewerNew = $viewerNew;
         $this->apiParas["viewer_new"] = $viewerNew;
	}

	public function getViewerNew(){
	  return $this->viewerNew;
	}

                        	                   			private $cliDeviceId;
    	                                                                        
	public function setCliDeviceId($cliDeviceId){
		$this->cliDeviceId = $cliDeviceId;
         $this->apiParas["cli_device_id"] = $cliDeviceId;
	}

	public function getCliDeviceId(){
	  return $this->cliDeviceId;
	}

                        	                   			private $shareTimes;
    	                                                            
	public function setShareTimes($shareTimes){
		$this->shareTimes = $shareTimes;
         $this->apiParas["share_times"] = $shareTimes;
	}

	public function getShareTimes(){
	  return $this->shareTimes;
	}

                        	                   			private $orderOrdNums;
    	                                                                        
	public function setOrderOrdNums($orderOrdNums){
		$this->orderOrdNums = $orderOrdNums;
         $this->apiParas["order_ord_nums"] = $orderOrdNums;
	}

	public function getOrderOrdNums(){
	  return $this->orderOrdNums;
	}

                        	                   			private $viewerShoppingCart;
    	                                                                        
	public function setViewerShoppingCart($viewerShoppingCart){
		$this->viewerShoppingCart = $viewerShoppingCart;
         $this->apiParas["viewer_shopping_cart"] = $viewerShoppingCart;
	}

	public function getViewerShoppingCart(){
	  return $this->viewerShoppingCart;
	}

                        	                   			private $likeTimes;
    	                                                            
	public function setLikeTimes($likeTimes){
		$this->likeTimes = $likeTimes;
         $this->apiParas["like_times"] = $likeTimes;
	}

	public function getLikeTimes(){
	  return $this->likeTimes;
	}

                        	                   			private $playTimes;
    	                                                            
	public function setPlayTimes($playTimes){
		$this->playTimes = $playTimes;
         $this->apiParas["play_times"] = $playTimes;
	}

	public function getPlayTimes(){
	  return $this->playTimes;
	}

                        	                   			private $dealOrdRoi;
    	                                                                        
	public function setDealOrdRoi($dealOrdRoi){
		$this->dealOrdRoi = $dealOrdRoi;
         $this->apiParas["deal_ord_roi"] = $dealOrdRoi;
	}

	public function getDealOrdRoi(){
	  return $this->dealOrdRoi;
	}

                        	                   			private $viewerRewardTimes;
    	                                                                        
	public function setViewerRewardTimes($viewerRewardTimes){
		$this->viewerRewardTimes = $viewerRewardTimes;
         $this->apiParas["viewer_reward_times"] = $viewerRewardTimes;
	}

	public function getViewerRewardTimes(){
	  return $this->viewerRewardTimes;
	}

                        	                   			private $clickTimes;
    	                                                            
	public function setClickTimes($clickTimes){
		$this->clickTimes = $clickTimes;
         $this->apiParas["click_times"] = $clickTimes;
	}

	public function getClickTimes(){
	  return $this->clickTimes;
	}

                        	                   			private $orderOrdRoi;
    	                                                                        
	public function setOrderOrdRoi($orderOrdRoi){
		$this->orderOrdRoi = $orderOrdRoi;
         $this->apiParas["order_ord_roi"] = $orderOrdRoi;
	}

	public function getOrderOrdRoi(){
	  return $this->orderOrdRoi;
	}

                        	                   			private $dealOrdGmv;
    	                                                                        
	public function setDealOrdGmv($dealOrdGmv){
		$this->dealOrdGmv = $dealOrdGmv;
         $this->apiParas["deal_ord_gmv"] = $dealOrdGmv;
	}

	public function getDealOrdGmv(){
	  return $this->dealOrdGmv;
	}

                        	                   			private $transRatio;
    	                                                            
	public function setTransRatio($transRatio){
		$this->transRatio = $transRatio;
         $this->apiParas["trans_ratio"] = $transRatio;
	}

	public function getTransRatio(){
	  return $this->transRatio;
	}

                        	                   			private $dealOrdNums;
    	                                                                        
	public function setDealOrdNums($dealOrdNums){
		$this->dealOrdNums = $dealOrdNums;
         $this->apiParas["deal_ord_nums"] = $dealOrdNums;
	}

	public function getDealOrdNums(){
	  return $this->dealOrdNums;
	}

                        	                   			private $viewerNums;
    	                                                            
	public function setViewerNums($viewerNums){
		$this->viewerNums = $viewerNums;
         $this->apiParas["viewer_nums"] = $viewerNums;
	}

	public function getViewerNums(){
	  return $this->viewerNums;
	}

                        	                   			private $commentTimes;
    	                                                            
	public function setCommentTimes($commentTimes){
		$this->commentTimes = $commentTimes;
         $this->apiParas["comment_times"] = $commentTimes;
	}

	public function getCommentTimes(){
	  return $this->commentTimes;
	}

                        	                   			private $viewerShareTimes;
    	                                                                        
	public function setViewerShareTimes($viewerShareTimes){
		$this->viewerShareTimes = $viewerShareTimes;
         $this->apiParas["viewer_share_times"] = $viewerShareTimes;
	}

	public function getViewerShareTimes(){
	  return $this->viewerShareTimes;
	}

                        	                   			private $planApply;
    	                                                            
	public function setPlanApply($planApply){
		$this->planApply = $planApply;
         $this->apiParas["plan_apply"] = $planApply;
	}

	public function getPlanApply(){
	  return $this->planApply;
	}

                            }





        
 

