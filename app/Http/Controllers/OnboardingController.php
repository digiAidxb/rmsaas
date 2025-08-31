<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Multitenancy\Models\Tenant;

class OnboardingController extends Controller
{
    public function index()
    {
        $tenant = Tenant::current();
        
        // Initialize onboarding if not already done
        if (!$tenant->onboarding_status) {
            $tenant->initializeOnboarding();
        }
        
        $progress = $tenant->getOnboardingProgress();
        
        return Inertia::render('Onboarding/Index', [
            'tenant' => $tenant,
            'progress' => $progress
        ]);
    }

    public function step($stepName)
    {
        $tenant = Tenant::current();
        
        if (!$tenant->onboarding_status) {
            $tenant->initializeOnboarding();
        }
        
        $progress = $tenant->getOnboardingProgress();
        $steps = $progress['steps'];
        
        if (!isset($steps[$stepName])) {
            return redirect()->route('onboarding.index');
        }
        
        $currentStep = $steps[$stepName];
        $stepIndex = array_search($stepName, array_keys($steps));
        
        return Inertia::render("Onboarding/Steps/{$this->getStepComponent($stepName)}", [
            'tenant' => $tenant,
            'progress' => $progress,
            'currentStep' => $currentStep,
            'stepName' => $stepName,
            'stepIndex' => $stepIndex,
            'totalSteps' => count($steps) - 1, // -1 to exclude 'completed' step
        ]);
    }

    public function complete(Request $request, $stepName)
    {
        $tenant = Tenant::current();
        
        $request->validate([
            'data' => 'sometimes|array'
        ]);
        
        // Process step-specific data
        $this->processStepData($stepName, $request->input('data', []), $tenant);
        
        // Mark step as completed
        $tenant->completeOnboardingStep($stepName);
        
        // Get next step
        $progress = $tenant->getOnboardingProgress();
        $steps = array_keys($progress['steps']);
        $currentIndex = array_search($stepName, $steps);
        
        if ($currentIndex !== false && $currentIndex + 1 < count($steps)) {
            $nextStep = $steps[$currentIndex + 1];
            
            // Skip 'completed' step if it's next
            if ($nextStep === 'completed') {
                return redirect()->route('dashboard')->with('success', 'Onboarding completed successfully! Welcome to RMSaaS!');
            }
            
            return redirect()->route('onboarding.step', $nextStep);
        }
        
        return redirect()->route('dashboard')->with('success', 'Onboarding completed successfully!');
    }

    public function skip(Request $request)
    {
        $tenant = Tenant::current();
        
        // Check if user wants test data
        $seedTestData = $request->input('seed_test_data', false);
        
        if ($seedTestData) {
            try {
                // Seed test data for demonstration
                \Artisan::call('tenant:seed-test-data', ['--tenant' => $tenant->id]);
                
                $tenant->skipOnboarding();
                
                return redirect()->route('dashboard')->with([
                    'success' => 'Onboarding skipped and demo data loaded! Explore the analytics and inventory management features.',
                    'demo_mode' => true
                ]);
                
            } catch (\Exception $e) {
                \Log::error('Failed to seed test data: ' . $e->getMessage());
                
                $tenant->skipOnboarding();
                
                return redirect()->route('dashboard')->with([
                    'warning' => 'Onboarding skipped, but failed to load demo data. You can still set up your restaurant manually.',
                ]);
            }
        }
        
        $tenant->skipOnboarding();
        
        return redirect()->route('dashboard')->with('info', 'Onboarding skipped. You can access setup features from the dashboard.');
    }

    private function getStepComponent($stepName): string
    {
        $componentMap = [
            'welcome' => 'Welcome',
            'business_info' => 'BusinessInfo',
            'data_import' => 'DataImport',
            'menu_setup' => 'MenuSetup',
            'inventory_setup' => 'InventorySetup',
            'staff_setup' => 'StaffSetup',
            'dashboard_tour' => 'DashboardTour',
        ];
        
        return $componentMap[$stepName] ?? 'Welcome';
    }

    private function processStepData($stepName, array $data, $tenant): void
    {
        switch ($stepName) {
            case 'business_info':
                // Update tenant settings with business information
                $settings = is_array($tenant->settings) ? $tenant->settings : [];
                $settings['business_info'] = $data;
                $tenant->settings = $settings;
                $tenant->save();
                break;
                
            case 'data_import':
                // Handle data import preferences
                $settings = is_array($tenant->settings) ? $tenant->settings : [];
                $settings['data_import'] = $data;
                $tenant->settings = $settings;
                $tenant->save();
                break;
                
            // Add more cases as needed for different steps
            default:
                // Store generic step data
                $settings = is_array($tenant->settings) ? $tenant->settings : [];
                $settings[$stepName] = $data;
                $tenant->settings = $settings;
                $tenant->save();
                break;
        }
    }
}
