<?php

namespace App\Observers;

use App\Models\Message;
use Illuminate\Support\Carbon;

class MessageObserver
{
    /**
     * Handle the Message "creating" event.
     *
     * @param  \App\Models\Message  $message
     * @return void
     */
    public function creating(Message $message)
    {
        if (!$message->created_at) {
            $message->created_at = Carbon::now();
        }
    }
}