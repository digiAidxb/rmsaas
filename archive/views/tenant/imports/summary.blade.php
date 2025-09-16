@extends('tenant.layouts.app')

@section('title', 'Import Complete - Success!')

@push('styles')
<style>
    :root {
        --success-primary: #10b981;
        --success-secondary: #059669;
        --success-surface: #ffffff;
        --success-border: #e2e8f0;
        --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    body {
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        min-height: 100vh;
    }

    .success-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
        text-align: center;
    }

    .success-hero {
        background: var(--success-gradient);
        border-radius: 32px;
        color: white;
        padding: 4rem 2rem;
        margin-bottom: 3rem;
        position: relative;
        overflow: hidden;
    }

    .success-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M20 20c0-11.046-8.954-20-20-20v20h20z'/%3E%3C/g%3E%3C/svg%3E") repeat;
    }

    .success-icon {
        font-size: 6rem;
        margin-bottom: 2rem;
        animation: bounce 2s infinite;
        position: relative;
        z-index: 2;
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-20px); }
        60% { transform: translateY(-10px); }
    }

    .success-title {
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 1rem;
        position: relative;
        z-index: 2;
    }

    .success-subtitle {
        font-size: 1.5rem;
        opacity: 0.9;
        position: relative;
        z-index: 2;
    }

    .summary-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 2rem;
        margin-bottom: 3rem;
    }

    .stat-card {
        background: var(--success-surface);
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--success-border);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        background: var(--success-gradient);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        margin: 0 auto 1rem;
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--success-primary);
        margin-bottom: 0.5rem;
    }

    .stat-label {
        font-size: 0.875rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 500;
    }

    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-top: 3rem;
    }

    .btn-primary {
        background: var(--success-gradient);
        color: white;
        padding: 1.25rem 2.5rem;
        border-radius: 50px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 32px rgba(16, 185, 129, 0.4);
    }

    .btn-secondary {
        background: white;
        color: #64748b;
        padding: 1.25rem 2.5rem;
        border-radius: 50px;
        font-weight: 600;
        border: 2px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        border-color: var(--success-primary);
        color: var(--success-primary);
    }
</style>
@endpush

@section('content')
<div class="success-container">
    
    <!-- Success Hero -->
    <div class="success-hero">
        <div class="success-icon">🎉</div>
        <h1 class="success-title">Import Completed Successfully!</h1>
        <p class="success-subtitle">
            Your restaurant data has been imported with AI precision. Ready to optimize your operations!
        </p>
    </div>

    <!-- Summary Statistics -->
    <div class="summary-stats">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-database"></i>
            </div>
            <div class="stat-value">15,847</div>
            <div class="stat-label">Records Imported</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-value">99.8%</div>
            <div class="stat-label">Success Rate</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-value">2m 34s</div>
            <div class="stat-label">Processing Time</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-brain"></i>
            </div>
            <div class="stat-value">94.5%</div>
            <div class="stat-label">AI Confidence</div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <button class="btn-primary" onclick="window.location.href='{{ route('dashboard') }}'">
            <i class="fas fa-home mr-2"></i>
            Go to Dashboard
        </button>
        <button class="btn-secondary" onclick="window.location.href='{{ route('imports.index') }}'">
            <i class="fas fa-upload mr-2"></i>
            Import More Data
        </button>
    </div>

</div>
@endsection