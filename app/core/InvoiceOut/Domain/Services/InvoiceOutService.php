<?php

namespace Core\InvoiceOut\Domain\Services;

use App\Exceptions\BadException;
use Core\InvoiceOut\Domain\Entities\InvoiceOut;

interface InvoiceOutService
{
    public function create(array $data): InvoiceOut;
    public function show(array $data) : array | BadException;
    public function findById(array $data) : InvoiceOut | BadException;
    public function getByOrderId(array $data) : ?InvoiceOut;
    public function findByOrderId(array $data) : InvoiceOut | BadException;
    public function update(array $data) : InvoiceOut | BadException;
    public function unApproved(array $data): InvoiceOut|BadException;
    public function changeShippingFee(array $data) : InvoiceOut | BadException;
}