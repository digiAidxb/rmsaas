@extends('tenant.layouts.app')

@section('title', 'AI Loss Analysis - Revolutionary Analytics')

@push('styles')
<style>
    :root {
        --analytics-primary: #ef4444;
        --analytics-secondary: #dc2626;
        --analytics-success: #10b981;
        --analytics-warning: #f59e0b;
        --analytics-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    body {
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        min-height: 100vh;
    }

    .analytics-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }

    .analytics-hero {
        background: var(--analytics-gradient);
        border-radius: 24px;
        color: white;
        padding: 3rem 2rem;
        margin-bottom: 3rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .analytics-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M20 20c0-11.046-8.954-20-20-20v20h20z'/%3E%3C/g%3E%3C/svg%3E") repeat;
    }

    .analytics-title {
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 1rem;
        position: relative;
        z-index: 2;
    }

    .analytics-subtitle {
        font-size: 1.25rem;
        opacity: 0.9;
        position: relative;
        z-index: 2;
    }

    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-bottom: 3rem;
    }

    .metric-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
        border: 1px solid #f3f4f6;
        transition: all 0.3s ease;
        text-align: center;
    }

    .metric-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
    }

    .metric-icon {
        width: 64px;
        height: 64px;
        background: var(--analytics-gradient);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
        margin: 0 auto 1.5rem;
    }

    .metric-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--analytics-primary);
        margin-bottom: 0.5rem;
    }

    .metric-label {
        font-size: 1rem;
        color: #64748b;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .metric-change {
        font-size: 0.875rem;
        font-weight: 600;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
    }

    .metric-change.positive {
        background: #dcfce7;
        color: var(--analytics-success);
    }

    .metric-change.negative {
        background: #fef2f2;
        color: var(--analytics-primary);
    }

    .insights-section {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
    }

    .insights-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .insight-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-left: 4px solid var(--analytics-primary);
        background: #fef2f2;
        border-radius: 0 12px 12px 0;
        margin-bottom: 1rem;
    }

    .insight-icon {
        color: var(--analytics-primary);
        font-size: 1.25rem;
    }

    .btn-primary {
        background: var(--analytics-gradient);
        color: white;
        padding: 1rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 8px 24px rgba(239, 68, 68, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 32px rgba(239, 68, 68, 0.4);
    }
</style>
@endpush

@section('content')
<div class="analytics-container">
    
    <!-- Analytics Hero -->
    <div class="analytics-hero">
        <h1 class="analytics-title">🧠 AI Loss Analysis</h1>
        <p class="analytics-subtitle">
            Revolutionary AI algorithms identifying and preventing restaurant losses in real-time
        </p>
    </div>

    <!-- Loss Metrics -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-icon">
                <i class="fas fa-chart-line-down"></i>
            </div>
            <div class="metric-value">${{ number_format($lossData['total_losses']) }}</div>
            <div class="metric-label">Total Losses Identified</div>
            <div class="metric-change negative">-15.2% from last month</div>
        </div>
        
        <div class="metric-card">
            <div class="metric-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="metric-value">${{ number_format($lossData['prevented_losses']) }}</div>
            <div class="metric-label">Losses Prevented</div>
            <div class="metric-change positive">+28.4% prevention rate</div>
        </div>
        
        <div class="metric-card">
            <div class="metric-icon">
                <i class="fas fa-piggy-bank"></i>
            </div>
            <div class="metric-value">${{ number_format($lossData['monthly_savings']) }}</div>
            <div class="metric-label">Monthly Savings</div>
            <div class="metric-change positive">+42.1% this month</div>
        </div>
        
        <div class="metric-card">
            <div class="metric-icon">
                <i class="fas fa-tachometer-alt"></i>
            </div>
            <div class="metric-value">{{ $lossData['efficiency_score'] }}%</div>
            <div class="metric-label">AI Efficiency Score</div>
            <div class="metric-change positive">+5.8% improvement</div>
        </div>
    </div>

    <!-- AI Insights -->
    <div class="insights-section">
        <h2 class="insights-title">
            <i class="fas fa-brain"></i>
            AI-Generated Insights
        </h2>
        
        <div class="insight-item">
            <i class="fas fa-exclamation-triangle insight-icon"></i>
            <div>
                <strong>High-Risk Period Detected:</strong> Inventory losses spike by 34% between 2-4 PM. Consider implementing additional monitoring during this window.
            </div>
        </div>
        
        <div class="insight-item">
            <i class="fas fa-lightbulb insight-icon"></i>
            <div>
                <strong>Optimization Opportunity:</strong> Menu items with 15%+ waste could be reformulated or portioned differently to reduce losses by estimated $1,247/month.
            </div>
        </div>
        
        <div class="insight-item">
            <i class="fas fa-users insight-icon"></i>
            <div>
                <strong>Staff Performance:</strong> Evening shift shows 23% better loss prevention metrics. Consider cross-training morning staff on evening procedures.
            </div>
        </div>
        
        <div class="insight-item">
            <i class="fas fa-calendar-alt insight-icon"></i>
            <div>
                <strong>Seasonal Pattern:</strong> Historical data suggests 18% increase in losses during weekend periods. Proactive scheduling recommended.
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div style="text-align: center; margin-top: 3rem;">
        <button class="btn-primary" onclick="window.location.href='{{ route('dashboard') }}'">
            <i class="fas fa-home mr-2"></i>
            Back to Dashboard
        </button>
        <button class="btn-primary" onclick="generateReport()" style="margin-left: 1rem;">
            <i class="fas fa-file-pdf mr-2"></i>
            Generate AI Report
        </button>
    </div>

</div>

<script>
function generateReport() {
    // Mock report generation
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generating...';
    button.disabled = true;
    
    setTimeout(() => {
        button.innerHTML = '<i class="fas fa-check mr-2"></i>Report Generated!';
        setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        }, 2000);
    }, 3000);
}
</script>
@endsection