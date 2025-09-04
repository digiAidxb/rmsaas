@extends('tenant.layouts.app')

@section('title', 'Restaurant Analytics Dashboard')

@push('styles')
<style>
    /* Modern Enterprise Color Scheme */
    :root {
        --primary-600: #4f46e5;
        --primary-700: #3730a3;
        --primary-50: #eef2ff;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --gray-900: #111827;
        --green-500: #10b981;
        --green-600: #059669;
        --blue-500: #3b82f6;
        --blue-600: #2563eb;
        --amber-500: #f59e0b;
        --amber-600: #d97706;
        --red-500: #ef4444;
        --red-600: #dc2626;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    }

    body {
        background-color: var(--gray-50);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    /* Header Section */
    .dashboard-header {
        background: linear-gradient(135deg, var(--primary-600) 0%, var(--primary-700) 100%);
        color: white;
        padding: 2rem;
        margin-bottom: 2rem;
        border-radius: 0.75rem;
        position: relative;
        overflow: hidden;
    }

    .dashboard-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='7' cy='7' r='1'/%3E%3Ccircle cx='27' cy='7' r='1'/%3E%3Ccircle cx='47' cy='7' r='1'/%3E%3Ccircle cx='7' cy='27' r='1'/%3E%3Ccircle cx='27' cy='27' r='1'/%3E%3Ccircle cx='47' cy='27' r='1'/%3E%3Ccircle cx='7' cy='47' r='1'/%3E%3Ccircle cx='27' cy='47' r='1'/%3E%3Ccircle cx='47' cy='47' r='1'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        z-index: 1;
    }

    .header-content {
        position: relative;
        z-index: 2;
    }

    .restaurant-name {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        letter-spacing: -0.025em;
    }

    .restaurant-tagline {
        font-size: 1.125rem;
        opacity: 0.9;
        font-weight: 500;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 0.75rem;
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
        transition: all 0.2s ease-in-out;
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-1px);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--primary-600), var(--blue-600));
        border-radius: 0.75rem 0.75rem 0 0;
    }

    .stat-header {
        display: flex;
        justify-content: between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .stat-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .stat-icon.revenue {
        background-color: var(--green-500);
        color: white;
    }

    .stat-icon.orders {
        background-color: var(--blue-500);
        color: white;
    }

    .stat-icon.inventory {
        background-color: var(--amber-500);
        color: white;
    }

    .stat-icon.analytics {
        background-color: var(--primary-600);
        color: white;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 0.25rem;
        letter-spacing: -0.025em;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--gray-600);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .stat-change {
        font-size: 0.75rem;
        font-weight: 600;
        margin-top: 0.5rem;
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .stat-change.positive {
        background-color: rgba(16, 185, 129, 0.1);
        color: var(--green-600);
    }

    .stat-change.negative {
        background-color: rgba(239, 68, 68, 0.1);
        color: var(--red-600);
    }

    /* Dashboard Grid */
    .dashboard-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .dashboard-card {
        background: white;
        border-radius: 0.75rem;
        box-shadow: var(--shadow);
        border: 1px solid var(--gray-200);
        overflow: hidden;
    }

    .card-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--gray-900);
    }

    .card-content {
        padding: 1.5rem;
    }

    /* Recent Activity */
    .activity-list {
        space-y: 1rem;
    }

    .activity-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        border-radius: 0.5rem;
        background-color: var(--gray-50);
        transition: background-color 0.2s;
    }

    .activity-item:hover {
        background-color: var(--gray-100);
    }

    .activity-icon {
        width: 2rem;
        height: 2rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 600;
        color: white;
        flex-shrink: 0;
    }

    .activity-content {
        flex: 1;
        min-width: 0;
    }

    .activity-title {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--gray-900);
        margin-bottom: 0.125rem;
    }

    .activity-time {
        font-size: 0.75rem;
        color: var(--gray-500);
    }

    /* Quick Actions */
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .action-button {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        padding: 1rem;
        background: var(--gray-50);
        border: 2px solid var(--gray-200);
        border-radius: 0.75rem;
        text-decoration: none;
        transition: all 0.2s ease-in-out;
        color: var(--gray-700);
    }

    .action-button:hover {
        background: white;
        border-color: var(--primary-600);
        color: var(--primary-600);
        transform: translateY(-1px);
        box-shadow: var(--shadow);
    }

    .action-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.5rem;
        background: var(--primary-600);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }

    .action-button:hover .action-icon {
        background: var(--primary-700);
    }

    .action-label {
        font-size: 0.875rem;
        font-weight: 500;
        text-align: center;
    }

    /* Performance Chart Placeholder */
    .chart-placeholder {
        background: linear-gradient(135deg, var(--gray-100) 0%, var(--gray-200) 100%);
        height: 12rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--gray-500);
        font-size: 0.875rem;
        position: relative;
        overflow: hidden;
    }

    .chart-placeholder::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg viewBox='0 0 100 20' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M10,10 Q20,5 30,10 T50,10 T70,10 T90,10' stroke='%234f46e5' stroke-width='2' fill='none' opacity='0.3'/%3E%3C/svg%3E");
        background-size: cover;
    }

    .chart-placeholder span {
        position: relative;
        z-index: 2;
        background: white;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        box-shadow: var(--shadow-sm);
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
        
        .stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        }
        
        .quick-actions {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .dashboard-header {
            padding: 1.5rem;
        }
        
        .restaurant-name {
            font-size: 1.75rem;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    
    <!-- Restaurant Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <h1 class="restaurant-name">{{ $tenant->name ?? 'GHORKA RESTAURANT LLC' }}</h1>
            <p class="restaurant-tagline">Advanced Analytics & Management Dashboard</p>
        </div>
    </div>

    <!-- Key Performance Indicators -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon revenue">AED</div>
            <div class="stat-value">{{ number_format($stats['monthly_revenue'] ?? 94230.50, 2) }}</div>
            <div class="stat-label">Monthly Revenue</div>
            <div class="stat-change positive">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                +12.5% vs last month
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon orders">#</div>
            <div class="stat-value">{{ number_format($stats['recent_imports'] ?? 4152) }}</div>
            <div class="stat-label">Total Orders</div>
            <div class="stat-change positive">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                +8.3% vs last month
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon inventory">📦</div>
            <div class="stat-value">{{ $stats['inventory_items'] ?? 100 }}</div>
            <div class="stat-label">Active Items</div>
            <div class="stat-change positive">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                5 new items added
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon analytics">🎯</div>
            <div class="stat-value">{{ number_format($stats['data_quality'] ?? 96.8, 1) }}%</div>
            <div class="stat-label">Data Quality Score</div>
            <div class="stat-change positive">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                Excellent quality
            </div>
        </div>
    </div>

    <!-- Main Dashboard Grid -->
    <div class="dashboard-grid">
        <!-- Performance Chart -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">Revenue Performance</h3>
                <div class="flex space-x-2">
                    <button class="px-3 py-1 text-xs font-medium text-gray-600 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">7D</button>
                    <button class="px-3 py-1 text-xs font-medium text-white bg-indigo-600 rounded-full">30D</button>
                    <button class="px-3 py-1 text-xs font-medium text-gray-600 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">90D</button>
                </div>
            </div>
            <div class="card-content">
                @if(isset($revenueData) && !empty($revenueData))
                    <div id="revenueChart" style="height: 12rem;"></div>
                @else
                    <div class="chart-placeholder">
                        <span>Connect your data source to view revenue analytics</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
            </div>
            <div class="card-content">
                <div class="quick-actions">
                    <a href="{{ route('imports.create') }}" class="action-button">
                        <div class="action-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                        </div>
                        <span class="action-label">Import Data</span>
                    </a>

                    <a href="#" class="action-button">
                        <div class="action-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <span class="action-label">Analytics</span>
                    </a>

                    <a href="#" class="action-button">
                        <div class="action-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <span class="action-label">Inventory</span>
                    </a>

                    <a href="#" class="action-button">
                        <div class="action-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                            </svg>
                        </div>
                        <span class="action-label">Settings</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Top Items -->
    <div class="dashboard-grid">
        <!-- Recent Imports -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">Recent Import Activity</h3>
                <a href="{{ route('imports.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">View All</a>
            </div>
            <div class="card-content">
                <div class="space-y-3">
                    @forelse($recentImports as $import)
                    <div class="activity-item">
                        <div class="activity-icon" style="background-color: 
                            @if($import->status === 'completed') var(--green-500) 
                            @elseif($import->status === 'failed') var(--red-500) 
                            @else var(--amber-500) @endif">
                            @if($import->status === 'completed') ✓
                            @elseif($import->status === 'failed') ✗
                            @else ⏳ @endif
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">{{ $import->filename ?? 'Data Import' }}</div>
                            <div class="activity-time">{{ $import->created_at ? $import->created_at->diffForHumans() : 'Recently' }} • {{ $import->records ?? 0 }} records</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-sm">No import activity yet</p>
                        <a href="{{ route('imports.create') }}" class="mt-2 text-indigo-600 hover:text-indigo-700 text-sm font-medium">Import your first dataset</a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Top Performing Items -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">Top Menu Items</h3>
                <span class="text-sm text-gray-500">This month</span>
            </div>
            <div class="card-content">
                <div class="space-y-3">
                    @forelse($topMenuItems ?? [] as $index => $item)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-{{ $index === 0 ? 'green' : ($index === 1 ? 'blue' : 'amber') }}-100 rounded-lg flex items-center justify-center">
                                <span class="text-{{ $index === 0 ? 'green' : ($index === 1 ? 'blue' : 'amber') }}-600 font-semibold text-sm">{{ $index + 1 }}</span>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $item->name ?? 'Menu Item' }}</div>
                                <div class="text-sm text-gray-500">{{ $item->orders ?? 0 }} orders</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-semibold text-gray-900">AED {{ number_format($item->revenue ?? 0, 0) }}</div>
                            <div class="text-sm text-green-600">{{ $item->growth > 0 ? '+' : '' }}{{ $item->growth ?? 0 }}%</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="text-sm">No menu performance data available</p>
                        <a href="{{ route('imports.create') }}" class="mt-2 text-indigo-600 hover:text-indigo-700 text-sm font-medium">Import your menu data</a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- AI Insights Banner -->
    <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold">AI-Powered Insights</h3>
                    <p class="text-purple-100">Get intelligent recommendations for loss prevention and profit optimization</p>
                </div>
            </div>
            <button class="bg-white text-purple-600 px-4 py-2 rounded-lg font-medium hover:bg-purple-50 transition-colors">
                Explore AI Features
            </button>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// Dashboard interactivity
document.addEventListener('DOMContentLoaded', function() {
    // Animate stats on load
    const statValues = document.querySelectorAll('.stat-value');
    statValues.forEach(stat => {
        const finalValue = stat.textContent;
        stat.textContent = '0';
        
        setTimeout(() => {
            stat.textContent = finalValue;
            stat.style.transition = 'all 0.8s ease-out';
        }, 300);
    });
    
    // Add hover effects to action buttons
    const actionButtons = document.querySelectorAll('.action-button');
    actionButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px) scale(1.02)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
});
</script>
@endpush