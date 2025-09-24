<template>
  <ButtonComponent
    class="relative my-2 w-full !rounded-none border border-gray-300 !bg-white !text-primary lg:!hidden"
    :disabled="isLoading"
    @click.stop="!isLoading && (showFilters = true)"
  >
    <template v-if="isLoading">
      <div
        class="absolute inset-0 flex items-center justify-center rounded bg-gray-500/50"
      >
        <LoadingComponent />
      </div>
    </template>
    <FilterIconComponent :class="{ 'opacity-50': isLoading }" />
    <span class="text-lg" :class="{ 'opacity-50': isLoading }">Filtrer</span>
  </ButtonComponent>
  <div v-if="showFilters" class="modal-overlay">
    <div
      v-if="categories.length > 0"
      v-click-outside="() => (showFilters = false)"
      class="fixed bottom-0 left-0 z-50 h-[66%] w-screen rounded-t-xl bg-white"
    >
      <div class="flex items-center justify-between p-4">
        <h2 class="text-xl font-bold uppercase text-primary">FILTRES</h2>
        <button
          class="flex h-8 w-8 items-center justify-center rounded-full hover:bg-gray-100"
          @click="showFilters = false"
        >
          <svg
            class="h-6 w-6 text-gray-600"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M6 18L18 6M6 6l12 12"
            />
          </svg>
        </button>
      </div>
      <MapFiltersComponent
        :categories="categories"
        :selected-category="selectedCategory"
        class="h-full overflow-scroll p-4"
        @category-changed="handleCategoryChange"
        @reset-filters="handleReset"
        @close-filters="showFilters = false"
      />
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, PropType } from 'vue'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import LoadingComponent from '@/vuejs/modules/shared/LoadingComponent.vue'
import MapFiltersComponent from '@/vuejs/modules/map/components/MapFiltersComponent.vue'
import FilterIconComponent from '@/vuejs/modules/shared/icon/FilterIconComponent.vue'

defineProps({
  categories: {
    type: Array as PropType<Array<{ id: string; name: string }>>,
    required: true,
  },
  selectedCategory: {
    type: String,
    required: false,
    default: null,
  },
  isLoading: {
    type: Boolean,
    required: false,
    default: false,
  },
})

const emit = defineEmits(['category-changed', 'reset-filters'])

const showFilters = ref<boolean>(false)

const handleCategoryChange = (categoryId: string | null) => {
  emit('category-changed', categoryId)
  showFilters.value = false
}

const handleReset = () => {
  emit('reset-filters')
  showFilters.value = false
}
</script>
