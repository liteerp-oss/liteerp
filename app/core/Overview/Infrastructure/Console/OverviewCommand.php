<?php

namespace Core\Overview\Infrastructure\Console;

use Core\Business\Application\UseCases\AllBusiness;
use Core\Overview\Application\UseCases\CreateOverview;
use Illuminate\Console\Command;

class OverviewCommand extends Command
{
    protected $signature = 'app:overview';
    protected $description = 'Build overview caches for all businesses';

    public function handle(CreateOverview $CreateOverview, AllBusiness $AllBusiness)
    {
        $CreateOverview->handle($AllBusiness);
        $this->info("Done!");
    }
}
