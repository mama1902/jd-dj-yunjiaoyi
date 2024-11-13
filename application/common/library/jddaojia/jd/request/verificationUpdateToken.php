<?php

class verificationUpdateToken
{

	private $apiParams = array();

    private $path = '';


	public function setOperrams($p)
	{
		$this->apiParams = $p;
	}


    public function setApiPath($path = '')
    {
       $this->path = $path;
    }


	public function getApiPath()
	{
		return  $this->path;
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

