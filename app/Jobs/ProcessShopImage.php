<?php

namespace App\Jobs;

use App\Model\WaRouteCustomer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessShopImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $filePath;
    protected $customerId;

    /**
     * Create a new job instance.
     */
    public function __construct($filePath, $customerId)
    {
        $this->filePath = $filePath;
        $this->customerId = $customerId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $customer = WaRouteCustomer::find($this->customerId);
            if ($customer) {
                $customer->image_url = basename($this->filePath);
                $customer->save();
            } else {
                Log::error('Customer not found for ID: ' . $this->customerId);
            }
        } catch (\Exception $e) {
            Log::error('Error processing image: ' . $e->getMessage());
        }
    }
}
