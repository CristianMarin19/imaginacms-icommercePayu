<?php

namespace Modules\Icommercepayu\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class UpdatePayuconfigRequest extends BaseFormRequest
{
    public function rules()
    {
        return [];
    }

    public function translationRules()
    {
        return [];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [];
    }

    public function translationMessages()
    {
        return [];
    }
}
