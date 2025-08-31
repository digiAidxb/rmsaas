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
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6 text-white">
                    <h1 class="text-3xl font-bold mb-2">👥 Staff Management</h1>
                    <p class="text-blue-100 text-lg">Add team members and set permissions</p>
                </div>

                <!-- Content -->
                <div class="p-8">
                    <div class="text-center py-12">
                        <div class="text-6xl mb-4">👥</div>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">Team Management Ready!</h2>
                        <p class="text-gray-600 mb-8">
                            Your staff management system is configured. You can add team members, 
                            assign roles, and manage permissions from your dashboard.
                        </p>
                        
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-6 max-w-md mx-auto">
                            <h3 class="font-semibold text-purple-900 mb-2">Staff features include:</h3>
                            <ul class="text-sm text-purple-800 space-y-1 text-left">
                                <li>• Employee profiles</li>
                                <li>• Role-based permissions</li>
                                <li>• Shift scheduling</li>
                                <li>• Performance tracking</li>
                            </ul>
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
                                Continue to Dashboard Tour →
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
        staff_configured: true
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