<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Batch;

class ClearedBatchPaymentQueryMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $batch;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Batch $batch)
    {
        $this->batch = $batch;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('BATCH PAYMENT QUERY CLEARED!!')->view('emails.cleared-query');
    }
}