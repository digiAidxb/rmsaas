<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    tenant: Object,
    onboarding_progress: Object,
    stats: Object,
    recentImports: Array,
    quickActions: Array,
    aiPreview: Object,
})

const form = useForm({})

const isOnboardingComplete = computed(() => {
    return props.tenant?.onboarding_completed_at !== null
})

const hasTestData = computed(() => {
    return props.tenant?.settings?.has_test_data || false
})

const isDemoMode = computed(() => {
    return hasTestData.value && !isOnboardingComplete.value
})

function seedTestData() {
    if (confirm('Load demo data to explore RMSaaS features? This includes sample menu items, inventory, and analytics data.')) {
        form.post('/seed-demo-data')
    }
}

function startOnboarding() {
    window.location.href = route('onboarding.index')
}

// Analytics data from controller
const analyticsData = computed(() => ({
    dailySales: 2847.50, // This could be from stats when available
    monthlyRevenue: props.stats?.monthly_revenue || 0,
    inventoryValue: 12450.75,
    lowStockItems: 8,
    topSellingItems: [
        { name: 'Grilled Ribeye Steak', sales: 45, revenue: 1752.75 },
        { name: 'Lobster Ravioli', sales: 32, revenue: 1054.40 },
        { name: 'Pan-Seared Salmon', sales: 28, revenue: 838.60 },
    ]
}))
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ tenant.name }} Dashboard
                </h2>
                <div class="text-sm text-gray-600">
                    <span v-if="isDemoMode" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        🚀 Demo Mode
                    </span>
                    <span v-else-if="!isOnboardingComplete" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        ⚙️ Setup Incomplete
                    </span>
                    <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        ✅ Active
                    </span>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                
                <!-- Onboarding/Setup Actions (if not complete) -->
                <div v-if="!isOnboardingComplete" class="bg-blue-50 border border-blue-200 rounded p-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-100 rounded flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Get Started with Your Restaurant</h3>
                            <p class="text-gray-600 mb-4">
                                <span v-if="hasTestData">
                                    You're using demo data to explore features. Complete the setup process to add your real restaurant information, or continue exploring with the sample data.
                                </span>
                                <span v-else>
                                    Welcome to RMSaaS! Set up your restaurant or explore our features with demo data first.
                                </span>
                            </p>
                            
                            <div class="flex flex-wrap gap-3">
                                <button
                                    @click="startOnboarding"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded transition duration-200"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    {{ hasTestData ? 'Complete Setup' : 'Start Setup Process' }}
                                </button>
                                
                                <button
                                    v-if="!hasTestData"
                                    @click="seedTestData"
                                    class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded transition duration-200"
                                >
                                    🚀 Explore with Demo Data
                                </button>
                                
                                <Link 
                                    v-if="hasTestData"
                                    href="/menu-management" 
                                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded transition duration-200"
                                >
                                    📋 View Sample Menu
                                </Link>
                                
                                <Link 
                                    v-if="hasTestData"
                                    href="/inventory" 
                                    class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded transition duration-200"
                                >
                                    📦 Check Inventory
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Real Analytics Dashboard (if has test data or is complete) -->
                <div v-if="hasTestData || isOnboardingComplete">
                    <!-- Key Metrics -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-white overflow-hidden border border-gray-200 rounded">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-green-100 rounded flex items-center justify-center">
                                            💰
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-500">Monthly Revenue</div>
                                        <div class="text-2xl font-bold text-gray-900">AED {{ (stats?.monthly_revenue || 0).toLocaleString() }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden border border-gray-200 rounded">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-blue-100 rounded flex items-center justify-center">
                                            📋
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-500">Menu Items</div>
                                        <div class="text-2xl font-bold text-gray-900">{{ stats?.total_menu_items || 0 }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden border border-gray-200 rounded">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-orange-100 rounded flex items-center justify-center">
                                            📦
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-500">Inventory Items</div>
                                        <div class="text-2xl font-bold text-gray-900">{{ stats?.inventory_items || 0 }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden border border-gray-200 rounded">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-purple-100 rounded flex items-center justify-center">
                                            📊
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-500">Data Quality</div>
                                        <div class="text-2xl font-bold text-gray-900">{{ (stats?.data_quality || 0).toFixed(1) }}%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts and Analytics -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Top Selling Items -->
                        <div class="bg-white overflow-hidden border border-gray-200 rounded">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">🏆 Top Selling Items</h3>
                                <div class="space-y-4">
                                    <div v-for="(item, index) in analyticsData.topSellingItems" :key="index" class="flex items-center justify-between">
                                        <div>
                                            <div class="font-medium text-gray-900">{{ item.name }}</div>
                                            <div class="text-sm text-gray-500">{{ item.sales }} orders</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-medium text-gray-900">${{ item.revenue.toFixed(2) }}</div>
                                            <div class="w-20 bg-gray-200 rounded-full h-2 mt-1">
                                                <div 
                                                    class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                                    :style="{ width: `${(item.sales / 50) * 100}%` }"
                                                ></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- AI Insights from Controller -->
                        <div class="bg-white overflow-hidden border border-gray-200 rounded">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">🤖 AI-Powered Insights</h3>
                                <div v-if="aiPreview && aiPreview.critical_actions" class="space-y-4">
                                    <div v-for="(action, index) in aiPreview.critical_actions" :key="index" class="p-4 bg-blue-50 rounded border border-blue-100">
                                        <div class="flex items-start space-x-3">
                                            <div class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                                            <div>
                                                <h4 class="font-medium text-gray-900">AI Recommendation {{ index + 1 }}</h4>
                                                <p class="text-sm text-gray-600 mt-1">{{ action }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="aiPreview.potential_monthly_impact" class="p-4 bg-green-50 rounded border border-green-100">
                                        <div class="flex items-start space-x-3">
                                            <div class="flex-shrink-0 w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                                            <div>
                                                <h4 class="font-medium text-gray-900">Potential Monthly Impact</h4>
                                                <p class="text-sm text-gray-600 mt-1">AI recommendations could generate AED {{ aiPreview.potential_monthly_impact.toLocaleString() }} additional monthly revenue.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="text-center py-8 text-gray-500">
                                    <div class="text-4xl mb-2">🤖</div>
                                    <p class="text-sm">AI insights will appear here once you import restaurant data</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions from Controller -->
                <div v-if="isOnboardingComplete || hasTestData" class="bg-white overflow-hidden border border-gray-200 rounded">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">⚡ Quick Actions</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <Link
                                v-for="action in quickActions"
                                :key="action.title"
                                :href="route(action.route)"
                                class="flex items-center p-4 border border-gray-200 rounded hover:bg-gray-50 transition duration-200"
                            >
                                <div class="w-8 h-8 bg-blue-100 rounded flex items-center justify-center mr-3">
                                    <span v-if="action.title === 'Import Data'">📤</span>
                                    <span v-else-if="action.title === 'Loss Management'">📉</span>
                                    <span v-else-if="action.title === 'Profit Optimization'">🧠</span>
                                    <span v-else-if="action.title === 'AI Insights'">💡</span>
                                    <span v-else>📊</span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ action.title }}</div>
                                    <div class="text-sm text-gray-500">{{ action.description }}</div>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Recent Imports -->
                <div v-if="recentImports && recentImports.length > 0" class="bg-white overflow-hidden border border-gray-200 rounded">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">📋 Recent Import Activity</h3>
                        <div class="space-y-3">
                            <div v-for="import_item in recentImports" :key="import_item.id" class="flex items-center p-3 bg-gray-50 rounded border">
                                <div class="w-8 h-8 rounded flex items-center justify-center mr-3"
                                     :class="{
                                         'bg-green-100 text-green-600': import_item.status === 'completed',
                                         'bg-red-100 text-red-600': import_item.status === 'failed',
                                         'bg-yellow-100 text-yellow-600': import_item.status === 'processing'
                                     }">
                                    <span v-if="import_item.status === 'completed'">✓</span>
                                    <span v-else-if="import_item.status === 'failed'">✗</span>
                                    <span v-else>⏳</span>
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">{{ import_item.filename || 'Data Import' }}</div>
                                    <div class="text-sm text-gray-500">{{ import_item.records || 0 }} records • {{ import_item.created_at ? new Date(import_item.created_at).toLocaleDateString() : 'Recently' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Demo Data Notice -->
                <div v-if="isDemoMode" class="bg-purple-50 border border-purple-200 rounded p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-purple-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-purple-700">
                                <strong>Demo Mode Active:</strong> You're viewing sample data to explore RMSaaS features. 
                                <button @click="startOnboarding" class="underline font-medium hover:text-purple-800">
                                    Complete setup
                                </button> to add your real restaurant data.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
