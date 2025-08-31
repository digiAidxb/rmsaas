<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class ListTenantsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:list {--active : Only show active tenants}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all tenants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = Tenant::on('landlord');
        
        if ($this->option('active')) {
            $query->where('is_active', true);
        }
        
        $tenants = $query->orderBy('created_at', 'desc')->get();
        
        if ($tenants->isEmpty()) {
            $this->info('No tenants found.');
            return Command::SUCCESS;
        }
        
        $headers = ['ID', 'Name', 'Domain', 'Database', 'Active', 'Onboarding', 'Created'];
        $rows = [];
        
        foreach ($tenants as $tenant) {
            $progress = $tenant->getOnboardingProgress();
            $onboardingStatus = $progress['is_complete'] ? 'Complete' : $progress['completed_steps'] . '/' . $progress['total_steps'];
            
            $rows[] = [
                $tenant->id,
                $tenant->name,
                $tenant->domain,
                $tenant->database,
                $tenant->is_active ? 'Yes' : 'No',
                $onboardingStatus,
                $tenant->created_at->format('Y-m-d H:i:s'),
            ];
        }
        
        $this->table($headers, $rows);
        
        return Command::SUCCESS;
    }
}
