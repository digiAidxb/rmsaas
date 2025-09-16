<template>
    <OnboardingLayout>
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-4">
                    Welcome to Your Restaurant Setup
                </h1>
                <p class="text-lg text-gray-600">
                    Let's get your restaurant management system configured in just a few steps
                </p>
            </div>

            <div class="bg-gray-50 border border-gray-200 rounded-lg overflow-hidden mb-8">
                <!-- Progress Bar -->
                <div class="bg-white border-b border-gray-200 px-6 py-4">
                    <div class="flex justify-between items-center mb-2">
                        <h2 class="text-lg font-medium text-gray-900">Setup Progress</h2>
                        <span class="text-sm text-gray-500">{{ progress?.completed_steps || 0 }}/{{ progress?.total_steps || 0 }} steps completed</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div
                            class="bg-blue-600 h-2 rounded-full transition-all duration-300 ease-out"
                            :style="{ width: `${progress?.percentage || 0}%` }"
                        ></div>
                    </div>
                </div>

                <!-- Steps Overview -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div
                            v-for="(step, stepName) in visibleSteps"
                            :key="stepName"
                            :class="{
                                'border-green-300 bg-green-50': step.completed,
                                'border-blue-300 bg-blue-50': !step.completed && isNextStep(stepName),
                                'border-gray-300 bg-white': !step.completed && !isNextStep(stepName)
                            }"
                            class="p-4 rounded border transition-all duration-200 hover:shadow-sm cursor-pointer"
                            @click="goToStep(stepName)"
                        >
                            <div class="flex items-start space-x-3">
                                <div
                                    :class="{
                                        'bg-green-600 text-white': step.completed,
                                        'bg-blue-600 text-white': !step.completed && isNextStep(stepName),
                                        'bg-gray-400 text-white': !step.completed && !isNextStep(stepName)
                                    }"
                                    class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium"
                                >
                                    <span v-if="step.completed">✓</span>
                                    <span v-else>{{ getStepNumber(stepName) }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-medium text-gray-900 truncate">{{ step.title }}</h3>
                                    <p class="text-xs text-gray-500 mt-1">{{ step.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-between items-center">
                    <div class="flex space-x-3">
                        <button
                            @click="skipOnboarding"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 transition duration-200"
                        >
                            Skip Setup
                        </button>
                        <button
                            @click="skipWithDemoData"
                            class="px-4 py-2 text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 border border-transparent rounded transition duration-200"
                        >
                            Skip & Load Demo Data
                        </button>
                    </div>

                    <button
                        v-if="nextStep"
                        @click="goToStep(nextStep)"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded transition duration-200"
                    >
                        {{ (progress?.completed_steps || 0) === 0 ? 'Get Started' : 'Continue Setup' }}
                    </button>

                    <Link
                        v-else
                        :href="route('dashboard')"
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded transition duration-200"
                    >
                        Go to Dashboard
                    </Link>
                </div>
            </div>

            <!-- Feature Highlights -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                <div class="bg-white p-6 rounded border border-gray-200">
                    <div class="text-3xl mb-3">📊</div>
                    <h3 class="font-medium text-gray-900 mb-2">Analytics & Reports</h3>
                    <p class="text-sm text-gray-600">Track sales, profits, and performance with detailed insights</p>
                </div>
                <div class="bg-white p-6 rounded border border-gray-200">
                    <div class="text-3xl mb-3">📋</div>
                    <h3 class="font-medium text-gray-900 mb-2">Inventory Management</h3>
                    <p class="text-sm text-gray-600">Monitor stock levels and optimize ingredient usage</p>
                </div>
                <div class="bg-white p-6 rounded border border-gray-200">
                    <div class="text-3xl mb-3">👥</div>
                    <h3 class="font-medium text-gray-900 mb-2">Team Management</h3>
                    <p class="text-sm text-gray-600">Manage staff, roles, and permissions efficiently</p>
                </div>
            </div>
        </div>
    </OnboardingLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import OnboardingLayout from '@/Layouts/OnboardingLayout.vue'

const props = defineProps({
    tenant: Object,
    progress: Object
})

// Filter out the 'completed' step from visible steps
const visibleSteps = computed(() => {
    if (!props.progress || !props.progress.steps) {
        return {}
    }
    const { completed, ...steps } = props.progress.steps
    return steps
})

const nextStep = computed(() => {
    const stepNames = Object.keys(visibleSteps.value)
    const nextIncompleteStep = stepNames.find(stepName => !visibleSteps.value[stepName].completed)
    return nextIncompleteStep
})

const form = useForm({})

function getStepNumber(stepName) {
    const stepNames = Object.keys(visibleSteps.value)
    return stepNames.indexOf(stepName) + 1
}

function isNextStep(stepName) {
    return nextStep.value === stepName
}

function goToStep(stepName) {
    if (visibleSteps.value[stepName].completed || isNextStep(stepName)) {
        window.location.href = route('onboarding.step', stepName)
    }
}

function skipOnboarding() {
    if (confirm('Are you sure you want to skip the setup? You can always configure these settings later from the dashboard.')) {
        form.post(route('onboarding.skip'))
    }
}

function skipWithDemoData() {
    if (confirm('Load demo data to explore RMSaaS features? This includes sample menu items, inventory, and analytics data for testing purposes.')) {
        form.post(route('onboarding.skip'), {
            data: { seed_test_data: true }
        })
    }
}
</script>