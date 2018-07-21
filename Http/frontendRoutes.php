<?php

use Illuminate\Routing\Router;

    $router->group(['prefix'=>'icommercepayu'],function (Router $router){
        $locale = LaravelLocalization::setLocale() ?: App::getLocale();

        $router->get('/', [
            'as' => 'icommercepayu',
            'uses' => 'PublicController@index',
        ]);

       
        $router->post('/ok', [
            'as' => 'icommercepayu.ok',
            'uses' => 'PublicController@ok',
        ]);
        
        $router->get('/back', [
            'as' => 'icommercepayu.back',
            'uses' => 'PublicController@back',
        ]);

       
    });