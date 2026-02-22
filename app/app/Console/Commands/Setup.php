<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class Setup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $steps = [
            [
                'command' => 'php artisan key:generate',
                'desc'    => 'Generate application encryption key',
            ],
            [
                'command' => 'php artisan migrate',
                'desc'    => 'Run database migrations',
            ],
            [
                'command' => 'php artisan storage:link',
                'desc'    => 'Create symbolic link for storage directory',
            ],
            [
                'command' => 'chmod -R 777 ./storage',
                'desc'    => 'Set write permissions for storage directory',
            ],
            [
                'command' => 'chmod -R 777 ./extensions',
                'desc'    => 'Set write permissions for extensions directory',
            ],
            [
                'command' => 'php artisan jwt:generate-keys',
                'desc'    => 'Generate private and public keys for JSON Web Token',
            ],
            [
                'command' => 'npm install',
                'desc'    => 'Install frontend package',
            ],
            [
                'command' => 'npm run build',
                'desc'    => 'Build frontend assets',
            ],
            [
                'command' => 'php artisan app:overview',
                'desc'    => 'Build cache overview',
            ],
        ];
        Process::run("cp -r ./.env.example .env");
        echo $this->info(__("Copy environment configuration file"));
        foreach ($steps as $key => $value) {
            $result = Process::run($value['command']);
            echo $result->output();
            if ($result->successful()) {
                echo $this->info(__($value['desc']));
            } else {
                echo $this->error(__($value['desc']));
                echo $result->errorOutput();
            }
        }
        $this->markErpAsInstalled();
        $this->info("🌍 Support the Project");
        $this->line("");

        $this->info("🇺🇸 English");
        $this->line("⭐ If this project helped you, please give it a star on GitHub!");
        $this->line("👉 https://github.com/liteerp-oss/liteerp");
        $this->line("");

        $this->info("🇻🇳 Tiếng Việt");
        $this->line("⭐ Nếu dự án này hữu ích với bạn, hãy cho 1 ⭐ trên GitHub nhé!");
        $this->line("👉 https://github.com/liteerp-oss/liteerp");
        $this->line("");

        $this->info("🇯🇵 日本語");
        $this->line("⭐ このプロジェクトが役に立ったら、ぜひ GitHub でスターをお願いします！");
        $this->line("👉 https://github.com/liteerp-oss/liteerp");
    }
    function markErpAsInstalled(): void
    {
        $path = 'erp.installed';

        if (!Storage::disk('local')->exists($path)) {
            Storage::disk('local')->put(
                $path,
                'LiteERP installed at ' . now()->toDateTimeString()
            );
        }
    }
}
