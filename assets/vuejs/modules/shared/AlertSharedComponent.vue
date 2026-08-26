<template>
  <div
    id="alert-1"
    class="mb-4 flex rounded-lg p-4 text-sm dark:bg-blue-200 dark:text-blue-800"
    :class="classes"
    role="alert"
  >
    <div class="flex">
      <div>
        <svg
          aria-hidden="true"
          class="mr-3 inline h-5 w-5 flex-shrink-0"
          fill="currentColor"
          viewBox="0 0 20 20"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            fill-rule="evenodd"
            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0
              11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001
              1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
            clip-rule="evenodd"
          />
        </svg>
      </div>
      <div class="ml-3 flex justify-start text-sm font-medium">
        <p v-html="alertStore.message" />
      </div>
    </div>
    <button
      type="button"
      class="-mx-1.5 -my-1.5 ml-auto inline-flex h-8 w-8 rounded-lg p-1.5 focus:ring-2 focus:ring-blue-400"
      :class="classes"
      data-dismiss-target="#alert-1"
      aria-label="Close"
      @click="closeClick"
    >
      <span class="sr-only">Close</span>
      <svg
        aria-hidden="true"
        class="h-5 w-5"
        fill="currentColor"
        viewBox="0 0 20 20"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          fill-rule="evenodd"
          d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0
            111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10
            11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1
            0 010-1.414z"
          clip-rule="evenodd"
        />
      </svg>
    </button>
  </div>
</template>
<script lang="ts" setup>
import { computed } from 'vue'
import { useAlertStore } from '@/vuejs/stores/alert'
import { AlertType } from '@/vuejs/types/Alert'

const alertStore = useAlertStore()

const classes = computed((): string => {
  let classes = ''
  switch (alertStore.type) {
    case AlertType.info:
      classes = 'text-blue-700 bg-blue-100 hover:bg-blue-200'
      break
    case AlertType.success: {
      classes = 'text-green-700! bg-green-100! hover:bg-green-200!'
      break
    }
    case AlertType.danger: {
      classes = 'text-red-700! bg-red-100! hover:bg-red-200!'
      break
    }
    case AlertType.warning: {
      classes = 'text-yellow-700! bg-yellow-100! hover:bg-yellow-200!'
      break
    }
  }
  return classes
})

const closeClick = (): void => {
  alertStore.setClose()
}
</script>
