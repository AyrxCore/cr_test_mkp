<template>
  <div class="rounded-xl bg-white px-6 py-4 text-primary">
    <div class="mb-4 flex items-center justify-between">
      <h3 class="text-xl font-bold uppercase md:hidden">Filtres</h3>
      <ButtonComponent
        v-if="hasFilter"
        class="button button-primary-outline mx-auto"
        @click="clearFilters"
      >
        Réinitialiser les filtres
      </ButtonComponent>
    </div>
    <template v-if="categories.length > 0">
      <h3 class="mt-4 text-base font-bold uppercase md:text-lg">Catégories</h3>
      <FilterCategoryComponent
        v-for="(category, index) in sortedCategories"
        v-show="index < visibleCategoryFilters"
        :key="category.id"
        :category="category"
        @change-category="changeFilterCategory"
      />
      <button
        v-if="visibleCategoryFilters < categories.length"
        class="my-2 w-full"
        @click.stop="showMoreFilters"
      >
        Voir +
      </button>
    </template>

    <CloseIcon
      class="cursor-pointer hover:text-secondary md:hidden"
      @click="emit('close-filters')"
    />
  </div>
</template>
<script lang="ts" setup>
import { computed, PropType, ref } from 'vue'
import { useRoute } from 'vue-router'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import CloseIcon from '@/vuejs/modules/shared/icon/CloseIconComponent.vue'
import FilterCategoryComponent from '@/vuejs/modules/products/components/filters/FilterCategoryComponent.vue'

import router from '@/vuejs/router'
import { sortAscName } from '@/vuejs/services/categories'
import { ProductPageList } from '@/vuejs/router/pages-list'
import { useSellerStore } from '@/vuejs/stores/seller'
import { Category } from '@/vuejs/types/Product/Category'

const sellerStore = useSellerStore()

const route = useRoute()

const emit = defineEmits(['filter-product', 'close-filters'])
const props = defineProps({
  categories: {
    required: true,
    type: Object as PropType<Category[]>,
  },
})

const visibleCategoryFilters = ref<number>(5)

const showMoreFilters = (): void => {
  visibleCategoryFilters.value += 5
}

const changeFilterCategory = (categoryId: number): void => {
  router.push({
    name: ProductPageList.SELLERS,
    query: { category: categoryId },
  })
}

const sortedCategories = computed((): Category[] => {
  return sortAscName(props.categories)
})

const clearFilters = (): void => {
  sellerStore.allSellers = []
  router.push({
    name: ProductPageList.SELLERS,
    query: {},
  })
}

const hasFilter = computed((): boolean => {
  return Object.keys(route.query).length > 0
})
</script>
