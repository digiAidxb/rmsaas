<template>
    <div class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center z-50" @click="preventClose">
        <div class="bg-white rounded-xl shadow-2xl max-w-6xl w-full mx-4 my-8 max-h-[90vh] overflow-y-auto border border-gray-200" @click.stop>
            <!-- Modal Header -->
            <div class="sticky top-0 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-t-xl px-6 py-5 flex items-center justify-between z-10">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white bg-opacity-20 backdrop-blur-sm rounded-lg flex items-center justify-center border border-white border-opacity-20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-white tracking-tight">Restaurant Management Setup</h1>
                        <p class="text-indigo-100 text-sm font-medium">Professional data import & configuration system</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="text-sm text-white text-opacity-80 hidden sm:flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="font-medium">Secure Setup</span>
                    </div>
                </div>
            </div>

            <!-- Modal Content -->
            <div class="px-6 py-8 bg-gray-50">
                <slot />
            </div>
        </div>
    </div>
</template>

<script setup>
// Prevent modal dismissal
function preventClose(event) {
    event.preventDefault();
    event.stopPropagation();
}

// Disable escape key
import { onMounted, onUnmounted } from 'vue'

const handleKeydown = (event) => {
    if (event.key === 'Escape') {
        event.preventDefault();
        event.stopPropagation();
    }
}

onMounted(() => {
    document.addEventListener('keydown', handleKeydown);
    document.body.style.overflow = 'hidden';
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown);
    document.body.style.overflow = '';
});
</script>

<style scoped>
/* Prevent text selection in modal overlay */
.fixed {
    user-select: none;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* Allow text selection in modal content */
.bg-white {
    user-select: text;
}

/* Professional backdrop blur effect */
.backdrop-blur-sm {
    backdrop-filter: blur(4px);
}

/* Enterprise-grade scrollbar */
.overflow-y-auto::-webkit-scrollbar {
    width: 8px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: rgba(243, 244, 246, 0.8);
    border-radius: 8px;
    margin: 8px 0;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border-radius: 8px;
    border: 2px solid rgba(255, 255, 255, 0.2);
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    border-color: rgba(255, 255, 255, 0.3);
}

/* Enhanced modal animations */
@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-20px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.bg-white {
    animation: modalSlideIn 0.3s ease-out;
}

/* Professional gradient header with subtle texture */
.bg-gradient-to-r {
    background-image: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    position: relative;
}

.bg-gradient-to-r::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='7' cy='7' r='1'/%3E%3Ccircle cx='27' cy='7' r='1'/%3E%3Ccircle cx='47' cy='7' r='1'/%3E%3Ccircle cx='7' cy='27' r='1'/%3E%3Ccircle cx='27' cy='27' r='1'/%3E%3Ccircle cx='47' cy='27' r='1'/%3E%3Ccircle cx='7' cy='47' r='1'/%3E%3Ccircle cx='27' cy='47' r='1'/%3E%3Ccircle cx='47' cy='47' r='1'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    border-radius: 0.75rem 0.75rem 0 0;
}

/* Ensure content is above the pattern */
.bg-gradient-to-r > * {
    position: relative;
    z-index: 1;
}
</style>