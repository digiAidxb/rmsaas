<template>
    <Head title="Import Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Import Dashboard
                </h2>
                <Link :href="route('imports.create')" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded transition duration-200">
                    📤 New Import
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">

                <!-- Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white border border-gray-200 rounded overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 rounded flex items-center justify-center">
                                        📊
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-500">Total Imports</div>
                                    <div class="text-2xl font-bold text-gray-900">{{ stats.total_imports || 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-100 rounded flex items-center justify-center">
                                        🔢
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-500">Records Processed</div>
                                    <div class="text-2xl font-bold text-gray-900">{{ stats.records_processed.toLocaleString() || 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-orange-100 rounded flex items-center justify-center">
                                        💰
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-500">Loss Prevented</div>
                                    <div class="text-2xl font-bold text-gray-900">AED {{ (stats.loss_prevented || 0).toLocaleString() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-100 rounded flex items-center justify-center">
                                        ⚡
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-500">High Quality Imports</div>
                                    <div class="text-2xl font-bold text-gray-900">{{ stats.profit_optimized || 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Imports -->
                <div class="bg-white border border-gray-200 rounded overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">📋 Recent Import Activity</h3>

                        <div v-if="recentImports && recentImports.length > 0" class="space-y-3">
                            <div v-for="import_item in recentImports" :key="import_item.id"
                                 class="flex items-center justify-between p-4 bg-gray-50 rounded border hover:bg-gray-100 transition duration-200">
                                <div class="flex items-center flex-1">
                                    <div class="w-10 h-10 rounded flex items-center justify-center mr-4"
                                         :class="{
                                             'bg-green-100 text-green-600': import_item.status === 'completed',
                                             'bg-red-100 text-red-600': import_item.status === 'failed',
                                             'bg-yellow-100 text-yellow-600': import_item.status === 'processing',
                                             'bg-blue-100 text-blue-600': import_item.status === 'pending'
                                         }">
                                        <span v-if="import_item.status === 'completed'">✅</span>
                                        <span v-else-if="import_item.status === 'failed'">❌</span>
                                        <span v-else-if="import_item.status === 'processing'">⏳</span>
                                        <span v-else>⏸️</span>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2">
                                            <h4 class="font-medium text-gray-900 truncate">{{ import_item.filename || 'Import Job' }}</h4>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full capitalize"
                                                  :class="{
                                                      'bg-blue-100 text-blue-800': import_item.import_type === 'menu',
                                                      'bg-orange-100 text-orange-800': import_item.import_type === 'inventory',
                                                      'bg-green-100 text-green-800': import_item.import_type === 'sales',
                                                      'bg-purple-100 text-purple-800': import_item.import_type === 'recipes'
                                                  }">
                                                {{ import_item.import_type }}
                                            </span>
                                        </div>

                                        <div class="flex items-center space-x-4 mt-1 text-sm text-gray-500">
                                            <span>{{ import_item.records || 0 }}/{{ import_item.total_records || 0 }} records</span>
                                            <span v-if="import_item.pos_system" class="capitalize">{{ import_item.pos_system }}</span>
                                            <span>{{ formatDate(import_item.created_at) }}</span>
                                            <span v-if="import_item.created_by">by {{ import_item.created_by }}</span>
                                        </div>

                                        <!-- Progress bar for active imports -->
                                        <div v-if="import_item.status === 'processing' && import_item.progress_percentage"
                                             class="mt-2 w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                                 :style="{ width: `${import_item.progress_percentage}%` }"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2 ml-4">
                                    <span v-if="import_item.success_rate > 0"
                                          class="text-sm font-medium"
                                          :class="{
                                              'text-green-600': import_item.success_rate >= 90,
                                              'text-yellow-600': import_item.success_rate >= 70,
                                              'text-red-600': import_item.success_rate < 70
                                          }">
                                        {{ import_item.success_rate }}%
                                    </span>

                                    <Link :href="route('imports.show', import_item.id)"
                                          class="text-blue-600 hover:text-blue-700 font-medium text-sm">
                                        View →
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-12 text-gray-500">
                            <div class="text-4xl mb-3">📂</div>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">No imports yet</h4>
                            <p class="text-sm text-gray-600 mb-4">Start by importing your restaurant data to unlock powerful analytics</p>
                            <Link :href="route('imports.create')"
                                  class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded transition duration-200">
                                📤 Import Your First Dataset
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white border border-gray-200 rounded overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">⚡ Quick Actions</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <Link :href="route('imports.create')"
                                  class="flex items-center p-4 border border-gray-200 rounded hover:bg-gray-50 transition duration-200">
                                <div class="w-8 h-8 bg-blue-100 rounded flex items-center justify-center mr-3">
                                    📤
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">Import Data</div>
                                    <div class="text-sm text-gray-500">Upload new files</div>
                                </div>
                            </Link>

                            <Link href="/dashboard"
                                  class="flex items-center p-4 border border-gray-200 rounded hover:bg-gray-50 transition duration-200">
                                <div class="w-8 h-8 bg-green-100 rounded flex items-center justify-center mr-3">
                                    📊
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">View Analytics</div>
                                    <div class="text-sm text-gray-500">See your insights</div>
                                </div>
                            </Link>

                            <button class="flex items-center p-4 border border-gray-200 rounded hover:bg-gray-50 transition duration-200 text-left">
                                <div class="w-8 h-8 bg-orange-100 rounded flex items-center justify-center mr-3">
                                    📋
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">Templates</div>
                                    <div class="text-sm text-gray-500">Download formats</div>
                                </div>
                            </button>

                            <button class="flex items-center p-4 border border-gray-200 rounded hover:bg-gray-50 transition duration-200 text-left">
                                <div class="w-8 h-8 bg-purple-100 rounded flex items-center justify-center mr-3">
                                    💡
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">Help & Tips</div>
                                    <div class="text-sm text-gray-500">Import guides</div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    stats: Object,
    recentImports: Array,
})

function formatDate(dateString) {
    if (!dateString) return 'Unknown'
    return new Date(dateString).toLocaleDateString()
}
</script>