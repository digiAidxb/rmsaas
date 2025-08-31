<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-8">
            <div class="text-center">
                <h2 class="mt-6 text-4xl font-extrabold text-gray-900">
                    Start Your Restaurant Management Journey
                </h2>
                <p class="mt-2 text-lg text-gray-600">
                    Create your restaurant tenant account and unlock powerful analytics
                </p>
            </div>

            <div class="bg-white shadow-2xl rounded-lg p-8 space-y-6">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Robot Check -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center space-x-3">
                            <input
                                id="captcha_verified"
                                v-model="form.captcha_verified"
                                type="checkbox"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                required
                            />
                            <label for="captcha_verified" class="text-sm font-medium text-gray-700">
                                I'm not a robot ✓
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            This simple verification helps us prevent automated registrations
                        </p>
                    </div>

                    <!-- Restaurant Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="restaurant_name" class="block text-sm font-medium text-gray-700">
                                Restaurant Name *
                            </label>
                            <input
                                id="restaurant_name"
                                v-model="form.restaurant_name"
                                type="text"
                                required
                                class="mt-1 appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                                placeholder="e.g., Mario's Italian Bistro"
                            />
                            <div v-if="errors.restaurant_name" class="text-red-600 text-sm mt-1">
                                {{ errors.restaurant_name }}
                            </div>
                        </div>

                        <div>
                            <label for="business_type" class="block text-sm font-medium text-gray-700">
                                Business Type *
                            </label>
                            <select
                                id="business_type"
                                v-model="form.business_type"
                                required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            >
                                <option value="">Select Business Type</option>
                                <option value="Fine Dining">Fine Dining</option>
                                <option value="Casual Dining">Casual Dining</option>
                                <option value="Fast Food">Fast Food</option>
                                <option value="Cafe">Café</option>
                                <option value="Bar & Grill">Bar & Grill</option>
                                <option value="Pizza">Pizza Restaurant</option>
                                <option value="Asian">Asian Cuisine</option>
                                <option value="Italian">Italian Restaurant</option>
                                <option value="Mexican">Mexican Restaurant</option>
                                <option value="Other">Other</option>
                            </select>
                            <div v-if="errors.business_type" class="text-red-600 text-sm mt-1">
                                {{ errors.business_type }}
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="contact_person" class="block text-sm font-medium text-gray-700">
                                Contact Person *
                            </label>
                            <input
                                id="contact_person"
                                v-model="form.contact_person"
                                type="text"
                                required
                                class="mt-1 appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                                placeholder="Your full name"
                            />
                            <div v-if="errors.contact_person" class="text-red-600 text-sm mt-1">
                                {{ errors.contact_person }}
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                Email Address *
                            </label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                required
                                class="mt-1 appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                                placeholder="your@email.com"
                            />
                            <div v-if="errors.email" class="text-red-600 text-sm mt-1">
                                {{ errors.email }}
                            </div>
                        </div>
                    </div>

                    <!-- Password Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Password *
                            </label>
                            <input
                                id="password"
                                v-model="form.password"
                                type="password"
                                required
                                class="mt-1 appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                                placeholder="Minimum 8 characters"
                            />
                            <div v-if="errors.password" class="text-red-600 text-sm mt-1">
                                {{ errors.password }}
                            </div>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                Confirm Password *
                            </label>
                            <input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                required
                                class="mt-1 appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                                placeholder="Repeat your password"
                            />
                            <div v-if="errors.password_confirmation" class="text-red-600 text-sm mt-1">
                                {{ errors.password_confirmation }}
                            </div>
                        </div>
                    </div>

                    <!-- Location Information -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="country_id" class="block text-sm font-medium text-gray-700">
                                Country *
                            </label>
                            <select
                                id="country_id"
                                v-model="form.country_id"
                                required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            >
                                <option value="">Select Country</option>
                                <option
                                    v-for="country in countries"
                                    :key="country.id"
                                    :value="country.id"
                                >
                                    {{ country.name }} ({{ country.code }})
                                </option>
                            </select>
                            <div v-if="errors.country_id" class="text-red-600 text-sm mt-1">
                                {{ errors.country_id }}
                            </div>
                        </div>

                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700">
                                City *
                            </label>
                            <input
                                id="city"
                                v-model="form.city"
                                type="text"
                                required
                                class="mt-1 appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                                placeholder="e.g., Dubai"
                            />
                            <p class="text-xs text-gray-500 mt-1">Used for unique subdomain generation</p>
                            <div v-if="errors.city" class="text-red-600 text-sm mt-1">
                                {{ errors.city }}
                            </div>
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">
                                Phone Number
                            </label>
                            <input
                                id="phone"
                                v-model="form.phone"
                                type="tel"
                                class="mt-1 appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                                placeholder="+971551234567"
                            />
                            <div v-if="errors.phone" class="text-red-600 text-sm mt-1">
                                {{ errors.phone }}
                            </div>
                        </div>
                    </div>

                    <!-- Business Address -->
                    <div>
                        <label for="business_address" class="block text-sm font-medium text-gray-700">
                            Business Address
                        </label>
                        <textarea
                            id="business_address"
                            v-model="form.business_address"
                            rows="3"
                            class="mt-1 appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                            placeholder="Full business address (optional)"
                        ></textarea>
                        <div v-if="errors.business_address" class="text-red-600 text-sm mt-1">
                            {{ errors.business_address }}
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button
                            type="submit"
                            :disabled="form.processing || !form.captcha_verified"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200"
                        >
                            <span v-if="form.processing" class="mr-2">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                            {{ form.processing ? 'Creating Your Restaurant...' : 'Create Restaurant Tenant' }}
                        </button>
                        
                        <p class="text-xs text-center text-gray-500 mt-3">
                            By registering, you agree to our terms of service and get a 30-day free trial
                        </p>
                    </div>
                </form>

                <!-- General Error Display -->
                <div v-if="errors.error" class="bg-red-50 border border-red-200 rounded-lg p-4 mt-4">
                    <div class="text-red-800">
                        {{ errors.error }}
                    </div>
                </div>
            </div>

            <!-- Back to Home -->
            <div class="text-center">
                <Link
                    :href="route('home')"
                    class="text-blue-600 hover:text-blue-500 font-medium"
                >
                    ← Back to Home
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'

const props = defineProps({
    countries: Array
})

const page = usePage()
const errors = computed(() => page.props.errors)

const form = useForm({
    restaurant_name: '',
    contact_person: '',
    email: '',
    password: '',
    password_confirmation: '',
    country_id: 16, // Default to UAE
    city: '',
    phone: '',
    business_address: '',
    business_type: '',
    captcha_verified: false,
})

const submit = () => {
    form.post(route('tenant.register.api'), {
        preserveScroll: true,
        onSuccess: () => {
            // Success is handled by redirect in controller
        },
    })
}
</script>