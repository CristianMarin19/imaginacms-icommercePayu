<?php

use Illuminate\Routing\Router;

$router->group(['prefix' => 'icommercepayu'], function (Router $router) {
    
    $router->get('/{orderid}', [
        'as' => 'icommercepayu.api.paypal.init',
        'uses' => 'IcommercePayuApiController@init',
    ]);

    $router->get('/method/response', [
        'as' => 'icommercepayu.api.payu.response',
        'uses' => 'IcommercePayuApiController@response',
    ]);

    $router->post('/method/response', [
        'as' => 'icommercepayu.api.payu.response',
        'uses' => 'IcommercePayuApiController@response',
    ]);

});