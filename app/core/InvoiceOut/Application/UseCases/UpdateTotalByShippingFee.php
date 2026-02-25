<?php

namespace Core\InvoiceOut\Application\UseCases;

use App\Exceptions\BadException;
use Core\InvoiceOut\Application\DTOs\UpdateInvoiceOutByShippingFeeRequest;
use Core\InvoiceOut\Domain\Services\InvoiceOutService;
use Illuminate\Support\Facades\DB;

class UpdateTotalByShippingFee
{
    public function __construct(
        private InvoiceOutService $service
    ) {}

    public function handle(array $data)
    {
        $dto = UpdateInvoiceOutByShippingFeeRequest::fromArray($data);
        if(floatval($dto->shipping_fee_actual) === 0) {
            /**
             * If shipping fee actual is not 
             * insert then system continue use estimate shipping free
             */
            return;
        }
        $findInvoice = $this->service->getByOrderId($dto->toArray());
        if(!$findInvoice) {
            /**
             * Invoice out is not exists then system skip
             */
            return;
        }
        DB::beginTransaction();
        $update = $this->service->changeShippingFee($dto->toArray());
        DB::commit();
        return $update;
    }
}
