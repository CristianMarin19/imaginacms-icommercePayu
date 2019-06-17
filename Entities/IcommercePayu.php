<?php

namespace Modules\Icommercepayu\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class IcommercePayu extends Model
{
    use Translatable;

    protected $table = 'icommercepayu__icommercepayus';
    public $translatedAttributes = [];
    protected $fillable = [];
}
