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
    <template v-if="companies.length > 0">
      <h3 class="text-base font-bold uppercase md:text-lg">Partenaires</h3>
      <FilterCompanyComponent
        v-for="(company, index) in companies"
        v-show="index < visibleCompanyFilter"
        :key="company.id"
        :company="company.value"
        @change-company="changeFilterCompany"
      />
      <button
        v-if="visibleCompanyFilter < companies.length"
        class="my-2 w-full"
        @click.stop="showMoreFilters(filterType.company)"
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
      <div
        v-for="(property, index) in filters.properties"
        v-show="index < visiblePropertyFilter"
        :key="index"
        class="my-2 cursor-pointer text-sm"
      >
        <label :for="property.id" class="cursor-pointer">
          {{ property.name }}
        </label>
        <select
          v-if="property.type === 'choice'"
          :id="property.id"
          v-model="property.id"
          class="flex w-full cursor-pointer"
          @change="changeFilterProperties"
        >
          <option value="">{{ `-- ${property.name} --` }}</option>
          <option
            v-for="child in property.children"
            :key="child.id"
            :data-key="child.id"
            :value="child.value"
          >
            {{ child.name }}
          </option>
        </select>
      </div>
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
import { sortCategories } from '@/vuejs/services/categories'
import { ProductCategory } from '@/vuejs/types/Product'
import { filterType } from '@/vuejs/modules/products'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import FilterCategoryComponent from '@/vuejs/modules/products/components/filters/FilterCategoryComponent.vue'
import FilterCompanyComponent from '@/vuejs/modules/products/components/filters/FilterCompanyComponent.vue'
import CloseIcon from '@/vuejs/modules/shared/icon/CloseIconComponent.vue'

const emit = defineEmits(['filter-product', 'close-filters'])

const productStore = useProductStore()

const { hasFilters } = storeToRefs(productStore)

const visibleCategoryFilters = ref<number>(5)
const visibleCompanyFilter = ref<number>(5)
const visiblePropertyFilter = ref<number>(10)

const showMoreFilters = (type: string) => {
  switch (type) {
    case filterType.category:
      visibleCategoryFilters.value += 5
      break
    case filterType.company:
      visibleCompanyFilter.value += 5
      break
    case filterType.property:
      visiblePropertyFilter.value += 10
      break
  }
}

const changeFilterCompany = (event) => {
  productStore.setSelectedCompany(event.company_id)
}

const changeFilterCategory = (categoryId: number) => {
  productStore.setSelectedCategory(categoryId)
}

const changeFilterProperties = (event) => {
  productStore.setSelectedProperty({
    property_id: event.target.selectedOptions[0].dataset.key,
    value: (event.target as HTMLInputElement).value,
  })
}

const filters = computed(() => {
  return productStore.products?.filters
})

const categories = computed((): ProductCategory[] => {
  return filters.value?.categories
    ? sortCategories(filters.value.categories)
    : []
})

const companies = computed(() => {
  try {
    return Object.entries(filters.value.companies).map(([key, content]) => ({
      id: key,
      value: content,
    }))
  } catch (error) {
    return []
  }
})

const clearFilters = () => {
  productStore.clearFilters()
}
</script>
