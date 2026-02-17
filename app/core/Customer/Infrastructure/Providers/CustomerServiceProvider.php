<?php

namespace Core\Customer\Infrastructure\Providers;

use Core\Customer\Application\UseCases\OrderShippingCustomer;
use Illuminate\Support\ServiceProvider;
use Core\Customer\Domain\Repositories\CustomerRepositoryInterface;
use Core\Customer\Infrastructure\Repositories\EloquentCustomerRepository;
use Core\Customer\Domain\Services\CustomerService;
use Core\Customer\Infrastructure\Listeners\CustomerListener;
use Core\Customer\Infrastructure\Services\CustomerServiceImpl;

class CustomerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(CustomerRepositoryInterface::class, EloquentCustomerRepository::class);
        $this->app->bind(CustomerService::class, CustomerServiceImpl::class);
        $this->mergeModuleConfig();
    }

    public function boot(OrderShippingCustomer $OrderShippingCustomer)
    {
        $this->loadModuleRoutes();
        $this->loadModuleTranslations();
        $listener = new CustomerListener();
        $listener->handle($OrderShippingCustomer);
    }

    protected function mergeModuleConfig(): void
    {
        $path = __DIR__ . '/../config/' . strtolower('Customer') . '.php';
        if (file_exists($path)) {
            $this->mergeConfigFrom($path, strtolower('Customer'));
        }
    }

    protected function loadModuleTranslations(): void
    {
        $langPath = __DIR__ . '/../lang';
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, strtolower('Customer'));
        }
    }

    protected function loadModuleRoutes(): void
    {
        $routePath = __DIR__ . '/../routes';
        if (file_exists("$routePath/api.php")) {
            $this->loadRoutesFrom("$routePath/api.php");
        }
        if (file_exists("$routePath/web.php")) {
            $this->loadRoutesFrom("$routePath/web.php");
        }
    }
}