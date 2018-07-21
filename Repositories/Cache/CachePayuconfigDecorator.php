<?php

namespace Modules\IcommercePayu\Repositories\Cache;

use Modules\IcommercePayu\Repositories\PayuconfigRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CachePayuconfigDecorator extends BaseCacheDecorator implements PayuconfigRepository
{
    public function __construct(PayuconfigRepository $payuconfig)
    {
        parent::__construct();
        $this->entityName = 'icommercepayu.payuconfigs';
        $this->repository = $payuconfig;
    }
}
