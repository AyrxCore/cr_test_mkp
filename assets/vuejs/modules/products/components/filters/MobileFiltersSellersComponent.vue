<template>
  <ButtonComponent
    class="my-2 w-full !rounded-none border border-gray-300 !bg-white !text-primary lg:!hidden"
    @click.stop="showFilters = true"
  >
    <FilterIconComponent />
    <span class="text-lg">Filtrer</span>
  </ButtonComponent>
  <div v-if="showFilters" class="modal-overlay">
    <FiltersSellerComponent
      v-if="categoriesFilters"
      v-click-outside="() => (showFilters = false)"
      :categories="categoriesFilters"
      class="fixed bottom-0 left-0 z-50 h-[66%] w-screen overflow-scroll rounded-b-none"
      @filter-product="emit('filter-products')"
      @close-filters="showFilters = false"
    />
  </div>
</template>
<script lang="ts" setup>
import { storeToRefs } from 'pinia'
import { computed, ref } from 'vue'

import { useProductStore } from '@/vuejs/stores/product'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import FiltersSellerComponent from '@/vuejs/modules/products/components/filters/FiltersSellerComponent.vue'
import FilterIconComponent from '@/vuejs/modules/shared/icon/FilterIconComponent.vue'

const emit = defineEmits(['filter-products'])

const productStore = useProductStore()
const { products } = storeToRefs(productStore)

const showFilters = ref<boolean>(false)

const categoriesFilters = computed(() => {
  return products.value?.filters?.categories
})
</script>
