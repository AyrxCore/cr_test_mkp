<template>
  <div class="mt-3 flex flex-col items-center justify-center space-y-2">
    <div
      v-if="error || isLoading"
      class="flex flex-col items-center justify-center space-y-2"
    >
      <!-- Error message -->
      <p
        v-if="error"
        class="rounded-md bg-white px-3 py-2 text-center text-sm font-medium text-red-500 shadow-sm md:text-base"
      >
        {{ error }}
      </p>

      <!-- Retry button -->
      <button
        v-if="!isLoading && error"
        class="mt-1 rounded-md bg-white px-3 py-1 text-sm font-medium text-primary hover:bg-opacity-100 hover:text-secondary"
        @click="$emit('retry')"
      >
        Cliquez ici pour réessayer
      </button>

      <!-- Loading message -->
      <p
        v-if="isLoading"
        class="rounded-md bg-white px-3 py-2 text-center text-sm font-medium text-primary shadow-sm md:text-base"
      >
        Géolocalisation en cours...
      </p>
    </div>
  </div>
</template>

<script lang="ts" setup>

defineProps({
  geolocationActive: {
    type: Boolean,
    required: true,
  },
  error: {
    type: String,
    default: '',
  },
  isLoading: {
    type: Boolean,
    required: true,
  },
})

defineEmits(['retry'])
</script>
