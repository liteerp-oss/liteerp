<?php 
namespace Core\Permission\Infrastructure\Listeners;

use Core\Permission\Application\UseCases\CheckPermission;
use Core\Permission\Application\UseCases\CreateFullPermission;
use Core\Permission\Application\UseCases\CreatePermission;
use Core\Permission\Infrastructure\Helpers\PermissionBuilder;
use Illuminate\Support\Facades\Event;

class ListenerEvent
{
    public function __construct(
        private CreateFullPermission $createFull,
        private CreatePermission $create,
        private CheckPermission $checkPermission,
        private PermissionBuilder $builder
    ) {}
    public function handle(){
        Event::listen('erp.*.*', function (string $event,array $data) {
            if( in_array($event,$this->builder->buildByPass())) {
                return;
            }
            if($event === 'erp.permissiongroup.create_admin') {
                $this->createFull->handle($data);
                return;
            }
            $this->checkPermission->handle([
                ...$data,
                'permission' => $event,
            ]);
            if($event == 'erp.permissiongroup.create') {
                $this->create->handle($data);
                return;
            }
        });
    }
}