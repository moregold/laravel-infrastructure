<?php namespace Moregold\Infrastructure\Clients\Sparrow\Contracts;

use Moregold\Domains\Billing\Transaction;

interface SparrowClientInterface
{
    public function requestAccessToken();
    public function postSaleTransaction($request_data = []);
}
