<?php

namespace Modules\Icommercepayu\Http\Controllers;

// Requests & Response
use Illuminate\Http\Request;
use Illuminate\Http\Response;

// Base
use Modules\Core\Http\Controllers\BasePublicController;

// Repositories
use Modules\Icommercepayu\Repositories\IcommercePayuRepository;

use Modules\Icommerce\Repositories\PaymentMethodRepository;
use Modules\Icommerce\Repositories\TransactionRepository;
use Modules\Icommerce\Repositories\OrderRepository;
use Modules\Icommerce\Repositories\CurrencyRepository;

// Entities
use Modules\Icommercepayu\Entities\PayU;


class PublicController extends BasePublicController
{
  
    private $icommercepayu;
    private $paymentMethod;
    private $order;
    private $transaction;
    private $currency;

    private $payu;
    protected $urlSandbox;
    protected $urlProduction;

    public function __construct(
        IcommercePayuRepository $icommercepayu,
        PaymentMethodRepository $paymentMethod,
        OrderRepository $order,
        TransactionRepository $transaction,
        CurrencyRepository $currency
    )
    {
        $this->icommercepayu = $icommercepayu;
        $this->paymentMethod = $paymentMethod;
        $this->order = $order;
        $this->transaction = $transaction;
        $this->currency = $currency;

        $this->urlSandbox = "https://sandbox.gateway.payulatam.com/ppp-web-gateway/";
        $this->urlProduction = "https://checkout.payulatam.com/ppp-web-gateway-payu/";
    }


    /**
     * Index data
     * @param Requests request
     * @return route
     */
    public function index($eURL){

        try {

            // Decr
            $infor = $this->icommercepayu->decriptUrl($eURL);
            $orderID = $infor[0];
            $transactionID = $infor[1];
            $currencyID = $infor[2];

            \Log::info('Module Icommercepayu: Index-ID:'.$orderID);
            
            // Validate get data
            $order = $this->order->find($orderID);
            $transaction = $this->transaction->find($transactionID);
            $currency = $this->currency->find($currencyID);

            $paymentName = config('asgard.icommercepayu.config.paymentName');

            // Configuration
            $attribute = array('name' => $paymentName);
            $paymentMethod = $this->paymentMethod->findByAttributes($attribute);

            // Order
            $order = $this->order->find($orderID);
            
            $restDescription = "Order:{$orderID} - {$order->email}";

            // OrderID Method
            $orderID = $order->id."-".$transaction->id;

            // Payu generate
            $payU = new PayU();

            if($paymentMethod->options->mode=="sandbox")
                $payU->setUrlgate($this->urlSandbox);
            else
                $payU->setUrlgate($this->urlProduction);

            $payU->setMerchantid($paymentMethod->options->merchantId);
            $payU->setAccountid($paymentMethod->options->accountId);
            $payU->setApikey($paymentMethod->options->apiKey);
            $payU->setReferenceCode($orderID); // OrderID
            $payU->setDescription($restDescription); //DESCRIPCION
            $payU->setAmount($order->total);
            $payU->setCurrency($currency->code);
            $payU->setTax(0); // 0 valor del impuesto asociado a la venta
            $payU->setTaxReturnBase(0); // 0 valor de devolución del impuesto
            $payU->setTest($paymentMethod->options->test);
            $payU->setLng(locale()); // Idioma
            $payU->setBuyerEmail($order->email);
            $payU->setConfirmationUrl(Route("icommercepayu.api.payu.response"));
            $payU->setResponseUrl(Route("icommercepayu.back"));
            
            $payU->executeRedirection();
            
           
            //========= Testing
            /*
            $client = new \GuzzleHttp\Client();

            $signature = $this->setSignature($paymentMethod->options->apiKey,$paymentMethod->options->merchantId,$orderID,$order->total,$currency->code);
           
            $res = $client->request('GET', $this->urlSandbox, [
                'form_params' => [
                    'merchantId' => $paymentMethod->options->merchantId,
                    'accountId' => $paymentMethod->options->accountId,
                    'description' => $restDescription,
                    'referenceCode' => $orderID,
                    'amount' => $order->total,
                    'tax' => 0,
                    'taxReturnBase' => 0,
                    'currency' => $currency->code,
                    'lng' => locale(),
                    'test' => $paymentMethod->options->test,
                    'buyerEmail' => $order->email,
                    'signature' => $signature,
                    'responseUrl' => Route("icommercepayu.back"),
                    'confirmationUrl' => Route("icommercepayu.api.payu.response")
                ]
            ]);

            dd($res);
            */
            
            

        } catch (\Exception $e) {

            \Log::error('Module Icommercepayu-Index: Message: '.$e->getMessage());
            \Log::error('Module Icommercepayu-Index: Code: '.$e->getCode());

            //Message Error
            $status = 500;
            $response = [
              'errors' => $e->getMessage(),
              'code' => $e->getCode()
            ];

            //return response()->json($response, $status ?? 200);

            return redirect()->route("icommercepayu.api.payu.response");

        }
   
    }


     /**
     * Button Back PayU
     * @param  Request $request
     * @return redirect
     */
    public function back(Request $request){

        if(isset($request->referenceCode)){

            $referenceSale = explode('-',$request->referenceCode);
            $order = $this->order->find($referenceSale[0]);

            if (!empty($order))
                return redirect()->route('icommerce.order.showorder', [$order->id, $order->key]);
            else
                return redirect()->route('homepage');
                 

        }else{
            return redirect()->route('homepage');
        }
       
    }
    
    /*
    public function setSignature($apiKey,$merchantId,$referenceCode,$amount,$currency){

        return md5($apiKey."~".$merchantId."~".$referenceCode."~".$amount.'~'.$currency);
    
    }
    */

   
}