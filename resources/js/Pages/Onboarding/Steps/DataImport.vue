<template>
    <OnboardingLayout>
    <div class="bg-gradient-to-br from-blue-50 to-indigo-100 py-8 px-4 sm:px-6 lg:px-8 rounded-lg">
        <div class="max-w-4xl mx-auto">
            <!-- Progress Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <Link 
                        :href="route('onboarding.index')" 
                        class="text-blue-600 hover:text-blue-500 font-medium flex items-center"
                    >
                        ← Back to Overview
                    </Link>
                    <div class="text-sm text-gray-600">
                        Step {{ stepIndex + 1 }} of {{ totalSteps }}
                    </div>
                </div>
                
                <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                    <div 
                        class="bg-gradient-to-r from-blue-600 to-indigo-600 h-2 rounded-full transition-all duration-300"
                        :style="{ width: `${((stepIndex + 1) / totalSteps) * 100}%` }"
                    ></div>
                </div>
            </div>

            <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6 text-white">
                    <h1 class="text-3xl font-bold mb-2">📊 Import Your Data</h1>
                    <p class="text-blue-100 text-lg">Bring in your existing restaurant data to get started quickly</p>
                </div>

                <!-- Content -->
                <div class="p-8">
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Data Import Strategy</h2>
                        <p class="text-gray-600 mb-6">
                            For optimal restaurant management, we recommend importing data in the correct sequence. This ensures proper relationships between your menu, inventory, and operations.
                        </p>

                        <!-- Import Sequence -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <h3 class="font-semibold text-blue-900 mb-3">📋 Recommended Import Sequence</h3>
                            <div class="space-y-2 text-sm text-blue-800">
                                <div class="flex items-center space-x-2">
                                    <span class="w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-semibold">1</span>
                                    <span><strong>Menu & Categories:</strong> Import your menu items with multi-level categories first</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-semibold">2</span>
                                    <span><strong>Inventory Items:</strong> Import ingredients and supplies for recipe management</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-semibold">3</span>
                                    <span><strong>Recipes:</strong> Link menu items to inventory ingredients</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-semibold">4</span>
                                    <span><strong>Sales Data:</strong> Historical sales for analytics and forecasting</span>
                                </div>
                            </div>
                        </div>

                        <!-- Import Options -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                            <!-- Import from POS -->
                            <div 
                                :class="{
                                    'border-blue-500 bg-blue-50': form.data.import_method === 'pos_import',
                                    'border-gray-300': form.data.import_method !== 'pos_import'
                                }"
                                class="border-2 rounded-lg p-6 cursor-pointer transition-all duration-200 hover:border-blue-400"
                                @click="form.data.import_method = 'pos_import'"
                            >
                                <div class="flex items-start space-x-4">
                                    <input
                                        v-model="form.data.import_method"
                                        type="radio"
                                        value="pos_import"
                                        class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500"
                                    />
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-3">
                                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900">Import from POS System</h3>
                                        </div>
                                        <p class="text-gray-600 text-sm mb-4">
                                            Upload your existing restaurant data following our recommended sequence for optimal system setup.
                                        </p>
                                        <div class="space-y-2 text-sm">
                                            <div class="flex items-center space-x-2 text-green-600">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span>Keep your existing menu and pricing</span>
                                            </div>
                                            <div class="flex items-center space-x-2 text-green-600">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span>Import historical sales data</span>
                                            </div>
                                            <div class="flex items-center space-x-2 text-green-600">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span>Immediate analytics insights</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Start with Sample Data -->
                            <div 
                                :class="{
                                    'border-blue-500 bg-blue-50': form.data.import_method === 'sample_data',
                                    'border-gray-300': form.data.import_method !== 'sample_data'
                                }"
                                class="border-2 rounded-lg p-6 cursor-pointer transition-all duration-200 hover:border-blue-400"
                                @click="form.data.import_method = 'sample_data'"
                            >
                                <div class="flex items-start space-x-4">
                                    <input
                                        v-model="form.data.import_method"
                                        type="radio"
                                        value="sample_data"
                                        class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500"
                                    />
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-3">
                                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900">Start with Demo Data</h3>
                                        </div>
                                        <p class="text-gray-600 text-sm mb-4">
                                            Explore the system with realistic restaurant data including multi-level categories and comprehensive menu structure.
                                        </p>
                                        <div class="space-y-2 text-sm">
                                            <div class="flex items-center space-x-2 text-green-600">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span>Quick setup and testing</span>
                                            </div>
                                            <div class="flex items-center space-x-2 text-green-600">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span>Realistic restaurant examples</span>
                                            </div>
                                            <div class="flex items-center space-x-2 text-green-600">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span>Easy to customize later</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- POS System Selection (shown when importing) -->
                        <div v-if="form.data.import_method === 'pos_import'" class="bg-gray-50 rounded-lg p-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Select Your POS System</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                <div 
                                    v-for="pos in posOptions" 
                                    :key="pos.value"
                                    :class="{
                                        'border-blue-500 bg-blue-50': form.data.selected_pos === pos.value,
                                        'border-gray-300': form.data.selected_pos !== pos.value
                                    }"
                                    class="border-2 rounded-lg p-3 cursor-pointer hover:border-blue-400 transition-all duration-200 text-center"
                                    @click="form.data.selected_pos = pos.value"
                                >
                                    <div class="text-2xl mb-2">{{ pos.icon }}</div>
                                    <div class="text-sm font-medium text-gray-900">{{ pos.name }}</div>
                                </div>
                            </div>
                            
                            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                <h4 class="font-medium text-blue-900 mb-2">📋 What You'll Need:</h4>
                                <ul class="text-sm text-blue-800 space-y-1">
                                    <li>• Menu items export (CSV or Excel)</li>
                                    <li>• Sales data for the last 30-90 days</li>
                                    <li>• Inventory/ingredient data (if available)</li>
                                    <li>• Staff information (optional)</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Sample Data Preview -->
                        <div v-if="form.data.import_method === 'sample_data'" class="bg-green-50 rounded-lg p-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Sample Data Includes:</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <h4 class="font-medium text-green-900 mb-2">Menu Items</h4>
                                    <ul class="text-sm text-green-800 space-y-1">
                                        <li>• 25+ sample dishes across categories</li>
                                        <li>• Appetizers, mains, desserts, beverages</li>
                                        <li>• Realistic pricing for UAE market</li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 class="font-medium text-green-900 mb-2">Analytics Data</h4>
                                    <ul class="text-sm text-green-800 space-y-1">
                                        <li>• 3 months of simulated sales</li>
                                        <li>• Customer patterns and trends</li>
                                        <li>• Inventory usage examples</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Next Steps Preview -->
                        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-indigo-900 mb-2">What Happens Next?</h3>
                            <div class="text-sm text-indigo-800">
                                <p v-if="form.data.import_method === 'pos_import'">
                                    We'll guide you through uploading your POS data files and help you map your menu items and categories to our system.
                                </p>
                                <p v-if="form.data.import_method === 'sample_data'">
                                    We'll populate your system with realistic sample data that you can customize and replace with your actual menu items.
                                </p>
                                <p class="mt-2">
                                    Don't worry - you can always change or import additional data later from your dashboard.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                        <button
                            @click="skipOnboarding"
                            type="button"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition duration-200"
                        >
                            Skip Setup
                        </button>
                        
                        <button
                            @click="completeStep"
                            :disabled="form.processing || !form.data.import_method"
                            class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-lg transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="form.processing" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Processing...
                            </span>
                            <span v-else>
                                Continue to Menu Setup →
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </OnboardingLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import OnboardingLayout from '@/Layouts/OnboardingLayout.vue'

const props = defineProps({
    tenant: Object,
    progress: Object,
    currentStep: Object,
    stepName: String,
    stepIndex: Number,
    totalSteps: Number
})

const posOptions = [
    { name: 'Square', value: 'square', icon: '🟦' },
    { name: 'Toast', value: 'toast', icon: '🍞' },
    { name: 'Clover', value: 'clover', icon: '🍀' },
    { name: 'Lightspeed', value: 'lightspeed', icon: '⚡' },
    { name: 'Revel', value: 'revel', icon: '🎯' },
    { name: 'TouchBistro', value: 'touchbistro', icon: '📱' },
    { name: 'Shopify POS', value: 'shopify', icon: '🛍️' },
    { name: 'Other/Custom', value: 'other', icon: '⚙️' }
]

const form = useForm({
    data: {
        import_method: '',
        selected_pos: '',
        notes: ''
    }
})

function completeStep() {
    form.post(route('onboarding.complete', props.stepName))
}

function skipOnboarding() {
    if (confirm('Are you sure you want to skip the setup? You can always configure these settings later from the dashboard.')) {
        form.post(route('onboarding.skip'))
    }
}
</script>