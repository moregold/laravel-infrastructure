<?php namespace Moregold\Infrastructure\Clients\PayPal;

use Illuminate\Support\Facades\Config;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Moregold\Infrastructure\Clients\PayPal\Contracts\PayPalClientInterface;
use Exception;

class PayPalClient implements PayPalClientInterface
{

    private $ClientID;
    private $ClientSecret;

    public function __construct()
    {
        $this->ClientID = Config::get('services.paypal.key.client_id');
        $this->ClientSecret = Config::get('services.paypal.key.secret');
    }


    

    public function getApiContext()
    {
        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                $this->ClientID,     // ClientID
                $this->ClientSecret     // ClientSecret
            )
        );
        // based configuration
        $apiContext->setConfig(
            array(
                'mode' => 'sandbox',
                'log.LogEnabled' => true,
                'log.FileName' => '../PayPal.log',
                'log.LogLevel' => 'DEBUG', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
                'cache.enabled' => true,
            )
        );
        return $apiContext;
    }


    public function payout( $request_data = [])
    {
        try {
            // Create a new instance of Payout object
            $payouts = $this->createPayOutData($request_data);
            // ### Create Payout
            $output = $this->postRequest('SynchronousCreate', $payouts, $this->getApiContext());
        } catch (Exception $ex) {
            if($ex->getCode() == 401){
                $error_box = $ex->getData(); 
            }else{
                $error_box = $ex->getMessage(); 
            }   
        }
        if(isset($output)){
            # $output->getBatchHeader()->getPayoutBatchId()
            if($output->getBatchHeader()->getBatchStatus() != 'SUCCESS'){
                $error_box = $output->getItems()[0]->getErrors()->getMessage(); 
            }
        }
        if(isset($error_box)){
            throw new Exception($error_box, 400);
        }
        return true;
    }


    public function createPayOutData($request_data = [])
    {
        // Create a new instance of Payout object
        foreach ($request_data as $value) {
            $request_data_merge[] = [
                "recipient_type" => "EMAIL",
                "amount" => [
                    "value" => $value['value'],
                    "currency" => "USD"
                ],
                "note" => "",
                "receiver" => $value['receiver']
            ];
        }
        $payouts = new \PayPal\Api\Payout();

        $senderBatchHeader = new \PayPal\Api\PayoutSenderBatchHeader();

        $senderBatchHeader->setSenderBatchId(uniqid())
            ->setEmailSubject("You have a payout");
        $payouts = $payouts->setSenderBatchHeader($senderBatchHeader);
        foreach ($request_data_merge as $key => $data) {
            $name = 'senderItem'.$key;
            $$name = new \PayPal\Api\PayoutItem();
            $$name->setRecipientType($data['recipient_type'])
                ->setNote($data['note'])
                ->setReceiver($data['receiver'])
                ->setSenderItemId("item_" . uniqid())
                ->setAmount(new \PayPal\Api\Currency(
                    json_encode($data['amount'])
                ));
            $payouts->addItem($$name);
        }
        return $payouts;
    }

    public function postRequest($method, $payouts, $apiContext)
    {
        if($method == 'SynchronousCreate'){
            return $payouts->createSynchronous($apiContext);
        }elseif($method == 'AsynchronousCreate'){
            return $payouts->create(null, $apiContext);
        }
    }
}