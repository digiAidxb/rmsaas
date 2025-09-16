<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Simple Header -->
        <nav class="border-b border-gray-100 bg-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between items-center">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <h1 class="text-lg font-semibold text-gray-900">Restaurant Setup</h1>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="text-sm text-gray-500">Secure Setup</span>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white">
                        <slot />
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
// Simple onboarding layout - no special functionality needed
</script>

