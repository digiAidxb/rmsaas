<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
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
                <div class="bg-gradient-to-r from-green-600 to-blue-600 px-8 py-6 text-white">
                    <h1 class="text-3xl font-bold mb-2">🎯 Dashboard Tour</h1>
                    <p class="text-green-100 text-lg">Learn about analytics and reporting features</p>
                </div>

                <!-- Content -->
                <div class="p-8">
                    <div class="text-center mb-8">
                        <div class="text-6xl mb-4">🎉</div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">Setup Complete!</h2>
                        <p class="text-xl text-gray-600 mb-8">
                            Congratulations! Your restaurant management system is ready to use.
                        </p>
                    </div>

                    <!-- Dashboard Features -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-lg border border-blue-200">
                            <div class="text-3xl mb-3">📊</div>
                            <h3 class="font-semibold text-gray-900 mb-2">Sales Analytics</h3>
                            <p class="text-sm text-gray-600">Track daily sales, popular items, and revenue trends</p>
                        </div>

                        <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-lg border border-green-200">
                            <div class="text-3xl mb-3">💰</div>
                            <h3 class="font-semibold text-gray-900 mb-2">Profit Analysis</h3>
                            <p class="text-sm text-gray-600">Monitor margins, costs, and profitability by item</p>
                        </div>

                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-lg border border-purple-200">
                            <div class="text-3xl mb-3">📋</div>
                            <h3 class="font-semibold text-gray-900 mb-2">Inventory Reports</h3>
                            <p class="text-sm text-gray-600">Stock levels, usage patterns, and reorder alerts</p>
                        </div>

                        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-6 rounded-lg border border-yellow-200">
                            <div class="text-3xl mb-3">👥</div>
                            <h3 class="font-semibold text-gray-900 mb-2">Staff Management</h3>
                            <p class="text-sm text-gray-600">Employee schedules, performance, and permissions</p>
                        </div>

                        <div class="bg-gradient-to-br from-red-50 to-red-100 p-6 rounded-lg border border-red-200">
                            <div class="text-3xl mb-3">🔔</div>
                            <h3 class="font-semibold text-gray-900 mb-2">Smart Alerts</h3>
                            <p class="text-sm text-gray-600">Low stock, high waste, and performance notifications</p>
                        </div>

                        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 p-6 rounded-lg border border-indigo-200">
                            <div class="text-3xl mb-3">📱</div>
                            <h3 class="font-semibold text-gray-900 mb-2">Mobile Ready</h3>
                            <p class="text-sm text-gray-600">Access your data anywhere, anytime, on any device</p>
                        </div>
                    </div>

                    <!-- Next Steps -->
                    <div class="bg-gradient-to-r from-green-50 to-blue-50 border border-green-200 rounded-lg p-6 mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">🚀 Ready to Get Started?</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Immediate Actions:</h4>
                                <ul class="text-gray-700 space-y-1">
                                    <li>• Start entering daily sales data</li>
                                    <li>• Add your team members</li>
                                    <li>• Configure low-stock alerts</li>
                                    <li>• Review sample analytics</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">This Week:</h4>
                                <ul class="text-gray-700 space-y-1">
                                    <li>• Complete your menu setup</li>
                                    <li>• Import historical data</li>
                                    <li>• Set up supplier information</li>
                                    <li>• Train your staff on the system</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Support Info -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                        <h3 class="text-lg font-semibold text-blue-900 mb-2">📞 Need Help?</h3>
                        <p class="text-blue-800 text-sm mb-4">
                            Our support team is here to help you succeed. Don't hesitate to reach out if you have questions.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <strong class="text-blue-900">Email Support:</strong><br>
                                <a href="mailto:support@rmsaas.com" class="text-blue-600 hover:text-blue-500">support@rmsaas.com</a>
                            </div>
                            <div>
                                <strong class="text-blue-900">Documentation:</strong><br>
                                <a href="#" class="text-blue-600 hover:text-blue-500">Help Center & Guides</a>
                            </div>
                        </div>
                    </div>

                    <!-- Final Action Button -->
                    <div class="text-center">
                        <button
                            @click="completeSetup"
                            :disabled="form.processing"
                            class="px-12 py-4 bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white text-lg font-bold rounded-lg transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg"
                        >
                            <span v-if="form.processing" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Finalizing Setup...
                            </span>
                            <span v-else>
                                🎉 Launch My Restaurant Dashboard!
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
    tenant: Object,
    progress: Object,
    currentStep: Object,
    stepName: String,
    stepIndex: Number,
    totalSteps: Number
})

const form = useForm({
    data: {
        tour_completed: true,
        completed_at: new Date().toISOString()
    }
})

function completeSetup() {
    form.post(route('onboarding.complete', props.stepName))
}
</script>