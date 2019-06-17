<?php

namespace Modules\Icommercepayu\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface IcommercePayuRepository extends BaseRepository
{

    public function encriptUrl($orderID,$transactionID,$currencyID);

    public function decriptUrl($eUrl);
    
}
