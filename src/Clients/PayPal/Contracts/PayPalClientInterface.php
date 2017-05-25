<?php namespace Moregold\Infrastructure\Clients\PayPal\Contracts;
use App\Models\PaypalTransactions;

interface PayPalClientInterface
{
    public function getApiContext();
    public function payout($reqeust_data =[]);
}