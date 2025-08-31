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
                    <h1 class="text-3xl font-bold mb-2">🏪 Business Information</h1>
                    <p class="text-blue-100 text-lg">Complete your restaurant profile and settings</p>
                </div>

                <!-- Content -->
                <form @submit.prevent="completeStep" class="p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <div>
                                <label for="cuisine_type" class="block text-sm font-medium text-gray-700 mb-2">
                                    Cuisine Type *
                                </label>
                                <select
                                    id="cuisine_type"
                                    v-model="form.data.cuisine_type"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="">Select cuisine type</option>
                                    <option value="Italian">Italian</option>
                                    <option value="Asian">Asian</option>
                                    <option value="Mexican">Mexican</option>
                                    <option value="American">American</option>
                                    <option value="Mediterranean">Mediterranean</option>
                                    <option value="Indian">Indian</option>
                                    <option value="Fast Food">Fast Food</option>
                                    <option value="Fine Dining">Fine Dining</option>
                                    <option value="Cafe">Café</option>
                                    <option value="Bakery">Bakery</option>
                                    <option value="Seafood">Seafood</option>
                                    <option value="Steakhouse">Steakhouse</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div>
                                <label for="seating_capacity" class="block text-sm font-medium text-gray-700 mb-2">
                                    Seating Capacity
                                </label>
                                <input
                                    id="seating_capacity"
                                    v-model="form.data.seating_capacity"
                                    type="number"
                                    min="1"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="e.g., 50"
                                />
                            </div>

                            <div>
                                <label for="operating_hours" class="block text-sm font-medium text-gray-700 mb-2">
                                    Typical Operating Hours
                                </label>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Opening Time</label>
                                        <input
                                            v-model="form.data.opening_time"
                                            type="time"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Closing Time</label>
                                        <input
                                            v-model="form.data.closing_time"
                                            type="time"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Service Style (Select all that apply)
                                </label>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            v-model="form.data.service_styles"
                                            type="checkbox"
                                            value="dine_in"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Dine-in</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            v-model="form.data.service_styles"
                                            type="checkbox"
                                            value="takeaway"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Takeaway</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            v-model="form.data.service_styles"
                                            type="checkbox"
                                            value="delivery"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Delivery</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <div>
                                <label for="pos_system" class="block text-sm font-medium text-gray-700 mb-2">
                                    Current POS System (if any)
                                </label>
                                <select
                                    id="pos_system"
                                    v-model="form.data.pos_system"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="">Select POS system</option>
                                    <option value="Square">Square</option>
                                    <option value="Toast">Toast</option>
                                    <option value="Clover">Clover</option>
                                    <option value="Lightspeed">Lightspeed</option>
                                    <option value="Revel">Revel</option>
                                    <option value="TouchBistro">TouchBistro</option>
                                    <option value="Shopify">Shopify POS</option>
                                    <option value="Custom">Custom/Other</option>
                                    <option value="None">None (New Setup)</option>
                                </select>
                            </div>

                            <div>
                                <label for="avg_daily_covers" class="block text-sm font-medium text-gray-700 mb-2">
                                    Average Daily Customers
                                </label>
                                <input
                                    id="avg_daily_covers"
                                    v-model="form.data.avg_daily_covers"
                                    type="number"
                                    min="1"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="e.g., 100"
                                />
                            </div>

                            <div>
                                <label for="avg_ticket_size" class="block text-sm font-medium text-gray-700 mb-2">
                                    Average Ticket Size (AED)
                                </label>
                                <input
                                    id="avg_ticket_size"
                                    v-model="form.data.avg_ticket_size"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="e.g., 85.00"
                                />
                            </div>

                            <div>
                                <label for="primary_goals" class="block text-sm font-medium text-gray-700 mb-2">
                                    Primary Goals (Select all that apply)
                                </label>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            v-model="form.data.primary_goals"
                                            type="checkbox"
                                            value="increase_profits"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Increase profit margins</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            v-model="form.data.primary_goals"
                                            type="checkbox"
                                            value="reduce_waste"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Reduce food waste</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            v-model="form.data.primary_goals"
                                            type="checkbox"
                                            value="improve_efficiency"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Improve operational efficiency</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            v-model="form.data.primary_goals"
                                            type="checkbox"
                                            value="better_analytics"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Better sales analytics</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                        <button
                            @click="skipOnboarding"
                            type="button"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition duration-200"
                        >
                            Skip Setup
                        </button>
                        
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-lg transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="form.processing" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Saving...
                            </span>
                            <span v-else>
                                Continue to Data Import →
                            </span>
                        </button>
                    </div>
                </form>
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
        cuisine_type: '',
        seating_capacity: '',
        opening_time: '09:00',
        closing_time: '22:00',
        service_styles: [],
        pos_system: '',
        avg_daily_covers: '',
        avg_ticket_size: '',
        primary_goals: []
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