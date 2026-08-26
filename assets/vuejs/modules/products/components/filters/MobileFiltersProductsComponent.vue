<template>
  <ButtonComponent
    class="my-2 w-full rounded-none! border border-gray-300 bg-white! text-primary! lg:hidden!"
    @click.stop="showFilters = true"
  >
    <FilterIconComponent />
    <span class="text-lg">Filtrer</span>
  </ButtonComponent>
  <div v-if="showFilters" class="modal-overlay">
    <FiltersProductComponent
      v-if="filters"
      v-click-outside="() => (showFilters = false)"
      :filters="filters"
      class="fixed bottom-0 left-0 z-50 h-[66%] w-screen overflow-scroll rounded-b-none"
      @filter-product="emit('filter-products')"
      @close-filters="showFilters = false"
    />
  </div>
</template>
<script lang="ts" setup>
import { computed, ref } from 'vue'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import FiltersProductComponent from '@/vuejs/modules/products/components/filters/FiltersProductComponent.vue'
import FilterIconComponent from '@/vuejs/modules/shared/icon/FilterIconComponent.vue'

import { useProductStore } from '@/vuejs/stores/product'

const emit = defineEmits(['filter-products'])

const productStore = useProductStore()

const showFilters = ref<boolean>(false)

const filters = computed(() => {
  return productStore.products?.filters
})
</script>
