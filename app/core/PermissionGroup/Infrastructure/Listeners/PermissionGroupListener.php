<?php 
namespace Core\PermissionGroup\Infrastructure\Listeners;

use Core\PermissionGroup\Application\UseCases\CreatePermissionGroupAdmin;
use Illuminate\Support\Facades\Event;

class PermissionGroupListener
{
    function __construct(private CreatePermissionGroupAdmin $createPermissionGroupAdmin)
    {
        
    }
    public function handle()
    {
        Event::listen('erp.business.*', function (string $event,array $data) {
            if($event == 'erp.business.create') {
                $this->createPermissionGroupAdmin->handle($data);
            }
        });
    }
}