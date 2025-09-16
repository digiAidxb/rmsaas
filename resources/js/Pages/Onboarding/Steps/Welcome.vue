<template>
    <OnboardingLayout>
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
                        class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                        :style="{ width: `${((stepIndex + 1) / totalSteps) * 100}%` }"
                    ></div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded overflow-hidden">
                <!-- Header -->
                <div class="bg-gray-50 border-b border-gray-200 px-8 py-6">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome to RMSaaS!</h1>
                    <p class="text-gray-600 text-lg">Your complete restaurant management solution</p>
                </div>

                <!-- Content -->
                <div class="p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                                Let's Get You Started
                            </h2>
                            <p class="text-gray-600 mb-6">
                                We're excited to help you transform your restaurant operations! This quick setup will guide you through configuring your system to match your unique business needs.
                            </p>
                            
                            <div class="space-y-4">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0 w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-medium text-gray-900">5-Minute Setup</h3>
                                        <p class="text-sm text-gray-600">Quick and easy configuration process</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0 w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-medium text-gray-900">Import Your Data</h3>
                                        <p class="text-sm text-gray-600">Bring in existing menu, inventory, and sales data</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0 w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-medium text-gray-900">Start Analyzing</h3>
                                        <p class="text-sm text-gray-600">Get instant insights into your restaurant performance</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">What You'll Configure:</h3>
                            <div class="space-y-3">
                                <div class="flex items-center space-x-3">
                                    <span class="text-2xl">🏪</span>
                                    <span class="text-gray-700">Restaurant profile and business settings</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="text-2xl">📊</span>
                                    <span class="text-gray-700">Data import from your existing POS system</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="text-2xl">📋</span>
                                    <span class="text-gray-700">Menu items and categories</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="text-2xl">📦</span>
                                    <span class="text-gray-700">Inventory and ingredient management</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="text-2xl">👥</span>
                                    <span class="text-gray-700">Staff accounts and permissions</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Restaurant Info Display -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Your Restaurant Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Restaurant Name</label>
                                <div class="text-lg font-medium text-gray-900">{{ tenant.name }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Subdomain</label>
                                <div class="text-lg font-mono text-blue-600">{{ tenant.domain }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-between items-center">
                        <button
                            @click="skipOnboarding"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition duration-200"
                        >
                            Skip Setup
                        </button>
                        
                        <button
                            @click="completeStep"
                            :disabled="form.processing"
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
                                Let's Get Started! →
                            </span>
                        </button>
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

const form = useForm({
    data: {
        welcomed: true,
        started_at: new Date().toISOString()
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