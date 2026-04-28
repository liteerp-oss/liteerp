<?php

namespace Core\ImageManager\Application\UseCases;

use Core\ImageManager\Application\DTOs\CreateImageManagerRequest;
use Core\ImageManager\Domain\Services\ImageManagerService;
use Illuminate\Support\Facades\Storage;

class CreateImageManager
{
    public function __construct(private ImageManagerService $service) {}

    public function handle(CreateImageManagerRequest $dto)
    {
        $path = Storage::putFile('public/business/' . $dto->business_id .'/images', $dto->file);
        return $this->service->create([
            ...$dto->toArray(),
            'path' => Storage::url($path)
        ]);
    }
}