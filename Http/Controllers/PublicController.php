<?php

namespace Modules\Icommercepayu\Http\Controllers;

use Mockery\CountValidator\Exception;

use Modules\Icommercepayu\Entities\PayU;
use Modules\Icommercepayu\Entities\Payuconfig;

use Modules\Core\Http\Controllers\BasePublicController;
use Route;
use Session;

use Modules\User\Contracts\Authentication;
use Modules\User\Repositories\UserRepository;
use Modules\Icommerce\Repositories\CurrencyRepository;
use Modules\Icommerce\Repositories\ProductRepository;
use Modules\Icommerce\Repositories\OrderRepository;
use Modules\Icommerce\Repositories\Order_ProductRepository;
use Modules\Setting\Contracts\Setting;
use Illuminate\Http\Request as Requests;
use Illuminate\Support\Facades\Log;



class PublicController extends BasePublicController
{
  
    private $order;
    private $setting;
    private $user;
    protected $auth;
    protected $payU;

    protected $urlSandbox;
    protected $urlProduction;

    public function __construct(Setting $setting, Authentication $auth, UserRepository $user,  OrderRepository $order)
    {

        $this->setting = $setting;
        $this->auth = $auth;
        $this->user = $user;
        $this->order = $order;

        $this->urlSandbox = "https://sandbox.gateway.payulatam.com/ppp-web-gateway/";
        $this->urlProduction = "https://checkout.payulatam.com/ppp-web-gateway-payu/";

    }

    /**
     * Go to the payment
     * @param Requests request
     * @return redirect payment 
     */
    public function index(Requests $request)
    {

        if($request->session()->exists('orderID')) {

            $orderID = session('orderID');
            $order = $this->order->find($orderID);

            $restDescription = "Order:{$orderID} - {$order->email}";
           
            $config = new Payuconfig();
            $config = $config->getData();

            try {

                $payU = new PayU();

                if($config->url_action==0)
                    $payU->setUrlgate($this->urlSandbox);
                else
                    $payU->setUrlgate($this->urlProduction);


                $payU->setMerchantid($config->merchantId);
                $payU->setAccountid($config->accountId);
                $payU->setApikey($config->apiKey);
                   
                $orderID = $orderID."-".time();
                
                $payU->setReferenceCode($orderID); // OrderID
                $payU->setDescription($restDescription); //DESCRIPCION
                $payU->setAmount($order->total);
                $payU->setCurrency($config->currency);
                $payU->setTax(0); // 0 valor del impuesto asociado a la venta
                $payU->setTaxReturnBase(0); // 0 valor de devolución del impuesto
                $payU->setTest($config->test);
                $payU->setLng(locale()); // Idioma
                $payU->setBuyerEmail($order->email);

                $payU->setConfirmationUrl(Route("icommercepayu.ok"));
                $payU->setResponseUrl(Route("icommercepayu.back"));

                $payU->executeRedirection();

                
            } catch (Exception $e) {
                echo $e->getMessage();
            }


        }else{
           return redirect()->route('homepage');
        }

    }

     /**
     * Confirmation Page
     * @param Requests request
     * @return response
     */
    public function ok(Requests $request)
    {

        //Log::info('Respuesta Confirmacion PayU: Recibida '.time());
        if(isset($request->reference_sale)){

            $referenceSale = explode('-',$request->reference_sale);
            $order = $this->order->find($referenceSale[0]);


            // Not PROCESSED and CANCELED
            if($order->order_status!=12 && $order->order_status!=2){

                $email_from = $this->setting->get('icommerce::from-email');
                $email_to = explode(',',$this->setting->get('icommerce::form-emails'));
                $sender  = $this->setting->get('core::site-name');
              
                $config = new Payuconfig();
                $config = $config->getData();
                
                $products=[];
                
                foreach ($order->products as $product) {
                    array_push($products,[
                        "title" => $product->title,
                        "sku" => $product->sku,
                        "quantity" => $product->pivot->quantity,
                        "price" => $product->pivot->price,
                        "total" => $product->pivot->total,
                    ]);
                }

                $userEmail = $order->email;
                $userFirstname = "{$order->first_name} {$order->last_name}";
               
                try {
                    
                    //Log::info('Order esta Pendiente -'.$referenceSale[0]);

                    $signature = $this->signatureGeneration($config->apiKey,$request->merchant_id,$request->reference_sale,$request->value,$request->currency,$request->state_pol);

                    if (strtoupper($signature) == strtoupper($request->sign)) {

                        $transactionState = $request->state_pol;
                        $polResponseCode = $request->response_code_pol;

                        if($transactionState == 6 && $polResponseCode == 5){

                            $success_process = icommerce_executePostOrder($referenceSale[0],6,$request);
                            $msjTheme = "icommerce::email.error_order";
                            $msjSubject = trans('icommerce::common.emailSubject.failed')."- Order:".$order->id;
                            $msjIntro = trans('icommerce::common.emailIntro.failed');
                           
                           
                        } else if($transactionState == 6 && $polResponseCode == 4){ 

                            $success_process = icommerce_executePostOrder($referenceSale[0],7,$request);
                            $msjTheme = "icommerce::email.error_order";
                            $msjSubject = trans('icommerce::common.emailSubject.refunded')."- Order:".$order->id;
                            $msjIntro = trans('icommerce::common.emailIntro.refunded');
                            
                        } else if($transactionState == 12 && $polResponseCode == 9994){

                            $success_process = icommerce_executePostOrder($referenceSale[0],10,$request);
                            $msjTheme = "icommerce::email.error_order";
                            $msjSubject = trans('icommerce::common.emailSubject.pending')."- Order:".$order->id;
                            $msjIntro = trans('icommerce::common.emailIntro.pending');

                        } else if($transactionState == 4 && $polResponseCode == 1){

                            $success_process = icommerce_executePostOrder($referenceSale[0],1,$request);
                            $msjTheme = "icommerce::email.success_order";
                            $msjSubject = trans('icommerce::common.emailSubject.complete')."- Order:".$order->id;
                            $msjIntro = trans('icommerce::common.emailIntro.complete');
                           
                        }else{ 

                            $success_process = icommerce_executePostOrder($referenceSale[0],6,$request);
                            $msjTheme = "icommerce::email.error_order";
                            $msjSubject = trans('icommerce::common.emailSubject.failed')."- Order:".$order->id;
                            $msjIntro = trans('icommerce::common.emailIntro.failed');
                        }
                        
                        $order = $this->order->find($referenceSale[0]);

                        $content=[
                            'order'=>$order,
                            'products' => $products,
                            'user' => $userFirstname
                        ];

                        icommerce_emailSend(['email_from'=>[$email_from],'theme' => $msjTheme,'email_to' => $request->email_buyer,'subject' => $msjSubject, 'sender'=>$sender,'data' => array('title' => $msjSubject,'intro'=> $msjIntro,'content'=>$content)]);
                        
                        icommerce_emailSend(['email_from'=>[$email_from],'theme' => $msjTheme,'email_to' => $email_to,'subject' => $msjSubject, 'sender'=>$sender,'data' => array('title' => $msjSubject,'intro'=> $msjIntro,'content'=>$content)]);
                        
                        return response('Correcto', 200);

                    }else{

                        $success_process = icommerce_executePostOrder($referenceSale[0],6,$request);

                        $msjTheme = "icommerce::email.error_order";
                        $msjSubject = trans('icommerce::common.payuSubject.signError')."- Order:".$order->id;
                        $msjIntro = trans('icommerce::common.payuIntro.signError');

                        $order = $this->order->find($referenceSale[0]);
                        
                        $content=[
                            'order'=>$order,
                            'products' => $products,
                            'user' => $userFirstname
                        ];
                        
                        icommerce_emailSend(['email_from'=>[$email_from],'theme' => $msjTheme ,'email_to' => [$userEmail],'subject' => $msjSubject, 'sender'=>$sender, 'data' => array('title' => $msjSubject,'intro'=>$msjIntro,'content'=>$content,)]);
                        

                        icommerce_emailSend(['email_from'=>[$email_from],'theme' => $msjTheme ,'email_to' => $email_to,'subject' => $msjSubject, 'sender'=>$sender, 'data' => array('title' => $msjSubject,'intro'=>$msjIntro,'content'=>$content,)]);
                        
                    }
                    
                }catch (Exception $e) {

                    Log::info('Error en Exception'.time());
                    //echo $e->getMessage();
                }
           
            }

            return response('Correcto', 200);

        }// If reference request
    }

    /**
     * Generate Signature (from function ok)
     * @param   string        $apikey
     * @return $signature
     */
    public function signatureGeneration($apiKey,$merchantId,$referenceSale,$new_value,$currency,$state_pol){
        
        $split = explode('.', $new_value);
        $decimals = $split[1];

        if ($decimals % 10 == 0) {
            $value = number_format($new_value, 1, '.', '');
        }else{
            $value = $new_value;
        }

        $signature_local = $apiKey.'~'.$merchantId.'~'.$referenceSale.'~'.$value.'~'.$currency.'~'.$state_pol;

        $signature_md5 = md5($signature_local);

        return $signature_md5;
        
    }

     /**
     * Button Back PayU
     * @param  Request $request
     * @return redirect
     */
    public function back(Requests $request){

        if(isset($request->referenceCode)){

            $referenceSale = explode('-',$request->referenceCode);
            $order = $this->order->find($referenceSale[0]);

            $user = $this->auth->user();

            if (isset($user) && !empty($user))
              if (!empty($order))
                return redirect()->route('icommerce.orders.show', [$order->id]);
              else
                return redirect()->route('homepage')
                  ->withSuccess(trans('icommerce::common.order_success'));
            else
              if (!empty($order))
                return redirect()->route('icommerce.order.showorder', [$order->id, $order->key]);
              else
                return redirect()->route('homepage')
                  ->withSuccess(trans('icommerce::common.order_success'));

        }else{
            return redirect()->route('homepage');
        }
       
    }

   
}