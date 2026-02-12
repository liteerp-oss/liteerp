<?php

namespace Extensions\FastMode\Jobs;

use Core\InvoiceOut\Application\UseCases\UpdateInvoiceOut;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Foundation\Queue\Queueable;

class TriggerApproveInvoice implements ShouldQueue,ShouldQueueAfterCommit
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private array $data)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $triggerUpdate = app(UpdateInvoiceOut::class);
        $triggerUpdate->handle($this->data);
    }
}
