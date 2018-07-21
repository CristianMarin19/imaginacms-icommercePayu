@php
	$configuration = icommercepayu_get_configuration();
	$options = array('required' =>'required');
	
	if($configuration==NULL){
		$cStatus = 0;
		$entity = icommercepayu_get_entity();
	}else{
		$cStatus = $configuration->status;
		$entity = $configuration;
	}

	$status = icommerce_get_status();
	$formID = uniqid("form_id");

@endphp


{!! Form::open(['route' => ['admin.icommercepayu.payuconfig.update'], 'method' => 'put','name' => $formID]) !!}


<div class="col-xs-12 col-sm-9">

	
	@include('icommerce::admin.products.partials.flag-icon',['entity' => $entity,'att' => 'description'])
	
	{!! Form::normalInput('description','*'.trans('icommercepayu::payuconfigs.table.description'), $errors,$configuration,$options) !!}

	{!! Form::normalInput('merchantId', '*'.trans('icommercepayu::payuconfigs.table.merchantId'), $errors,$configuration,$options) !!}

	{!! Form::normalInput('apilogin', '*'.trans('icommercepayu::payuconfigs.table.apilogin'), $errors,$configuration,$options) !!}

	{!! Form::normalInput('apiKey', '*'.trans('icommercepayu::payuconfigs.table.apiKey'), $errors,$configuration,$options) !!}

	{!! Form::normalInput('accountId', '*'.trans('icommercepayu::payuconfigs.table.accountId'), $errors,$configuration,$options) !!}
	
	<div class="form-group">
        <label for="url_action">*Mode</label>
        <select class="form-control" id="url_action" name="url_action" required>
        	<option value="0" @if(!empty($configuration) && $configuration->url_action==0) selected @endif>SANDBOX</option>
        	<option value="1" @if(!empty($configuration) && $configuration->url_action==1) selected @endif>PRODUCTION</option>
        </select>
    </div>
   
	{!! Form::normalInput('currency', '*'.trans('icommercepayu::payuconfigs.table.currency'), $errors,$configuration,$options) !!}

	<div class="form-group">
        <label for="test">*{{trans('icommercepayu::payuconfigs.table.test')}}</label>
        <select class="form-control" id="test" name="test" required>
        	<option value="1" @if(!empty($configuration) && $configuration->test==1) selected @endif>YES</option>
            <option value="0" @if(!empty($configuration) && $configuration->test==0) selected @endif>NO</option>
        </select>
    </div>

    <div class="form-group">
	    <div>
		    <label class="checkbox-inline">
		    	<input name="status" type="checkbox" @if($cStatus==1) checked @endif>{{trans('icommercepayu::payuconfigs.table.activate')}}
		    </label>
		</div>   
	</div>

</div>

<div class="col-sm-3">

	@include('icommercepayu::admin.payuconfigs.partials.featured-img',['crop' => 0,'name' => 'mainimage','action' => 'create'])

</div>
   	
	
 <div class="clearfix"></div>   

    <div class="box-footer">
    <button type="submit" class="btn btn-primary btn-flat">{{ trans('icommercepayu::payuconfigs.button.save configuration') }}</button>
    </div>



{!! Form::close() !!}