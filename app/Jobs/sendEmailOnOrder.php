<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class sendEmailOnOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    private $data;
    private $email;
    public function __construct(Order $data, $email)
    {
        $this->data = $data;
        $this->email = $email;

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::send('mail.guestEmail',['data' => $this->data,'user' => $this->email], function ($message){
            $message->from('qsdwebshop@gmail.com', 'QSD WebShop')
                    ->to($this->email) 
                    ->subject('QSD Order details');
        });
    }
}
