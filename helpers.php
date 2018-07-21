<?php


use Modules\IcommercePayu\Entities\Payuconfig;

if (! function_exists('icommercepayu_get_configuration')) {

    function icommercepayu_get_configuration()
    {

    	$configuration = new Payuconfig();
    	return $configuration->getData();

    }

}

if (! function_exists('icommercepayu_get_entity')) {

	function icommercepayu_get_entity()
    {
    	$entity = new Payuconfig;
    	return $entity;	
    }

}