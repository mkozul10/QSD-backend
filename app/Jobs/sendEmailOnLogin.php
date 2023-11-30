<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use stdClass;

class sendEmailOnLogin implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    private $number;
    private $name;
    private $email;
    public function __construct($name, $number, $email)
    {
        $this->name = $name;
        $this->number = $number;
        $this->email = $email;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::send('mail.validate',['number' => $this->number,'user' => $this->name], function ($message){
                $message->from('qsdwebshop@gmail.com', 'QSD WebShop')
                    ->to($this->email,$this->name) 
                    ->subject('QSD Verification code');
            });
        } catch (\Exception $e) {
            
            \Log::error('Error in sendEmailOnLogin job: ' . $e->getMessage());
            
        }
        
    }
}
