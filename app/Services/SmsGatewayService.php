<?php

namespace App\Services;

use App\Models\SMSConfig;
use App\Services\Contracts\SmsGatewayInterface;
use Illuminate\Support\Facades\App;

class SmsGatewayService
{
    protected $gateway;

    public function __construct()
    {
        $smsConfig = SMSConfig::where('status', true)->first();

        if ($smsConfig) {
            $this->gateway = $this->resolveGateway($smsConfig->provider);
        }
    }

    protected function resolveGateway(string $provider): ?SmsGatewayInterface
    {
        switch ($provider) {
            case 'twilio':
                return App::make(TwilioService::class);
            case 'message_bird':
                return App::make(MessageBirdService::class);
            case 'nexmo':
                return App::make(NexmoService::class);
            case 'telesign':
                return App::make(TelesignService::class);
            default:
                return null;
        }
    }

    public function sendSMS($phoneCode, $phoneNumber, $message)
    {
        if (! $this->gateway) {
            return false;
        }

        $to = $phoneCode.$phoneNumber;
        return $this->gateway->sendSMS($to, $message);
    }

    public function sendWhatsApp($phoneCode, $phoneNumber, $message)
    {
        if (! $this->gateway) {
            return false;
        }

        $to = $phoneCode.$phoneNumber;
        return $this->gateway->sendWhatsApp($to, $message);
    }
}
