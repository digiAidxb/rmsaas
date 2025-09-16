<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <Link :href="route('imports.index')" class="text-blue-600 hover:text-blue-800 flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Imports
                </Link>

                <h1 class="text-3xl font-bold text-gray-900">Import Details</h1>
                <p class="text-gray-600 mt-2">{{ importJob.original_filename }}</p>
            </div>

            <!-- Status Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Import Status</h2>
                    <div class="flex items-center gap-2">
                        <div :class="statusClasses(importJob.status)" class="px-3 py-1 rounded-full text-sm font-medium">
                            {{ statusText(importJob.status) }}
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mb-4">
                    <div class="flex justify-between text-sm text-gray-600 mb-2">
                        <span>Progress</span>
                        <span>{{ importJob.progress_percentage }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div
                            :class="progressBarClasses(importJob.status)"
                            class="h-2 rounded-full transition-all duration-300"
                            :style="{ width: importJob.progress_percentage + '%' }"
                        ></div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ importJob.total_records || 0 }}</div>
                        <div class="text-sm text-gray-500">Total Records</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ importJob.successful_imports || 0 }}</div>
                        <div class="text-sm text-gray-500">Successful</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-600">{{ importJob.failed_imports || 0 }}</div>
                        <div class="text-sm text-gray-500">Failed</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-yellow-600">{{ importJob.skipped_records || 0 }}</div>
                        <div class="text-sm text-gray-500">Skipped</div>
                    </div>
                </div>
            </div>

            <!-- Import Details -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- File Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">File Information</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Filename</dt>
                            <dd class="text-sm text-gray-900">{{ importJob.original_filename }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">File Size</dt>
                            <dd class="text-sm text-gray-900">{{ formatFileSize(importJob.file_size_bytes) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Type</dt>
                            <dd class="text-sm text-gray-900">{{ importJob.file_mime_type }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Import Type</dt>
                            <dd class="text-sm text-gray-900 capitalize">{{ importJob.import_type }}</dd>
                        </div>
                        <div v-if="importJob.pos_system">
                            <dt class="text-sm font-medium text-gray-500">POS System</dt>
                            <dd class="text-sm text-gray-900 capitalize">{{ importJob.pos_system }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Processing Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Processing Information</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Started At</dt>
                            <dd class="text-sm text-gray-900">{{ formatDate(importJob.started_at) || 'Not started' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Completed At</dt>
                            <dd class="text-sm text-gray-900">{{ formatDate(importJob.completed_at) || 'Not completed' }}</dd>
                        </div>
                        <div v-if="importJob.processing_time_seconds">
                            <dt class="text-sm font-medium text-gray-500">Processing Time</dt>
                            <dd class="text-sm text-gray-900">{{ formatDuration(importJob.processing_time_seconds) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created At</dt>
                            <dd class="text-sm text-gray-900">{{ formatDate(importJob.created_at) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Error Messages -->
            <div v-if="importJob.error_message" class="mt-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-red-800 mb-2">Error Details</h3>
                <p class="text-red-700">{{ importJob.error_message }}</p>
            </div>

            <!-- Import Summary -->
            <div v-if="importJob.import_summary" class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-blue-800 mb-2">Import Summary</h3>
                <p class="text-blue-700">{{ importJob.import_summary }}</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    importJob: Object
})

function statusClasses(status) {
    const classes = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'parsing': 'bg-blue-100 text-blue-800',
        'mapping': 'bg-blue-100 text-blue-800',
        'validating': 'bg-blue-100 text-blue-800',
        'importing': 'bg-blue-100 text-blue-800',
        'completed': 'bg-green-100 text-green-800',
        'failed': 'bg-red-100 text-red-800',
        'cancelled': 'bg-gray-100 text-gray-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

function statusText(status) {
    const texts = {
        'pending': 'Pending',
        'parsing': 'Parsing',
        'mapping': 'Mapping',
        'validating': 'Validating',
        'importing': 'Importing',
        'completed': 'Completed',
        'failed': 'Failed',
        'cancelled': 'Cancelled'
    }
    return texts[status] || status
}

function progressBarClasses(status) {
    if (status === 'completed') return 'bg-green-500'
    if (status === 'failed') return 'bg-red-500'
    if (status === 'cancelled') return 'bg-gray-500'
    return 'bg-blue-500'
}

function formatFileSize(bytes) {
    if (!bytes) return 'Unknown'
    const sizes = ['Bytes', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(1024))
    return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i]
}

function formatDate(dateString) {
    if (!dateString) return null
    return new Date(dateString).toLocaleString()
}

function formatDuration(seconds) {
    if (!seconds) return 'Unknown'
    const minutes = Math.floor(seconds / 60)
    const remainingSeconds = seconds % 60
    if (minutes > 0) {
        return `${minutes}m ${remainingSeconds}s`
    }
    return `${remainingSeconds}s`
}
</script>