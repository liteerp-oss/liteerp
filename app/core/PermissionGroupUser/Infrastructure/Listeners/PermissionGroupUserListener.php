<?php 
namespace Core\PermissionGroupUser\Infrastructure\Listeners;

use Core\PermissionGroupUser\Application\UseCases\CreatePermissionAdminGroupUser;
use Core\PermissionGroupUser\Application\UseCases\CreatePermissionGroupUser;
use Core\PermissionGroupUser\Application\UseCases\DeletePermissionGroupUser;
use Illuminate\Support\Facades\Event;

class PermissionGroupUserListener
{
    function __construct(private CreatePermissionGroupUser $create,
    private DeletePermissionGroupUser $delete,
    private CreatePermissionAdminGroupUser $createAdmin)
    {
        
    }
    public function handle()
    {
        Event::listen('erp.*.*', function (string $event,array $payload) {
            if($event == 'erp.user.create') {
                $this->create->handle($payload);
            }
            if($event == 'erp.permissiongroup.create_admin') {
                $this->createAdmin->handle($payload);
            }
            if($event == 'erp.user.delete') {
                $this->delete->handle($payload);
            }
        });
    }
}