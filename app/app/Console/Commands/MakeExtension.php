<?php

namespace App\Console\Commands;

use Core\Extension\Application\UseCases\MakeExtension as UseCasesMakeExtension;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class MakeExtension extends Command
{
    protected $signature = 'make:extension {name} {directory}';
    protected $description = 'Generate a new LiteERP extension skeleton';
    private array $info = [
        'name' => '',
        'version' => '0.0.1',
        'description' => "",
        'status' => false,
        "verified"  => true,
        "author" => "Author name",
        "icon" => null,
        "setting_link" => null,
        "email" => null,
        "directory" => '',
        "support_version" => '',
    ];

    public function handle(UseCasesMakeExtension $make)
    {
        $directory = Str::studly($this->argument('directory'));
        $basePath = base_path("extensions/{$directory}");

        $fs = new Filesystem();

        if ($fs->exists($basePath)) {
            $this->error("Extension {$this->argument('name')} already exists.");
            return Command::FAILURE;
        }
        $this->info['name'] = $this->argument('name');
        $this->info['directory'] = $directory;
        $this->info['description'] = $this->argument('name') . " extension support LiteERP";
        $this->createDirectories($fs, $basePath);
        $this->createFiles($fs, $basePath, $directory);
        $make->handle($this->info);
        /**
         * We need setup chmod 777 to support ubuntu delete folder
         */
        exec('chmod -R 777 ' . $basePath);
        $this->info("Extension {$this->argument('name')} generated successfully.");
        return Command::SUCCESS;
    }

    protected function createDirectories(Filesystem $fs, string $base)
    {
        $dirs = [
            'Http/Controllers',
            'Models',
            'Hooks',
            'Database/Migrations',
            'Routes',
            'Config',
            'Resources/js',
            'Resources/js/i18n/en',
            'Resources/js/i18n/ja',
            'Resources/js/i18n/vi',
            'Resources/css',
            'Resources/assets',
            'lang/en',
            'lang/ja',
            'lang/vi',
        ];

        foreach ($dirs as $dir) {
            $fs->makeDirectory("{$base}/{$dir}", 0755, true);
        }
    }

    protected function createFiles(Filesystem $fs, string $base, string $directory)
    {
        $strtolower = strtolower($directory);
        $namespace = "Extensions\\{$directory}";

        // Service Provider
        $fs->put("{$base}/ExtensionServiceProvider.php", <<<PHP
<?php

namespace {$namespace};

use Illuminate\Support\ServiceProvider;

class ExtensionServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
        \$this->app->tag(
            \Extensions\\{$directory}\\Hooks\AddMenuHook::class,
            'liteerp.hooks'
        );
    }

    public function boot()
    {
        if(env('APP_ENV') !== 'production') {
          // support for development to easy
          \$this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        }
        \$this->loadRoutesFrom(__DIR__.'/Routes/web.php');
        \$this->loadRoutesFrom(__DIR__.'/Routes/web.php');
        \$this->loadTranslationsFrom(__DIR__.'/lang','extension.{$strtolower}');
    }
}
PHP);

        // Route
        $fs->put("{$base}/Routes/web.php", <<<PHP
<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web'])
    ->prefix('extensions')
    ->group(function () {
        //
    });
PHP);

        // Hook example
        $fs->put("{$base}/Hooks/AddMenuHook.php", <<<PHP
<?php

namespace Extensions\\{$directory}\\Hooks;

use App\Supports\Forms\FormFieldRender;
use App\Supports\Forms\FormFieldType;
use App\Supports\Hooks\HookContext;
use App\Contracts\Hooks\HookInterface;
use App\Supports\Hooks\HookAction;
use App\Supports\Hooks\HookPhase;
use App\Supports\Hooks\HookResult;
use App\Supports\Hooks\HookTiming;

class AddMenuHook implements HookInterface
{
    //private static \$module="Permission";
    public static function supports(HookContext \$context): bool
    {
        return \$context->action === HookAction::INDEX
            && \$context->phase === HookPhase::UI
            && \$context->timing === HookTiming::BEFORE;
            //&& \$context->module === self::\$module;
    }

    public function handle(HookContext \$context): HookResult
    {
        return HookResult::pass([
            ...\$context->payload
        ]);
    }
}
PHP);

        // Model
        $fs->put("{$base}/Models/{$directory}Model.php", <<<PHP
<?php

namespace {$namespace}\Models;

use Illuminate\Database\Eloquent\Model;

class {$directory}Model extends Model
{
    protected \$guarded = [];
}
PHP);

        // Install
        $fs->put("{$base}/Install.php", <<<PHP
<?php

return [
    'install' => [
        'commands' => [
            // [
            //     'name' => 'app:npmbuild',
            //     'description' => 'Build frontend assets',
            //     'risk' => 'low',
            // ],
        ],

        'migrations' => [
            // '2025_01_01_000000_create_example_table.php',
        ],
    ],

    'uninstall' => [
        'commands' => [
            // [
            //     'name' => 'app:npmbuild',
            //     'description' => 'Rebuild frontend after uninstall',
            //     'risk' => 'low',
            // ],
        ],

        'migrations' => [
            // rollback handled by core
        ],
    ],
];

PHP);

        // lang
        $fs->put("{$base}/lang/en/messages.php", <<<PHP
            <?php

            return [
                "nav" => "{$directory}"
            ];

            PHP);
        $fs->put("{$base}/lang/ja/messages.php", <<<PHP
            <?php

            return [
                "nav" => "{$directory}"
            ];

            PHP);
        $fs->put("{$base}/lang/vi/messages.php", <<<PHP
            <?php

            return [
                "nav" => "{$directory}"
            ];

            PHP);

        /**
         * React
         */
        // React router   
        $fs->put("{$base}/Resources/js/autoload.js", <<<JS
        import Extension from '@core/Extension'
        import RegisterRoute from '@core/RegisterRoute'
        import {$directory}App from './app.jsx'
        export default class ServiceProvider extends Extension {
            register() {
                RegisterRoute({
                    path: '/{$strtolower}',
                    component: {$directory}App
                })
            }
            boot() {
                //console.log('{$directory} loadded');
            }
        }
        JS);
        // react app 
        $fs->put("{$base}/Resources/js/app.jsx", <<<JS
        import React, { useState } from 'react';
        import DashboardLayout from '@layouts/DashboardLayout'
        import PageHead from '@components/PageHead'
        import {useI18n} from '@i18n/useI18n'
        const {$directory}Page = () => {
            const {t} = useI18n()

            return (
                <DashboardLayout>
                    <div className="">
                        <PageHead title={t('{$strtolower}.title')} subtitle={t('{$strtolower}.desc')}/>
                        <div>

                        </div>
                    </div>
                </DashboardLayout>

            );
        };

        export default {$directory}Page;
        JS);
        // react i18n - en 
        $fs->put("{$base}/Resources/js/i18n/en/messages.js", <<<JS
        export default {}
        JS);
        // react i18n - vi 
        $fs->put("{$base}/Resources/js/i18n/vi/messages.js", <<<JS
        export default {}
        JS);
        // react i18n - ja 
        $fs->put("{$base}/Resources/js/i18n/ja/messages.js", <<<JS
        export default {}
        JS);

        // css 
        $fs->put("{$base}/Resources/css/autoload.css", <<<CSS
        .{$strtolower} {}
        CSS);

        // extension.json
        $fs->put("{$base}/extension.json", json_encode($this->info, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        // README
        $fs->put("{$base}/README.md", <<<MD
# {$directory}

LiteERP extension.

## Description
Describe what this extension does.

## Hooks
- 

## Notes
This extension does not modify core modules.
MD);

        // LICENSE
        $fs->put("{$base}/Resources/assets/license", <<<TXT
            # {$directory}
        TXT);
    }
}
