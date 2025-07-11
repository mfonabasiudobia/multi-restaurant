<?php

namespace App\Services;

use App\Models\SMSConfig;
use App\Services\Contracts\SmsGatewayInterface;
use Twilio\Rest\Client;

class TwilioService implements SmsGatewayInterface
{
    protected $client;
    protected $config;

    public function __construct()
    {
        $smsConfig = SMSConfig::where('provider', 'twilio')
            ->where('status', true)
            ->first();

        if ($smsConfig) {
            $this->config = json_decode($smsConfig->data);
            $this->client = new Client($this->config->twilio_sid, $this->config->twilio_token);
        }
    }

    public function sendMessage($phone, $message)
    {
        if (!$this->client || !$this->config) {
            \Log::error('Twilio client not initialized. Please check configuration.');
            return false;
        }
        // Default to SMS if not specified
        return $this->sendSMS($phone, $message);
    }

    public function sendSMS($phone, $message)
    {
        if (!$this->client || !$this->config) {
            \Log::error('Twilio client not initialized. Please check configuration.');
            return false;
        }

        \Log::info('TwilioService sendSMS method called');
        \Log::info('phone: ' . $phone);
        \Log::info('message: ' . $message);
        \Log::info('config: ' . json_encode($this->config));

        // Ensure phone number starts with +
        $phone = '+' . ltrim($phone, '+');

        return $this->client->messages->create($phone, [
            'from' => $this->config->twilio_from,
            'body' => $message,
        ]);
    }

    public function sendWhatsApp($phone, $message)
    {
        if (!$this->client || !$this->config) {
            \Log::error('Twilio client not initialized. Please check configuration.');
            return false;
        }

        \Log::info('TwilioService sendWhatsApp method called');
        \Log::info('phone: ' . $phone);
        \Log::info('message: ' . $message);
        \Log::info('config: ' . json_encode($this->config));

        // Format the phone number correctly for WhatsApp
        $phone = ltrim($phone, '+'); // Remove + if exists
        $phone = preg_replace('/^whatsapp:/', '', $phone); // Remove whatsapp: if exists
        $whatsappNumber = 'whatsapp:+' . $phone;

        return $this->client->messages->create($whatsappNumber, [
            'from' => 'whatsapp:' . ltrim($this->config->twilio_whatsapp_from, '+'),
            'body' => $message,
        ]);
    }
}
