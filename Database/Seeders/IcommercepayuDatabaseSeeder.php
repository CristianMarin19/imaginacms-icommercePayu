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

        $options['mainimage'] = "";
        $options['merchantId'] = "";
        $options['apilogin'] = "";
        $options['apiKey'] = "";
        $options['accountId'] = "";
        $options['mode'] = "sandbox";
        $options['test'] = 0;
        
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
