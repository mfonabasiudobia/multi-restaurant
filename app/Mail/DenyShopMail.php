<?php

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DenyShopMail extends Mailable
{
    use Queueable, SerializesModels;


    public $message;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.denyShop')
                    ->with([
                        'messageContent' => $this->message,
                    ])
                    ->subject('Shop Denial Notification');
    }
}