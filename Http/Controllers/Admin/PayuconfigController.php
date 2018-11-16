<?php

namespace Modules\Icommercepayu\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Icommercepayu\Entities\Payuconfig;
use Modules\Icommercepayu\Http\Requests\CreatePayuconfigRequest;
use Modules\Icommercepayu\Http\Requests\UpdatePayuconfigRequest;
use Modules\Icommercepayu\Repositories\PayuconfigRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Setting\Repositories\SettingRepository;

class PayuconfigController extends AdminBaseController
{
    /**
     * @var PayuconfigRepository
     */
    private $payuconfig;
    private $setting;

    public function __construct(PayuconfigRepository $payuconfig,SettingRepository $setting)
    {
        parent::__construct();
        $this->payuconfig = $payuconfig;
        $this->setting=$setting;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreatePayuconfigRequest $request
     * @return Response
     */
    public function store(CreatePayuconfigRequest $request)
    {

       
    }

   

    /**
     * Update the specified resource in storage.
     *
     * @param  Payuconfig $payuconfig
     * @param  UpdatePayuconfigRequest $request
     * @return Response
     */
    public function update(Payuconfig $payuconfig, UpdatePayuconfigRequest $request)
    {   

        //dd($request);

        if($request->status=='on')
            $request['status'] = "1";
        else
            $request['status'] = "0";

        $data = $request->all();
        $token =$data['_token'];
        $requestimage =$data['mainimage'];

        unset($data['_token']);
        unset($data['mainimage']);
        unset($data['_method']);
        unset($data['locale']);

        $newData['_token'] = $token;//Add token first

        if(($requestimage==NULL) || (!empty($requestimage)) )
            $requestimage = $this->saveImage($requestimage,"assets/icommercepayu/1.jpg");

        foreach ($data as $key => $val)
            $newData['icommercepayu::'.$key ] = $val;

        $newData['icommercepayu::image'] = $requestimage;

        $s = $this->setting->createOrUpdate($newData);

        return redirect()->route('admin.icommerce.payment.index')
            ->withSuccess(trans('core::core.messages.resource updated', ['name' => trans('icommercepayu::payuconfigs.title.payuconfigs')]));
    }

    public function saveImage($value,$destination_path)
    {

        $disk = "publicmedia";

        //Defined return.
        if(ends_with($value,'.jpg')) {
            return $value;
        }

        // if a base64 was sent, store it in the db
        if (starts_with($value, 'data:image'))
        {
            // 0. Make the image
            $image = \Image::make($value);
            // resize and prevent possible upsizing

            $image->resize(config('asgard.iblog.config.imagesize.width'), config('asgard.iblog.config.imagesize.height'), function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            if(config('asgard.iblog.config.watermark.activated')){
                $image->insert(config('asgard.iblog.config.watermark.url'), config('asgard.iblog.config.watermark.position'), config('asgard.iblog.config.watermark.x'), config('asgard.iblog.config.watermark.y'));
            }
            // 2. Store the image on disk.
            \Storage::disk($disk)->put($destination_path, $image->stream('jpg','80'));


            // Save Thumbs
            \Storage::disk($disk)->put(
                str_replace('.jpg','_mediumThumb.jpg',$destination_path),
                $image->fit(config('asgard.iblog.config.mediumthumbsize.width'),config('asgard.iblog.config.mediumthumbsize.height'))->stream('jpg','80')
            );

            \Storage::disk($disk)->put(
                str_replace('.jpg','_smallThumb.jpg',$destination_path),
                $image->fit(config('asgard.iblog.config.smallthumbsize.width'),config('asgard.iblog.config.smallthumbsize.height'))->stream('jpg','80')
            );

            // 3. Return the path
            return $destination_path;
        }

        // if the image was erased
        if ($value==null) {
            // delete the image from disk
            \Storage::disk($disk)->delete($destination_path);

            // set null in the database column
            return null;
        }
    }

   
}
