<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/icommercepayu'], function (Router $router) {
    $router->bind('payuconfig', function ($id) {
        return app('Modules\IcommercePayu\Repositories\PayuconfigRepository')->find($id);
    });
    $router->get('payuconfigs', [
        'as' => 'admin.icommercepayu.payuconfig.index',
        'uses' => 'PayuconfigController@index',
        'middleware' => 'can:icommercepayu.payuconfigs.index'
    ]);
    $router->get('payuconfigs/create', [
        'as' => 'admin.icommercepayu.payuconfig.create',
        'uses' => 'PayuconfigController@create',
        'middleware' => 'can:icommercepayu.payuconfigs.create'
    ]);
    $router->post('payuconfigs', [
        'as' => 'admin.icommercepayu.payuconfig.store',
        'uses' => 'PayuconfigController@store',
        'middleware' => 'can:icommercepayu.payuconfigs.create'
    ]);
    $router->get('payuconfigs/{payuconfig}/edit', [
        'as' => 'admin.icommercepayu.payuconfig.edit',
        'uses' => 'PayuconfigController@edit',
        'middleware' => 'can:icommercepayu.payuconfigs.edit'
    ]);


    $router->put('payuconfigs', [
        'as' => 'admin.icommercepayu.payuconfig.update',
        'uses' => 'PayuconfigController@update',
        'middleware' => 'can:icommercepayu.payuconfigs.edit'
    ]);
    

    $router->delete('payuconfigs/{payuconfig}', [
        'as' => 'admin.icommercepayu.payuconfig.destroy',
        'uses' => 'PayuconfigController@destroy',
        'middleware' => 'can:icommercepayu.payuconfigs.destroy'
    ]);
// append

});
