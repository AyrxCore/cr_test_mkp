<template>
  <div class="rounded-xl bg-white px-6 py-4 text-primary">
    <div class="mb-4 flex items-center justify-between">
      <h3 class="text-xl font-bold uppercase md:hidden">Filtres</h3>
      <ButtonComponent
        v-if="hasFilters"
        class="button button-primary-outline mx-auto"
        @click="clearFilters"
      >
        Réinitialiser les filtres
      </ButtonComponent>
      <CloseIcon
        class="cursor-pointer hover:text-secondary md:hidden"
        @click="emit('close-filters')"
      />
    </div>
    <template v-if="sellers.length > 0">
      <h3 class="text-base font-bold uppercase md:text-lg">Partenaires</h3>
      <FilterSellerComponent
        v-for="(seller, index) in sellers"
        v-show="index < visibleSellersFilter"
        :key="seller.id"
        :seller="seller"
        @change-seller="changeFilterSeller(seller)"
      />
      <button
        v-if="visibleSellersFilter < sellers.length"
        class="my-2 w-full"
        @click.stop="showMoreFilters(filterType.seller)"
      >
        Voir +
      </button>
    </template>
    <template v-if="categories.length > 0">
      <h3 class="mt-4 text-base font-bold uppercase md:text-lg">Catégories</h3>
      <FilterCategoryComponent
        v-for="(category, index) in categories"
        v-show="index < visibleCategoryFilters"
        :key="category.id"
        :category="category"
        @change-category="changeFilterCategory"
      />
      <button
        v-if="visibleCategoryFilters < categories.length"
        class="my-2 w-full"
        @click.stop="showMoreFilters(filterType.category)"
      >
        Voir +
      </button>
    </template>
    <template v-if="filters.properties">
      <h3 class="mt-4 text-base font-bold uppercase md:text-lg">Propriétés</h3>
      <FilterPropertyComponent
        v-for="(property, index) in filters.properties"
        v-show="index < visiblePropertyFilter"
        :key="property.id"
        :property="property"
        class="my-2 cursor-pointer text-sm"
        @change-property="changeFilterProperties"
      />
      <button
        v-if="visiblePropertyFilter < filters.properties.length"
        class="my-2 w-full"
        @click.stop="showMoreFilters(filterType.property)"
      >
        Voir +
      </button>
    </template>
  </div>
</template>

<script lang="ts" setup>
import { computed, ref } from 'vue'
import { storeToRefs } from 'pinia'

import { useProductStore } from '@/vuejs/stores/product'
import { sortAscName } from '@/vuejs/services/categories'
import { ProductFilters } from '@/vuejs/types/Product'
import { filterType } from '@/vuejs/modules/products'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import FilterCategoryComponent from '@/vuejs/modules/products/components/filters/FilterCategoryComponent.vue'
import FilterSellerComponent from '@/vuejs/modules/products/components/filters/FilterSellerComponent.vue'
import FilterPropertyComponent from '@/vuejs/modules/products/components/filters/FilterPropertyComponent.vue'
import CloseIcon from '@/vuejs/modules/shared/icon/CloseIconComponent.vue'
import { Seller } from '@/vuejs/types/Seller'
import { Category } from '@/vuejs/types/Product/Category'

const emit = defineEmits(['filter-product', 'close-filters'])

const productStore = useProductStore()

const { hasFilters } = storeToRefs(productStore)

const visibleCategoryFilters = ref<number>(5)
const visibleSellersFilter = ref<number>(5)
const visiblePropertyFilter = ref<number>(10)

const showMoreFilters = (type: string) => {
  switch (type) {
    case filterType.category:
      visibleCategoryFilters.value += 5
      break
    case filterType.seller:
      visibleSellersFilter.value += 5
      break
    case filterType.property:
      visiblePropertyFilter.value += 10
      break
  }
}

const changeFilterSeller = (seller: Seller) => {
  productStore.setSelectedSeller(seller.externalId)
}

const changeFilterCategory = (categoryId: number) => {
  productStore.setSelectedCategory(categoryId)
}

const changeFilterProperties = (event) => {
  productStore.setSelectedProperty(event)
}

const filters = computed((): ProductFilters => {
  return productStore.products?.filters
})

const categories = computed((): Category[] => {
  return filters.value?.categories
    ? sortAscName(filters.value.categories)
    : []
})

const sellers = computed((): Seller[] => {
  return filters.value?.sellers
    ? [...filters.value.sellers].sort((a, b) => a.name.localeCompare(b.name))
    : []
})

const clearFilters = () => {
  productStore.clearFilters()
}
</script>
