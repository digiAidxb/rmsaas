@extends('tenant.layouts.modal')

@section('title', 'Welcome to RMSaaS - Restaurant Management Setup')

@push('styles')
<style>
/* Enterprise Onboarding Styles - Salesforce Design System Inspired */
.slds-onboarding {
    background: var(--slds-color-neutral-1);
    min-height: 100%;
    overflow-y: auto;
}

.slds-onboarding__header {
    background: linear-gradient(135deg, var(--slds-color-brand) 0%, var(--slds-color-brand-dark) 100%);
    color: var(--slds-color-neutral-1);
    padding: var(--slds-spacing-x-large) var(--slds-spacing-large);
    text-align: center;
    position: relative;
    overflow: hidden;
}

.slds-onboarding__header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M20 20c0-11.046-8.954-20-20-20v20h20z'/%3E%3C/g%3E%3C/svg%3E") repeat;
}

.slds-onboarding__title {
    position: relative;
    z-index: 2;
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: var(--slds-spacing-small);
    letter-spacing: -0.01em;
}

.slds-onboarding__subtitle {
    position: relative;
    z-index: 2;
    font-size: var(--slds-font-size-5);
    opacity: 0.9;
    font-weight: 400;
    max-width: 600px;
    margin: 0 auto;
}

.slds-onboarding__body {
    padding: var(--slds-spacing-x-large);
}

.slds-progress-indicator {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: var(--slds-spacing-x-large);
    padding: var(--slds-spacing-medium);
    background: var(--slds-color-neutral-2);
    border-radius: 0.375rem;
    border: 1px solid var(--slds-color-neutral-3);
}

.slds-progress-indicator__step {
    display: flex;
    align-items: center;
    font-size: var(--slds-font-size-3);
    font-weight: 500;
    color: var(--slds-color-neutral-8);
}

.slds-progress-indicator__marker {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: var(--slds-color-brand);
    color: var(--slds-color-neutral-1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: var(--slds-font-size-2);
    font-weight: 700;
    margin-right: var(--slds-spacing-x-small);
}

.slds-progress-indicator__text {
    color: var(--slds-color-neutral-10);
    font-weight: 600;
}

.slds-setup-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: var(--slds-spacing-medium);
    margin-bottom: var(--slds-spacing-x-large);
}

.slds-setup-card {
    background: var(--slds-color-neutral-1);
    border: 2px solid var(--slds-color-neutral-3);
    border-radius: 0.375rem;
    padding: var(--slds-spacing-large);
    text-align: center;
    cursor: pointer;
    transition: all 0.15s ease-in-out;
    position: relative;
    min-height: 320px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.slds-setup-card:hover {
    border-color: var(--slds-color-brand);
    box-shadow: var(--slds-shadow-2);
    transform: translateY(-2px);
}

.slds-setup-card.slds-is-selected {
    border-color: var(--slds-color-brand);
    background: rgba(21, 137, 238, 0.02);
    box-shadow: var(--slds-shadow-3);
}

.slds-setup-card__icon {
    width: 56px;
    height: 56px;
    border-radius: 0.375rem;
    background: var(--slds-color-brand);
    color: var(--slds-color-neutral-1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin: 0 auto var(--slds-spacing-medium);
    box-shadow: var(--slds-shadow-2);
}

.slds-setup-card__icon.slds-icon-success {
    background: var(--slds-color-success);
}

.slds-setup-card__icon.slds-icon-warning {
    background: var(--slds-color-warning);
}

.slds-setup-card__title {
    font-size: var(--slds-font-size-6);
    font-weight: 700;
    color: var(--slds-color-neutral-10);
    margin-bottom: var(--slds-spacing-small);
    line-height: 1.25;
}

.slds-setup-card__description {
    font-size: var(--slds-font-size-4);
    color: var(--slds-color-neutral-8);
    line-height: 1.5;
    margin-bottom: var(--slds-spacing-medium);
    flex: 1;
}

.slds-setup-card__features {
    list-style: none;
    padding: 0;
    margin: 0;
    text-align: left;
}

.slds-setup-card__feature {
    display: flex;
    align-items: center;
    font-size: var(--slds-font-size-3);
    color: var(--slds-color-neutral-8);
    margin-bottom: var(--slds-spacing-xx-small);
}

.slds-setup-card__feature-icon {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: var(--slds-color-success);
    color: var(--slds-color-neutral-1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    margin-right: var(--slds-spacing-x-small);
    flex-shrink: 0;
}

.slds-form {
    background: var(--slds-color-neutral-2);
    border: 1px solid var(--slds-color-neutral-3);
    border-radius: 0.375rem;
    padding: var(--slds-spacing-large);
    margin: var(--slds-spacing-large) 0;
    display: none;
}

.slds-form.slds-show {
    display: block;
    animation: slds-slide-down 0.2s ease-out;
}

@keyframes slds-slide-down {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.slds-form__group {
    margin-bottom: var(--slds-spacing-medium);
}

.slds-form__label {
    font-size: var(--slds-font-size-4);
    font-weight: 600;
    color: var(--slds-color-neutral-10);
    display: block;
    margin-bottom: var(--slds-spacing-xx-small);
}

.slds-select {
    width: 100%;
    padding: var(--slds-spacing-small);
    border: 1px solid var(--slds-color-neutral-4);
    border-radius: 0.25rem;
    font-size: var(--slds-font-size-4);
    background: var(--slds-color-neutral-1);
    color: var(--slds-color-neutral-10);
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.slds-select:focus {
    border-color: var(--slds-color-brand);
    outline: 0;
    box-shadow: 0 0 0 2px rgba(21, 137, 238, 0.1);
}

.slds-checkbox {
    display: flex;
    align-items: center;
    font-size: var(--slds-font-size-4);
    color: var(--slds-color-neutral-10);
}

.slds-checkbox__input {
    margin-right: var(--slds-spacing-x-small);
}

.slds-button-group {
    display: flex;
    justify-content: center;
    gap: var(--slds-spacing-small);
    margin-top: var(--slds-spacing-x-large);
}

.slds-button {
    padding: var(--slds-spacing-small) var(--slds-spacing-large);
    border-radius: 0.25rem;
    font-size: var(--slds-font-size-4);
    font-weight: 600;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.15s ease-in-out;
    display: inline-flex;
    align-items: center;
    text-decoration: none;
    min-height: 2.5rem;
}

.slds-button_brand {
    background: var(--slds-color-brand);
    color: var(--slds-color-neutral-1);
    border-color: var(--slds-color-brand);
}

.slds-button_brand:hover {
    background: var(--slds-color-brand-dark);
    border-color: var(--slds-color-brand-dark);
    color: var(--slds-color-neutral-1);
}

.slds-button_brand:disabled {
    background: var(--slds-color-neutral-4);
    border-color: var(--slds-color-neutral-4);
    color: var(--slds-color-neutral-8);
    cursor: not-allowed;
}

.slds-button_neutral {
    background: var(--slds-color-neutral-1);
    color: var(--slds-color-neutral-10);
    border-color: var(--slds-color-neutral-4);
}

.slds-button_neutral:hover {
    background: var(--slds-color-neutral-2);
    border-color: var(--slds-color-neutral-8);
}

.slds-button_success {
    background: var(--slds-color-success);
    color: var(--slds-color-neutral-1);
    border-color: var(--slds-color-success);
}

.slds-button_success:hover {
    background: #027843;
    border-color: #027843;
}

.slds-button_warning {
    background: var(--slds-color-warning);
    color: var(--slds-color-neutral-1);
    border-color: var(--slds-color-warning);
}

.slds-button_warning:hover {
    background: #dd7a01;
    border-color: #dd7a01;
}

.slds-button__icon {
    margin-right: var(--slds-spacing-xx-small);
    font-size: var(--slds-font-size-3);
}

.slds-button__icon_left {
    margin-right: var(--slds-spacing-xx-small);
    margin-left: 0;
}

.slds-button__icon_right {
    margin-left: var(--slds-spacing-xx-small);
    margin-right: 0;
}

/* Responsive Design */
@media (max-width: 768px) {
    .slds-setup-options {
        grid-template-columns: 1fr;
    }
    
    .slds-onboarding__header {
        padding: var(--slds-spacing-large) var(--slds-spacing-medium);
    }
    
    .slds-onboarding__body {
        padding: var(--slds-spacing-large) var(--slds-spacing-medium);
    }
    
    .slds-button-group {
        flex-direction: column;
        align-items: center;
    }
    
    .slds-button {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endpush

@section('content')
<div class="slds-onboarding">
    <!-- Header Section -->
    <header class="slds-onboarding__header">
        <h1 class="slds-onboarding__title">Welcome to RMSaaS</h1>
        <p class="slds-onboarding__subtitle">
            Set up your restaurant management system in minutes with AI-powered data import and intelligent analytics.
        </p>
    </header>

    <!-- Main Content -->
    <main class="slds-onboarding__body">
        
        <!-- Progress Indicator -->
        <div class="slds-progress-indicator">
            <div class="slds-progress-indicator__step">
                <div class="slds-progress-indicator__marker">{{ $progress['completed_steps'] + 1 }}</div>
                <span class="slds-progress-indicator__text">
                    {{ $progress['current_step'] }} - Step {{ $progress['completed_steps'] + 1 }} of {{ $progress['total_steps'] }}
                </span>
            </div>
        </div>

        @if($progress['completed_steps'] > 0)
        <!-- Progress Status -->
        <div class="slds-progress-status" style="background: var(--slds-color-neutral-2); padding: var(--slds-spacing-medium); border-radius: 0.375rem; margin-bottom: var(--slds-spacing-x-large); border: 1px solid var(--slds-color-neutral-3);">
            <div style="display: flex; align-items: center; margin-bottom: var(--slds-spacing-small);">
                <svg style="width: 20px; height: 20px; color: var(--slds-color-success); margin-right: var(--slds-spacing-x-small);" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span style="font-weight: 600; color: var(--slds-color-neutral-10);">Setup Progress Detected</span>
            </div>
            <p style="color: var(--slds-color-neutral-8); font-size: var(--slds-font-size-4); margin: 0;">
                @if($progress['import_preference'] === 'now')
                    You previously started importing your restaurant data. You can continue the import process or choose a different option.
                @elseif($progress['import_preference'] === 'demo')
                    You previously selected demo data. You can continue with demo data or choose a different option.
                @elseif($progress['import_preference'] === 'skip')
                    You previously chose to skip setup. You can continue without data or choose a different option.
                @else
                    You have some progress in your setup. You can continue or start fresh with a new option.
                @endif
            </p>
        </div>
        @endif

        <!-- Setup Options -->
        <div class="slds-setup-options">
            
            <!-- Import Data Now -->
            <div class="slds-setup-card" data-option="now" onclick="selectOption('now')">
                <div>
                    <div class="slds-setup-card__icon">
                        <i class="fas fa-upload"></i>
                    </div>
                    <h3 class="slds-setup-card__title">Import Restaurant Data</h3>
                    <p class="slds-setup-card__description">
                        Upload your existing POS data files and let our AI system process everything automatically with enterprise-grade precision.
                    </p>
                </div>
                <ul class="slds-setup-card__features">
                    <li class="slds-setup-card__feature">
                        <div class="slds-setup-card__feature-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <span>Supports 9+ POS systems</span>
                    </li>
                    <li class="slds-setup-card__feature">
                        <div class="slds-setup-card__feature-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <span>AI-powered field mapping</span>
                    </li>
                    <li class="slds-setup-card__feature">
                        <div class="slds-setup-card__feature-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <span>95%+ accuracy rate</span>
                    </li>
                    <li class="slds-setup-card__feature">
                        <div class="slds-setup-card__feature-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <span>2-3 minute setup time</span>
                    </li>
                </ul>
            </div>

            <!-- Explore with Demo Data -->
            <div class="slds-setup-card" data-option="demo" onclick="selectOption('demo')">
                <div>
                    <div class="slds-setup-card__icon slds-icon-success">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="slds-setup-card__title">Explore with Demo Data</h3>
                    <p class="slds-setup-card__description">
                        Load comprehensive sample data to explore all platform features immediately. Perfect for evaluation and training.
                    </p>
                </div>
                <ul class="slds-setup-card__features">
                    <li class="slds-setup-card__feature">
                        <div class="slds-setup-card__feature-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <span>Complete menu & inventory</span>
                    </li>
                    <li class="slds-setup-card__feature">
                        <div class="slds-setup-card__feature-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <span>7 days of sales data</span>
                    </li>
                    <li class="slds-setup-card__feature">
                        <div class="slds-setup-card__feature-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <span>Recipe & cost analysis</span>
                    </li>
                    <li class="slds-setup-card__feature">
                        <div class="slds-setup-card__feature-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <span>Instant feature access</span>
                    </li>
                </ul>
            </div>

            <!-- Skip Setup -->
            <div class="slds-setup-card" data-option="skip" onclick="selectOption('skip')">
                <div>
                    <div class="slds-setup-card__icon slds-icon-warning">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                    <h3 class="slds-setup-card__title">Skip for Now</h3>
                    <p class="slds-setup-card__description">
                        Continue to the dashboard without any initial data. You can import your data or load demo content anytime later.
                    </p>
                </div>
                <ul class="slds-setup-card__features">
                    <li class="slds-setup-card__feature">
                        <div class="slds-setup-card__feature-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <span>Empty workspace</span>
                    </li>
                    <li class="slds-setup-card__feature">
                        <div class="slds-setup-card__feature-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <span>Manual configuration</span>
                    </li>
                    <li class="slds-setup-card__feature">
                        <div class="slds-setup-card__feature-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <span>Import available later</span>
                    </li>
                    <li class="slds-setup-card__feature">
                        <div class="slds-setup-card__feature-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <span>Full control</span>
                    </li>
                </ul>
            </div>

        </div>

        <!-- Setup Form (Hidden by default) -->
        <div class="slds-form" id="setupForm">
            <h3 class="slds-text-heading_medium slds-m-bottom_medium">Setup Information</h3>
            
            <form method="POST" action="{{ route('onboarding.import.quick') }}" id="onboardingForm">
                @csrf
                
                <div class="slds-form__group" id="posSystemGroup" style="display: none;">
                    <label for="pos_system" class="slds-form__label">Current POS System</label>
                    <select name="pos_system" id="pos_system" class="slds-select">
                        <option value="">Select your POS system</option>
                        <option value="square">Square</option>
                        <option value="toast">Toast</option>
                        <option value="clover">Clover</option>
                        <option value="lightspeed">Lightspeed</option>
                        <option value="touchbistro">TouchBistro</option>
                        <option value="resy">Resy</option>
                        <option value="opentable">OpenTable</option>
                        <option value="aloha">Aloha</option>
                        <option value="micros">Micros</option>
                        <option value="other">Other / Not Sure</option>
                    </select>
                </div>

                <div class="slds-form__group" id="existingDataGroup" style="display: none;">
                    <label class="slds-checkbox">
                        <input type="checkbox" name="has_existing_data" value="1" class="slds-checkbox__input">
                        <span>I have existing menu and sales data ready to import</span>
                    </label>
                </div>

                <input type="hidden" name="import_preference" id="import_preference" value="">
                
                <div class="slds-button-group">
                    <button type="submit" class="slds-button slds-button_brand" id="continueButton" disabled>
                        <i class="fas fa-arrow-right slds-button__icon slds-button__icon_left"></i>
                        Continue
                    </button>
                    <button type="button" class="slds-button slds-button_neutral" onclick="goBack()">
                        <i class="fas fa-arrow-left slds-button__icon slds-button__icon_left"></i>
                        Back
                    </button>
                </div>
            </form>
        </div>

        <!-- Main Action Buttons (Shown by default) -->
        <div class="slds-button-group" id="mainActions">
            <button type="button" class="slds-button slds-button_brand" onclick="proceedWithSelection()" disabled id="proceedButton">
                <i class="fas fa-arrow-right slds-button__icon slds-button__icon_left"></i>
                Select an Option Above
            </button>
        </div>

    </main>
</div>

<script>
// Enterprise Onboarding Logic - Blessed by Lord Bhairava
let selectedOption = null;

function selectOption(option) {
    console.log('🚀 Option selected:', option);
    
    // Remove previous selections
    document.querySelectorAll('.slds-setup-card').forEach(card => {
        card.classList.remove('slds-is-selected');
    });
    
    // Mark selected card
    const selectedCard = document.querySelector(`[data-option="${option}"]`);
    if (selectedCard) {
        selectedCard.classList.add('slds-is-selected');
    }
    
    selectedOption = option;
    
    // Update proceed button
    const proceedButton = document.getElementById('proceedButton');
    proceedButton.disabled = false;
    
    // Update button text and style based on selection
    if (option === 'now') {
        proceedButton.innerHTML = '<i class="fas fa-upload slds-button__icon slds-button__icon_left"></i>Start Data Import';
        proceedButton.className = 'slds-button slds-button_brand';
    } else if (option === 'demo') {
        proceedButton.innerHTML = '<i class="fas fa-star slds-button__icon slds-button__icon_left"></i>Load Demo Data';
        proceedButton.className = 'slds-button slds-button_success';
    } else if (option === 'skip') {
        proceedButton.innerHTML = '<i class="fas fa-arrow-right slds-button__icon slds-button__icon_left"></i>Skip Setup';
        proceedButton.className = 'slds-button slds-button_warning';
    }
}

function proceedWithSelection() {
    if (!selectedOption) return;
    
    console.log('📈 Proceeding with option:', selectedOption);
    
    if (selectedOption === 'demo') {
        // Handle demo data loading immediately
        loadDemoData();
    } else if (selectedOption === 'skip') {
        // Handle skip immediately
        skipSetup();
    } else {
        // Show form for import option
        showSetupForm();
    }
}

function showSetupForm() {
    document.getElementById('mainActions').style.display = 'none';
    
    const setupForm = document.getElementById('setupForm');
    setupForm.classList.add('slds-show');
    
    // Show relevant form fields
    if (selectedOption === 'now') {
        document.getElementById('posSystemGroup').style.display = 'block';
        document.getElementById('existingDataGroup').style.display = 'block';
    }
    
    document.getElementById('import_preference').value = selectedOption;
    
    // Update form button
    const continueButton = document.getElementById('continueButton');
    continueButton.disabled = false;
    continueButton.innerHTML = '<i class="fas fa-rocket slds-button__icon slds-button__icon_left"></i>Start AI Import';
    continueButton.className = 'slds-button slds-button_brand';
}

function goBack() {
    document.getElementById('setupForm').classList.remove('slds-show');
    document.getElementById('mainActions').style.display = 'flex';
}

function loadDemoData() {
    const proceedButton = document.getElementById('proceedButton');
    proceedButton.innerHTML = '<i class="fas fa-spinner fa-spin slds-button__icon slds-button__icon_left"></i>Loading Demo Data...';
    proceedButton.disabled = true;
    
    // Call the onboarding skip endpoint with demo flag
    fetch('{{ route("onboarding.skip") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            load_demo_data: true
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('✅ Demo data response:', data);
        if (data.success) {
            // Show success message
            proceedButton.innerHTML = '<i class="fas fa-check slds-button__icon slds-button__icon_left"></i>Demo Data Loaded!';
            proceedButton.className = 'slds-button slds-button_success';
            
            // Redirect after brief delay
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1000);
        } else {
            throw new Error(data.message || 'Demo data loading failed');
        }
    })
    .catch(error => {
        console.error('❌ Demo data error:', error);
        proceedButton.innerHTML = '<i class="fas fa-exclamation-triangle slds-button__icon slds-button__icon_left"></i>Demo Data Failed';
        proceedButton.className = 'slds-button slds-button_error';
        
        setTimeout(() => {
            proceedButton.innerHTML = '<i class="fas fa-star slds-button__icon slds-button__icon_left"></i>Load Demo Data';
            proceedButton.className = 'slds-button slds-button_success';
            proceedButton.disabled = false;
        }, 3000);
    });
}

function skipSetup() {
    const proceedButton = document.getElementById('proceedButton');
    proceedButton.innerHTML = '<i class="fas fa-spinner fa-spin slds-button__icon slds-button__icon_left"></i>Completing Setup...';
    proceedButton.disabled = true;
    
    // Call the onboarding skip endpoint without demo data
    fetch('{{ route("onboarding.skip") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            load_demo_data: false
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('✅ Skip response:', data);
        if (data.success) {
            // Show success message
            proceedButton.innerHTML = '<i class="fas fa-check slds-button__icon slds-button__icon_left"></i>Setup Complete!';
            proceedButton.className = 'slds-button slds-button_success';
            
            // Redirect after brief delay
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1000);
        } else {
            throw new Error(data.message || 'Setup completion failed');
        }
    })
    .catch(error => {
        console.error('❌ Skip setup error:', error);
        proceedButton.innerHTML = '<i class="fas fa-exclamation-triangle slds-button__icon slds-button__icon_left"></i>Setup Failed';
        proceedButton.className = 'slds-button slds-button_error';
        
        setTimeout(() => {
            proceedButton.innerHTML = '<i class="fas fa-arrow-right slds-button__icon slds-button__icon_left"></i>Skip Setup';
            proceedButton.className = 'slds-button slds-button_warning';
            proceedButton.disabled = false;
        }, 3000);
    });
}

// Form submission handling
document.getElementById('onboardingForm').addEventListener('submit', function(e) {
    const continueButton = document.getElementById('continueButton');
    continueButton.innerHTML = '<i class="fas fa-spinner fa-spin slds-button__icon slds-button__icon_left"></i>Preparing Import...';
    continueButton.disabled = true;
});

// Auto-submit when POS system is selected for import
document.getElementById('pos_system').addEventListener('change', function() {
    if (selectedOption === 'now' && this.value) {
        setTimeout(() => {
            document.getElementById('onboardingForm').submit();
        }, 500);
    }
});

console.log('🙏 Enterprise Onboarding Initialized - Blessed by Lord Bhairava');
</script>
@endsection