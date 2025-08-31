<template>
    <div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-8">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-6">
                    <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                
                <h2 class="text-4xl font-extrabold text-gray-900">
                    🎉 Welcome to RMSaaS!
                </h2>
                <p class="mt-2 text-xl text-gray-600">
                    Your restaurant tenant "<strong>{{ tenant.name }}</strong>" has been successfully created!
                </p>
            </div>

            <div class="bg-white shadow-2xl rounded-lg overflow-hidden">
                <!-- Success Info -->
                <div class="px-8 py-6 bg-gradient-to-r from-green-500 to-blue-500 text-white">
                    <h3 class="text-2xl font-bold">Your Restaurant Management System is Ready!</h3>
                    <p class="mt-2 text-green-100">
                        You now have access to powerful analytics, inventory management, and profitability optimization tools.
                    </p>
                </div>

                <!-- Login Information -->
                <div class="p-8 space-y-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">
                            🔑 Your Login Information
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Restaurant Subdomain
                                </label>
                                <div class="flex items-center space-x-2">
                                    <code class="bg-gray-100 px-3 py-2 rounded text-sm font-mono text-gray-800 flex-1">
                                        {{ tenant.domain }}
                                    </code>
                                    <button
                                        @click="copyToClipboard(tenant.domain)"
                                        class="px-2 py-2 text-gray-500 hover:text-gray-700 transition"
                                        title="Copy domain"
                                    >
                                        📋
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Status
                                </label>
                                <div class="flex items-center space-x-2">
                                    <span
                                        :class="{
                                            'bg-yellow-100 text-yellow-800': tenant.status === 'pending',
                                            'bg-green-100 text-green-800': tenant.status === 'approved',
                                            'bg-red-100 text-red-800': tenant.status === 'suspended'
                                        }"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium capitalize"
                                    >
                                        {{ tenant.status }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Your Login URL
                            </label>
                            <div class="flex items-center space-x-2">
                                <code class="bg-gray-100 px-3 py-2 rounded text-sm font-mono text-blue-600 flex-1">
                                    {{ tenant.login_url }}
                                </code>
                                <button
                                    @click="copyToClipboard(tenant.login_url)"
                                    class="px-2 py-2 text-gray-500 hover:text-gray-700 transition"
                                    title="Copy login URL"
                                >
                                    📋
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- What's Next -->
                    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-lg p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">
                            🚀 What's Next?
                        </h4>
                        
                        <div class="space-y-3">
                            <div class="flex items-start space-x-3">
                                <span class="flex-shrink-0 w-6 h-6 bg-indigo-500 text-white rounded-full flex items-center justify-center text-sm font-medium">1</span>
                                <div>
                                    <p class="font-medium text-gray-900">Check Your Email</p>
                                    <p class="text-sm text-gray-600">We've sent you a welcome email with additional setup instructions.</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <span class="flex-shrink-0 w-6 h-6 bg-indigo-500 text-white rounded-full flex items-center justify-center text-sm font-medium">2</span>
                                <div>
                                    <p class="font-medium text-gray-900">Login to Your Tenant</p>
                                    <p class="text-sm text-gray-600">Use the login URL above to access your restaurant management system.</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <span class="flex-shrink-0 w-6 h-6 bg-indigo-500 text-white rounded-full flex items-center justify-center text-sm font-medium">3</span>
                                <div>
                                    <p class="font-medium text-gray-900">Complete Onboarding</p>
                                    <p class="text-sm text-gray-600">Import your existing data or start with our sample data for testing.</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <span class="flex-shrink-0 w-6 h-6 bg-indigo-500 text-white rounded-full flex items-center justify-center text-sm font-medium">4</span>
                                <div>
                                    <p class="font-medium text-gray-900">Start Analyzing</p>
                                    <p class="text-sm text-gray-600">Begin tracking sales, managing inventory, and optimizing profitability.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Trial Information -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">
                            🎁 Your Free Trial
                        </h4>
                        <p class="text-gray-700">
                            You have <strong>30 days</strong> of free access to all premium features. 
                            No credit card required during your trial period.
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a
                            :href="tenant.login_url"
                            target="_blank"
                            class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-3 px-6 rounded-lg text-center transition duration-200 shadow-lg"
                        >
                            🚀 Access Your Restaurant System
                        </a>
                        
                        <Link
                            :href="route('home')"
                            class="flex-1 bg-white hover:bg-gray-50 text-gray-700 font-semibold py-3 px-6 rounded-lg text-center transition duration-200 border border-gray-300"
                        >
                            ← Back to Home
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Support Information -->
            <div class="text-center text-gray-600">
                <p class="text-sm">
                    Need help getting started? 
                    <a href="mailto:support@rmsaas.com" class="text-blue-600 hover:text-blue-500 font-medium">
                        Contact our support team
                    </a>
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'

defineProps({
    tenant: Object
})

const copyToClipboard = async (text) => {
    try {
        await navigator.clipboard.writeText(text)
        // You could add a toast notification here
        alert('Copied to clipboard!')
    } catch (err) {
        console.error('Failed to copy text: ', err)
        // Fallback method
        const textArea = document.createElement('textarea')
        textArea.value = text
        document.body.appendChild(textArea)
        textArea.select()
        document.execCommand('copy')
        document.body.removeChild(textArea)
        alert('Copied to clipboard!')
    }
}
</script>