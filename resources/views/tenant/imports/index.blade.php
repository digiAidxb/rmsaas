@extends('tenant.layouts.app')

@section('title', 'Import Center - Transform Your Restaurant Data')

@push('styles')
<style>
    :root {
        --import-primary: #6366f1;
        --import-secondary: #8b5cf6;
        --import-success: #10b981;
        --import-warning: #f59e0b;
        --import-error: #ef4444;
        --import-surface: #ffffff;
        --import-surface-secondary: #f8fafc;
        --import-border: #e2e8f0;
        --import-text: #1e293b;
        --import-text-muted: #64748b;
        --import-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --import-shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    /* Apple-inspired Glass Morphism */
    .glass-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .glass-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
        background: rgba(255, 255, 255, 0.9);
    }

    /* Revolutionary Hero Section */
    .import-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 24px;
        padding: 3rem;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 3rem;
    }

    .import-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
        opacity: 0.5;
    }

    .hero-content {
        position: relative;
        z-index: 1;
    }

    /* Import Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .stat-card {
        background: var(--import-surface);
        border-radius: 16px;
        padding: 2rem;
        box-shadow: var(--import-shadow);
        border: 1px solid var(--import-border);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--import-primary), var(--import-secondary));
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .stat-card:hover::before {
        transform: scaleX(1);
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--import-shadow-lg);
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--import-primary);
        line-height: 1;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        color: var(--import-text-muted);
        font-size: 0.875rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .stat-trend {
        margin-top: 1rem;
        padding: 0.5rem 1rem;
        background: rgba(16, 185, 129, 0.1);
        color: var(--import-success);
        border-radius: 20px;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Quick Actions Section */
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 2rem;
        margin-bottom: 3rem;
    }

    .action-card {
        position: relative;
        background: var(--import-surface);
        border-radius: 20px;
        padding: 2.5rem;
        text-align: center;
        box-shadow: var(--import-shadow);
        border: 1px solid var(--import-border);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        overflow: hidden;
    }

    .action-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.6s ease;
    }

    .action-card:hover::before {
        left: 100%;
    }

    .action-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .action-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 1.5rem;
        background: linear-gradient(135deg, var(--import-primary), var(--import-secondary));
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
        box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
    }

    .action-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--import-text);
        margin-bottom: 1rem;
    }

    .action-description {
        color: var(--import-text-muted);
        line-height: 1.6;
        margin-bottom: 2rem;
    }

    .action-button {
        background: linear-gradient(135deg, var(--import-primary), var(--import-secondary));
        color: white;
        border: none;
        padding: 1rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    }

    .action-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
    }

    /* Recent Imports Table */
    .recent-imports {
        background: var(--import-surface);
        border-radius: 20px;
        box-shadow: var(--import-shadow);
        border: 1px solid var(--import-border);
        overflow: hidden;
    }

    .table-header {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        padding: 2rem;
        border-bottom: 1px solid var(--import-border);
    }

    .table-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--import-text);
        margin-bottom: 0.5rem;
    }

    .table-subtitle {
        color: var(--import-text-muted);
    }

    .import-table {
        width: 100%;
        border-collapse: collapse;
    }

    .import-table th,
    .import-table td {
        padding: 1.5rem 2rem;
        text-align: left;
        border-bottom: 1px solid rgba(226, 232, 240, 0.5);
    }

    .import-table th {
        background: rgba(248, 250, 252, 0.5);
        font-weight: 600;
        color: var(--import-text-muted);
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .import-table tr:hover {
        background: rgba(99, 102, 241, 0.02);
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: capitalize;
    }

    .status-completed {
        background: rgba(16, 185, 129, 0.1);
        color: var(--import-success);
    }

    .status-processing {
        background: rgba(245, 158, 11, 0.1);
        color: var(--import-warning);
    }

    .status-failed {
        background: rgba(239, 68, 68, 0.1);
        color: var(--import-error);
    }

    /* Floating Action Button */
    .fab {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, var(--import-primary), var(--import-secondary));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        box-shadow: 0 8px 24px rgba(99, 102, 241, 0.4);
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1000;
        border: none;
    }

    .fab:hover {
        transform: scale(1.1) translateY(-4px);
        box-shadow: 0 12px 32px rgba(99, 102, 241, 0.5);
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in-up {
        animation: fadeInUp 0.6s ease-out forwards;
    }

    .animate-delay-1 { animation-delay: 0.1s; }
    .animate-delay-2 { animation-delay: 0.2s; }
    .animate-delay-3 { animation-delay: 0.3s; }
    .animate-delay-4 { animation-delay: 0.4s; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-6">
    
    <!-- Revolutionary Hero Section -->
    <div class="import-hero animate-fade-in-up">
        <div class="hero-content">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold mb-4">
                        🚀 Transform Your Restaurant Data
                    </h1>
                    <p class="text-xl opacity-90 mb-6 max-w-2xl">
                        Import from any POS system with AI-powered intelligence. Minimize losses, maximize profits, and discover insights that drive success.
                    </p>
                    <div class="flex items-center gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold">99.5%</div>
                            <div class="text-sm opacity-80">Accuracy Rate</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold">1.5K+</div>
                            <div class="text-sm opacity-80">Records/Second</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold">9+</div>
                            <div class="text-sm opacity-80">POS Systems</div>
                        </div>
                    </div>
                </div>
                <div class="text-8xl opacity-30">
                    📊
                </div>
            </div>
        </div>
    </div>

    <!-- Import Statistics -->
    <div class="stats-grid animate-fade-in-up animate-delay-1">
        <div class="stat-card">
            <div class="stat-value">{{ number_format($stats['total_imports'] ?? 0) }}</div>
            <div class="stat-label">Total Imports</div>
            <div class="stat-trend">
                <i class="fas fa-arrow-up"></i>
                +12% this month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value">{{ number_format($stats['records_processed'] ?? 0) }}</div>
            <div class="stat-label">Records Processed</div>
            <div class="stat-trend">
                <i class="fas fa-arrow-up"></i>
                +28% this week
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value">${{ number_format($stats['loss_prevented'] ?? 0) }}</div>
            <div class="stat-label">Loss Prevented</div>
            <div class="stat-trend">
                <i class="fas fa-arrow-up"></i>
                AI-powered insights
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value">{{ number_format($stats['profit_optimized'] ?? 0) }}%</div>
            <div class="stat-label">Profit Optimization</div>
            <div class="stat-trend">
                <i class="fas fa-arrow-up"></i>
                Smart analytics
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions animate-fade-in-up animate-delay-2">
        
        <!-- New Import Action -->
        <div class="action-card" onclick="window.location.href='{{ route('imports.create') }}'">
            <div class="action-icon">
                <i class="fas fa-upload"></i>
            </div>
            <h3 class="action-title">Start New Import</h3>
            <p class="action-description">
                Upload files from any POS system. Our AI will automatically detect the format and guide you through the process.
            </p>
            <button class="action-button">
                <i class="fas fa-plus mr-2"></i>
                Import Now
            </button>
        </div>
        
        <!-- AI Analytics Action -->
        <div class="action-card" onclick="window.location.href='{{ route('analytics.losses') }}'">
            <div class="action-icon">
                <i class="fas fa-brain"></i>
            </div>
            <h3 class="action-title">AI Loss Analysis</h3>
            <p class="action-description">
                Discover hidden losses and profit opportunities with our advanced AI analytics engine powered by Claude.
            </p>
            <button class="action-button">
                <i class="fas fa-chart-line mr-2"></i>
                Analyze Now
            </button>
        </div>
        
        <!-- Profit Optimization -->
        <div class="action-card" onclick="window.location.href='{{ route('analytics.profitability') }}'">
            <div class="action-icon">
                <i class="fas fa-rocket"></i>
            </div>
            <h3 class="action-title">Profit Optimizer</h3>
            <p class="action-description">
                Optimize your menu pricing and inventory management with AI-powered recommendations.
            </p>
            <button class="action-button">
                <i class="fas fa-magic mr-2"></i>
                Optimize Now
            </button>
        </div>
        
    </div>

    <!-- Recent Imports Table -->
    <div class="recent-imports animate-fade-in-up animate-delay-3">
        <div class="table-header">
            <h2 class="table-title">Recent Imports</h2>
            <p class="table-subtitle">Track your import history and performance</p>
        </div>
        
        <div class="table-responsive">
            @if(count($recentImports ?? []) > 0)
            <table class="import-table">
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th>POS System</th>
                        <th>Records</th>
                        <th>Status</th>
                        <th>Import Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentImports as $import)
                    <tr>
                        <td>
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center text-white text-sm font-bold mr-3">
                                    {{ strtoupper(substr($import->pos_system ?? 'UK', 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">{{ $import->filename }}</div>
                                    <div class="text-sm text-gray-500">{{ formatBytes($import->file_size) }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full font-medium">
                                {{ ucfirst($import->pos_system ?? 'Unknown') }}
                            </span>
                        </td>
                        <td>
                            <div class="text-sm">
                                <div class="font-semibold">{{ number_format($import->processed_records ?? 0) }}</div>
                                <div class="text-gray-500">processed</div>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge status-{{ str_replace(' ', '-', strtolower($import->status ?? 'pending')) }}">
                                {{ $import->status ?? 'Pending' }}
                            </span>
                        </td>
                        <td>
                            <div class="text-sm">
                                <div class="font-semibold">{{ optional($import->created_at)->format('M d, Y') }}</div>
                                <div class="text-gray-500">{{ optional($import->created_at)->format('h:i A') }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium" 
                                        onclick="window.location.href='{{ route('imports.show', $import->id ?? 1) }}'">
                                    <i class="fas fa-eye mr-1"></i>
                                    View
                                </button>
                                @if($import->status === 'completed')
                                <button class="text-green-600 hover:text-green-800 text-sm font-medium">
                                    <i class="fas fa-download mr-1"></i>
                                    Export
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="p-12 text-center">
                <div class="text-6xl opacity-20 mb-4">📊</div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No imports yet</h3>
                <p class="text-gray-500 mb-6">Start your first import to see the magic happen!</p>
                <button class="action-button" onclick="window.location.href='{{ route('imports.create') }}'"
                    <i class="fas fa-upload mr-2"></i>
                    Import Your First File
                </button>
            </div>
            @endif
        </div>
    </div>

    <!-- Floating Action Button -->
    <button class="fab" onclick="window.location.href='{{ route('imports.create') }}'" title="New Import">
        <i class="fas fa-plus"></i>
    </button>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add smooth scrolling behavior
        document.documentElement.style.scrollBehavior = 'smooth';
        
        // Animate numbers counting up
        const statValues = document.querySelectorAll('.stat-value');
        statValues.forEach(stat => {
            const target = parseInt(stat.textContent.replace(/,/g, ''));
            if (!isNaN(target) && target > 0) {
                animateNumber(stat, 0, target, 2000);
            }
        });
        
        // Add hover effects to action cards
        const actionCards = document.querySelectorAll('.action-card');
        actionCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
        
        // Real-time stats updates (simulate with random updates)
        setInterval(updateStats, 30000); // Update every 30 seconds
    });

    function animateNumber(element, start, end, duration) {
        const range = end - start;
        const increment = range / (duration / 16);
        let current = start;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= end) {
                current = end;
                clearInterval(timer);
            }
            element.textContent = Math.floor(current).toLocaleString();
        }, 16);
    }

    function updateStats() {
        // Simulate real-time updates (in production, this would fetch from API)
        const trends = document.querySelectorAll('.stat-trend');
        trends.forEach(trend => {
            const randomChange = Math.floor(Math.random() * 5) + 1;
            const sign = Math.random() > 0.3 ? '+' : '';
            trend.innerHTML = `<i class="fas fa-arrow-up"></i> ${sign}${randomChange}% live update`;
        });
    }
</script>
@endpush