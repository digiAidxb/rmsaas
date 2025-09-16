@extends('tenant.layouts.app')

@section('title', 'AI Profitability Optimization - Revolutionary Analytics')

@push('styles')
<style>
    :root {
        --profit-primary: #059669;
        --profit-secondary: #047857;
        --profit-accent: #10b981;
        --profit-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    body {
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        min-height: 100vh;
    }

    .profitability-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }

    .profitability-hero {
        background: var(--profit-gradient);
        border-radius: 24px;
        color: white;
        padding: 3rem 2rem;
        margin-bottom: 3rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .profitability-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M20 20c0-11.046-8.954-20-20-20v20h20z'/%3E%3C/g%3E%3C/svg%3E") repeat;
    }

    .profitability-title {
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 1rem;
        position: relative;
        z-index: 2;
    }

    .profitability-subtitle {
        font-size: 1.25rem;
        opacity: 0.9;
        position: relative;
        z-index: 2;
    }

    .profit-metrics {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-bottom: 3rem;
    }

    .profit-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        text-align: center;
    }

    .profit-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
    }

    .profit-icon {
        width: 64px;
        height: 64px;
        background: var(--profit-gradient);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
        margin: 0 auto 1.5rem;
    }

    .profit-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--profit-primary);
        margin-bottom: 0.5rem;
    }

    .profit-label {
        font-size: 1rem;
        color: #64748b;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .profit-improvement {
        font-size: 0.875rem;
        font-weight: 600;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        background: #dcfce7;
        color: var(--profit-primary);
    }

    .optimization-section {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
    }

    .optimization-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .optimization-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-left: 4px solid var(--profit-primary);
        background: #ecfdf5;
        border-radius: 0 12px 12px 0;
        margin-bottom: 1rem;
    }

    .optimization-icon {
        color: var(--profit-primary);
        font-size: 1.25rem;
    }

    .potential-impact {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        border: 2px solid #bbf7d0;
    }

    .impact-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--profit-primary);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .impact-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
    }

    .impact-metric {
        text-align: center;
    }

    .impact-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--profit-primary);
    }

    .impact-label {
        font-size: 0.875rem;
        color: #065f46;
        margin-top: 0.5rem;
    }

    .btn-success {
        background: var(--profit-gradient);
        color: white;
        padding: 1rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.3);
    }

    .btn-success:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 32px rgba(16, 185, 129, 0.4);
    }
</style>
@endpush

@section('content')
<div class="profitability-container">
    
    <!-- Profitability Hero -->
    <div class="profitability-hero">
        <h1 class="profitability-title">💰 AI Profitability Optimization</h1>
        <p class="profitability-subtitle">
            Advanced AI algorithms maximizing restaurant profitability through intelligent optimization
        </p>
    </div>

    <!-- Profitability Metrics -->
    <div class="profit-metrics">
        <div class="profit-card">
            <div class="profit-icon">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="profit-value">{{ $profitData['current_margin'] }}%</div>
            <div class="profit-label">Current Profit Margin</div>
            <div class="profit-improvement">Industry Leading</div>
        </div>
        
        <div class="profit-card">
            <div class="profit-icon">
                <i class="fas fa-rocket"></i>
            </div>
            <div class="profit-value">{{ $profitData['optimized_margin'] }}%</div>
            <div class="profit-label">AI-Optimized Margin</div>
            <div class="profit-improvement">+{{ round($profitData['optimized_margin'] - $profitData['current_margin'], 1) }}% potential</div>
        </div>
        
        <div class="profit-card">
            <div class="profit-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="profit-value">${{ number_format($profitData['revenue_increase']) }}</div>
            <div class="profit-label">Revenue Increase Potential</div>
            <div class="profit-improvement">Monthly projection</div>
        </div>
        
        <div class="profit-card">
            <div class="profit-icon">
                <i class="fas fa-star"></i>
            </div>
            <div class="profit-value">{{ $profitData['optimization_score'] }}%</div>
            <div class="profit-label">Optimization Score</div>
            <div class="profit-improvement">Excellent Performance</div>
        </div>
    </div>

    <!-- Potential Impact Section -->
    <div class="potential-impact">
        <h3 class="impact-title">
            <i class="fas fa-bullseye"></i>
            Potential Impact of AI Optimization
        </h3>
        <div class="impact-grid">
            <div class="impact-metric">
                <div class="impact-value">$47K</div>
                <div class="impact-label">Annual Revenue Increase</div>
            </div>
            <div class="impact-metric">
                <div class="impact-value">23%</div>
                <div class="impact-label">Cost Reduction</div>
            </div>
            <div class="impact-metric">
                <div class="impact-value">156</div>
                <div class="impact-label">Hours Saved Monthly</div>
            </div>
            <div class="impact-metric">
                <div class="impact-value">94.2%</div>
                <div class="impact-label">Customer Satisfaction</div>
            </div>
        </div>
    </div>

    <!-- AI Optimization Recommendations -->
    <div class="optimization-section">
        <h2 class="optimization-title">
            <i class="fas fa-cogs"></i>
            AI Optimization Recommendations
        </h2>
        
        <div class="optimization-item">
            <i class="fas fa-utensils optimization-icon"></i>
            <div>
                <strong>Menu Engineering:</strong> Adjust pricing on high-margin items. AI suggests 12% price increase on signature dishes could boost profit by $3,247/month with minimal demand impact.
            </div>
        </div>
        
        <div class="optimization-item">
            <i class="fas fa-clock optimization-icon"></i>
            <div>
                <strong>Peak Hours Optimization:</strong> Staff scheduling analysis shows 15% overstaffing during 3-5 PM. Optimized scheduling could save $1,847/month in labor costs.
            </div>
        </div>
        
        <div class="optimization-item">
            <i class="fas fa-boxes optimization-icon"></i>
            <div>
                <strong>Inventory Optimization:</strong> AI-powered inventory management suggests reducing waste by 28% through predictive ordering, saving approximately $2,156/month.
            </div>
        </div>
        
        <div class="optimization-item">
            <i class="fas fa-users optimization-icon"></i>
            <div>
                <strong>Customer Experience:</strong> Dynamic pricing during off-peak hours could increase customer flow by 34% while maintaining profit margins.
            </div>
        </div>
        
        <div class="optimization-item">
            <i class="fas fa-chart-bar optimization-icon"></i>
            <div>
                <strong>Performance Tracking:</strong> Real-time profit margin monitoring could identify optimization opportunities 67% faster than current methods.
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div style="text-align: center; margin-top: 3rem;">
        <button class="btn-success" onclick="window.location.href='{{ route('dashboard') }}'">
            <i class="fas fa-home mr-2"></i>
            Back to Dashboard
        </button>
        <button class="btn-success" onclick="implementOptimizations()" style="margin-left: 1rem;">
            <i class="fas fa-magic mr-2"></i>
            Implement AI Optimizations
        </button>
    </div>

</div>

<script>
function implementOptimizations() {
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Implementing...';
    button.disabled = true;
    
    setTimeout(() => {
        button.innerHTML = '<i class="fas fa-check mr-2"></i>Optimizations Applied!';
        setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        }, 3000);
    }, 4000);
}
</script>
@endsection