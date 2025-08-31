<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            <div class="flex flex-col items-center">
                <!-- Logo/Icon -->
                <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <!-- Restaurant Name -->
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2 text-center">
                    {{ tenant.name }}
                </h1>

                <!-- Status Message -->
                <div class="text-center mb-6">
                    <div class="inline-flex items-center px-4 py-2 bg-amber-100 dark:bg-amber-900 text-amber-800 dark:text-amber-200 text-sm font-medium rounded-full">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ getStatusMessage() }}
                    </div>
                </div>

                <!-- Information -->
                <div class="bg-blue-50 dark:bg-blue-900 border-l-4 border-blue-400 p-4 w-full mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                Your restaurant account is currently under review. You'll receive an email notification once your account has been approved and you can start using the system.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Support Information -->
                <div class="text-center text-sm text-gray-600 dark:text-gray-400">
                    <p class="mb-2">Need help? Contact our support team:</p>
                    <a href="mailto:support@rmsaas.com" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">
                        support@rmsaas.com
                    </a>
                </div>

                <!-- Domain Info -->
                <div class="mt-6 text-xs text-gray-500 dark:text-gray-400 text-center">
                    Domain: {{ tenant.domain }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineProps } from 'vue'

const props = defineProps({
    tenant: {
        type: Object,
        required: true
    }
})

function getStatusMessage() {
    switch (props.tenant.status) {
        case 'pending':
            return 'Account Under Review'
        case 'rejected':
            return 'Account Rejected'
        case 'suspended':
            return 'Account Suspended'
        default:
            return 'Account Not Active'
    }
}
</script>