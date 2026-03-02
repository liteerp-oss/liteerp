<?php

namespace Core\Extension\Application\UseCases;

use App\Supports\Permissions\Enums\Permission;
use Core\Extension\Application\DTOs\IndexExtensionRequest;
use Core\Extension\Domain\Services\ExtensionService;
use Illuminate\Support\Facades\Event;

class IndexExtension
{
    public function __construct(private ExtensionService $service) {}

    public function handle(array $data)
    {
        Event::dispatch(Permission::EXTENSION_INDEX->value,$data);
        $dto = IndexExtensionRequest::fromArray($data);
        return $this->service->index($dto->toArray());
    }
}