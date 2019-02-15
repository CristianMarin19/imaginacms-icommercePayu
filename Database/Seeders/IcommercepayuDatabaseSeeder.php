<?php

namespace Modules\Icommercepayu\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Icommerce\Entities\PaymentMethod;

class IcommercepayuDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $options['init'] = "Modules\Icommercepayu\Http\Controllers\Api\IcommercePayuApiController";
        $options['mainimage'] = null;
        $options['merchantId'] = "508029";
        $options['apilogin'] = "pRRXKOl8ikMmt9u";
        $options['apiKey'] = "4Vj8eK4rloUd272L48hsrarnUA";
        $options['accountId'] = "512321";
        $options['mode'] = "sandbox";
        $options['test'] = 1;
        
        $params = array(
            'title' => trans('icommercepayu::icommercepayus.single'),
            'description' => trans('icommercepayu::icommercepayus.description'),
            'name' => config('asgard.icommercepayu.config.paymentName'),
            'status' => 0,
            'options' => $options
        );

        PaymentMethod::create($params);

    }
}
