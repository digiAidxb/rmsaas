<template>
    <Head title="Import Data" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Import Restaurant Data
                </h2>
                <Link href="/dashboard" class="text-blue-600 hover:text-blue-500 font-medium">
                    ← Back to Dashboard
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">

                <!-- Upload Section -->
                <div class="bg-white border border-gray-200 rounded overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">📤 Upload Your Data</h3>

                        <!-- File Upload Zone -->
                        <div
                            @dragover.prevent="dragOver = true"
                            @dragleave.prevent="dragOver = false"
                            @drop.prevent="handleDrop"
                            :class="['border-2 border-dashed rounded-lg p-8 text-center transition-colors',
                                     dragOver ? 'border-blue-500 bg-blue-50' : 'border-gray-300 hover:border-gray-400']"
                        >
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                </div>

                                <h4 class="text-lg font-medium text-gray-900 mb-2">
                                    Drop your files here or click to browse
                                </h4>
                                <p class="text-sm text-gray-500 mb-4">
                                    Supports CSV, Excel (.xlsx, .xls), and POS system exports<br>
                                    Auto-detects: Menu, Inventory, Sales, and Recipe data
                                </p>

                                <input
                                    ref="fileInput"
                                    type="file"
                                    @change="handleFileSelect"
                                    accept=".csv,.xlsx,.xls"
                                    multiple
                                    class="hidden"
                                >

                                <button
                                    @click="$refs.fileInput.click()"
                                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded transition duration-200"
                                >
                                    Select Files
                                </button>
                            </div>
                        </div>

                        <!-- Selected Files -->
                        <div v-if="selectedFiles.length > 0" class="mt-6">
                            <h4 class="text-md font-medium text-gray-900 mb-3">Selected Files:</h4>
                            <div class="space-y-2">
                                <div v-for="(file, index) in selectedFiles" :key="index"
                                     class="flex items-center justify-between p-3 bg-gray-50 rounded border">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-green-100 rounded flex items-center justify-center mr-3">
                                            📄
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ file.name }}</div>
                                            <div class="text-sm text-gray-500">{{ formatFileSize(file.size) }}</div>
                                        </div>
                                    </div>
                                    <button
                                        @click="removeFile(index)"
                                        class="text-red-600 hover:text-red-700 p-1"
                                    >
                                        ✗
                                    </button>
                                </div>
                            </div>

                            <div class="mt-4 flex justify-end">
                                <button
                                    @click="uploadFiles"
                                    :disabled="uploading"
                                    class="px-6 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white font-medium rounded transition duration-200"
                                >
                                    <span v-if="uploading">Uploading...</span>
                                    <span v-else>Upload Files</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Supported Formats -->
                <div class="mt-6 bg-white border border-gray-200 rounded overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">📋 Supported Data Formats</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="p-4 border border-gray-200 rounded">
                                <div class="font-medium text-gray-900 mb-2">💼 POS Systems</div>
                                <div class="text-sm text-gray-600">Square, Toast, Shopify POS, Clover</div>
                            </div>
                            <div class="p-4 border border-gray-200 rounded">
                                <div class="font-medium text-gray-900 mb-2">📊 Spreadsheets</div>
                                <div class="text-sm text-gray-600">Excel (.xlsx), CSV files</div>
                            </div>
                            <div class="p-4 border border-gray-200 rounded">
                                <div class="font-medium text-gray-900 mb-2">📈 Analytics</div>
                                <div class="text-sm text-gray-600">Sales, inventory, menu data</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Imports -->
                <div class="mt-6 bg-white border border-gray-200 rounded overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">📝 Import History</h3>
                        <div class="text-center py-8 text-gray-500">
                            <div class="text-4xl mb-2">📂</div>
                            <p class="text-sm">Your import history will appear here</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const dragOver = ref(false)
const selectedFiles = ref([])
const uploading = ref(false)
const importType = ref('')
const posSystem = ref('')

const form = useForm({
    files: [],
    import_type: '',
    pos_system: ''
})

function handleDrop(e) {
    dragOver.value = false
    const files = Array.from(e.dataTransfer.files)
    addFiles(files)
}

function handleFileSelect(e) {
    const files = Array.from(e.target.files)
    addFiles(files)
}

function addFiles(files) {
    const validFiles = files.filter(file => {
        const validTypes = ['text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        return validTypes.includes(file.type) || file.name.endsWith('.csv')
    })

    selectedFiles.value.push(...validFiles)
}

function removeFile(index) {
    selectedFiles.value.splice(index, 1)
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes'
    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

function detectFileType(filename) {
    const name = filename.toLowerCase()
    if (name.includes('menu') || name.includes('item')) return 'menu'
    if (name.includes('inventory') || name.includes('stock')) return 'inventory'
    if (name.includes('sales') || name.includes('transaction')) return 'sales'
    if (name.includes('recipe')) return 'recipes'
    return 'menu' // default
}

function uploadFiles() {
    if (selectedFiles.value.length === 0) return

    uploading.value = true

    // Create FormData for file upload
    const formData = new FormData()
    selectedFiles.value.forEach((file, index) => {
        formData.append(`files[${index}]`, file)
    })

    if (importType.value) {
        formData.append('import_type', importType.value)
    }

    if (posSystem.value) {
        formData.append('pos_system', posSystem.value)
    }

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')

    if (!csrfToken) {
        console.error('CSRF token not found')
        alert('Security token missing. Please refresh the page.')
        uploading.value = false
        return
    }

    // Use regular fetch with FormData for file uploads since Inertia doesn't handle files well
    fetch('/imports/upload', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`)
        }
        return response.json()
    })
    .then(data => {
        uploading.value = false
        if (data.success) {
            selectedFiles.value = []
            // Show success message
            alert(data.message || 'Upload successful!')

            // Redirect to the appropriate page
            if (data.redirect) {
                window.location.href = data.redirect
            } else {
                window.location.href = '/imports'
            }
        } else {
            console.error('Upload error:', data.error)
            alert('Upload failed: ' + (data.error || 'Unknown error'))
        }
    })
    .catch(error => {
        uploading.value = false
        console.error('Upload error:', error)
        alert('Upload failed: ' + error.message)
    })
}
</script>