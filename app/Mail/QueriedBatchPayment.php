<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Batch;

class QueriedBatchPayment extends Mailable
{
    use Queueable, SerializesModels;

    public $batch, $message;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Batch $batch, $message)
    {
        $this->batch = $batch;
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("QUERIED BATCH PAYMENT ALERT!!")->view('emails.queried');
    }
}
