<?php 
namespace Core\Permission\Infrastructure\Listeners;

use Core\Permission\Application\UseCases\CheckPermission;
use Core\Permission\Application\UseCases\CreateFullPermission;
use Illuminate\Support\Facades\Event;

class ListenerEvent
{
    public function __construct(
        private CreateFullPermission $useCase,
        private CheckPermission $checkPermission
    ) {}
    public function handle(){
        Event::listen('erp.*.*', function (string $event,array $data) {
            if($event === 'erp.business.create'
            || $event === 'erp.notification.create'
            || $event === 'erp.notification.createMany'
            || $event === "erp.authencation.create_admin") {
                return;
            }
            if($event == 'erp.permissiongroup.create_admin') {
                $this->useCase->handle($data);
                return;
            }
            $this->checkPermission->handle([
                ...$data,
                'permission' => $event,
            ]);
        });
    }
}