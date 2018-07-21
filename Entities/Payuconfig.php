<?php

namespace Modules\IcommercePayu\Entities;

class Payuconfig
{

    private $description;
    private $merchantId;
    private $apilogin;
    private $apiKey;
    private $accountId;
    private $url_action;
    private $currency;
    private $test;
    private $image;
    private $status;

    public function __construct()
    {
        $this->description = setting('icommercePayu::description');
        $this->merchantId = setting('icommercePayu::merchantId');
        $this->apilogin = setting('icommercePayu::apilogin');
        $this->apiKey = setting('icommercePayu::apiKey');
        $this->accountId = setting('icommercePayu::accountId');
        $this->url_action = setting('icommercePayu::url_action');
        $this->currency = setting('icommercePayu::currency');
        $this->test = setting('icommercePayu::test');

        $this->image = setting('icommercePayu::image');
        $this->status = setting('icommercePayu::status');
    }

    public function getData()
    {
        return (object) [
            'description' => $this->description,
            'merchantId' => $this->merchantId,
            'apilogin' => $this->apilogin,
            'apiKey' => $this->apiKey,
            'accountId' => $this->accountId,
            'url_action' => $this->url_action,
            'currency' => $this->currency,
            'test' => $this->test,
            'image' => url($this->image),
            'status' => $this->status
        ];
    }

    /*
    public function setStatusAttribute($value)
    {
    	
    	if($value==="on"){
        	$this->attributes['status'] = 1;
    	}else{
    		$this->attributes['status'] = 0;
    	}

    }
    */

   

}