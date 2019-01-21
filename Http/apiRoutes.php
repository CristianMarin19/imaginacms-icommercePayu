<?php

use Illuminate\Routing\Router;

$router->group(['prefix' => 'icommercepayu'], function (Router $router) {
    
    $router->get('/', [
        'as' => 'icommercepayu.api.payu.init',
        'uses' => 'IcommercePayuApiController@init',
    ]);

    $router->get('/response', [
        'as' => 'icommercepayu.api.payu.response',
        'uses' => 'IcommercePayuApiController@response',
    ]);

    $router->post('/response', [
        'as' => 'icommercepayu.api.payu.response',
        'uses' => 'IcommercePayuApiController@response',
    ]);

});