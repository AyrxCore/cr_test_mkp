<template>
  <ButtonComponent
    class="mb-2 w-full rounded-none! border border-gray-300 bg-white! text-primary! lg:hidden!"
    @click.stop="showFilters = true"
  >
    <FilterIconComponent />
    <span class="text-lg">Filtrer</span>
  </ButtonComponent>
  <div v-if="showFilters" class="modal-overlay">
    <FiltersSellerComponent
      v-if="categories"
      v-click-outside="() => (showFilters = false)"
      :categories="categories"
      class="fixed bottom-0 left-0 z-50 min-h-[30%] w-screen overflow-scroll rounded-b-none"
      @filter-product="emit('filter-products')"
      @close-filters="showFilters = false"
    />
  </div>
</template>

<script lang="ts" setup>
import { storeToRefs } from 'pinia'
import { ref } from 'vue'

import { useCategoryStore } from '@/vuejs/stores/category'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import FiltersSellerComponent from '@/vuejs/modules/products/components/filters/FiltersSellerComponent.vue'
import FilterIconComponent from '@/vuejs/modules/shared/icon/FilterIconComponent.vue'

const emit = defineEmits(['filter-products'])

const categoryStore = useCategoryStore()

const { categories } = storeToRefs(categoryStore)

const showFilters = ref<boolean>(false)
</script>
