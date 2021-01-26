<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketsPurchased extends Mailable
{
    use Queueable, SerializesModels;

    public $tickets;

    /**
     * Create a new message instance.
     *
     * @param $tickets
     */
    public function __construct($tickets)
    {
        $this->tickets = $tickets;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('no-reply@elixirevents.com')
            ->subject('Tickets purchased')
            ->view('mail.tickets.purchased');
    }
}
