<?php

namespace Extensions\InventoryTracking\Jobs;

use Core\Inventory\Application\UseCases\FindInventoryById;
use Core\Notifications\Application\DTOs\InsertManyNotificationRequest;
use Core\Notifications\Application\UseCases\InsertManyNotification;
use Core\Product\Application\UseCases\ShowProduct;
use Extensions\InventoryTracking\Models\InventoryTrackingModel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Foundation\Queue\Queueable;

class InventoryTrackingJob implements ShouldQueue,ShouldQueueAfterCommit
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
        $findInventoryById = app(FindInventoryById::class);
        $insertManyNotification = app(InsertManyNotification::class);
        $showProduct = app(ShowProduct::class);
        $setting = InventoryTrackingModel::where('business_id',$this->data['business_id'])->first();
        if ($setting) {
            $inventory = $findInventoryById->handle([
                'id' => $this->data['id'],
                'business_id' => $this->data['business_id']
            ]);
            $total = floatval($inventory->quantity) - intval($inventory->reserved_qty);
            if ($total <= floatval($setting->min)) {
                $product = $showProduct->handle([
                    'business_id' => $this->data['business_id'],
                    'user_id' => $this->data['created_by'],
                    'id' => $inventory->product_id
                ]);
                $insertManyNotification->handle(InsertManyNotificationRequest::fromArray([
                    'message' => 'extension.inventorytracking::messages.inventory.message',
                    'message_params' => [
                        'sku' => $product['sku'],
                        'name' => $product['name']
                    ],
                    'link'    => '/dashboard/inventory',
                    'title'   =>  'extension.inventorytracking::messages.inventory.title',
                    'entity_type' => 'inventory',
                    'entity_id' => $inventory->id,
                    'chanels' => ['mail','db'],
                    'role' => ['manager'],
                    'business_id'   => $this->data['business_id'],
                    'type'  => 'updated',
                    'user_id' => $this->data['created_by']
                ]));
            }
        }
    }
}
