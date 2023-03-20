<template>
  <DropdownListComponent
    class="lg:overflow-y-auto lg:h-auto c-scrollbar !rounded-l-md"
  >
    <template #button-label> Filtres </template>
    <template #content>
      <div
        v-if="props.filters"
        class="bg-white px-7.5 "
      >
        <div
          v-if="companies.length > 0"
          class="bg-white pt-2 mb-1"
        >

          <h3 class="text-primary text-base md:text-lg uppercase font-bold">Partenaires ({{ companies.length }})</h3>
          <div
            v-for="(company, index) in companies"
            v-show="index < visibleCompanyFilter"
            :key="company.id"
            class="h-max  pt-3 pb-2 text-lg"
          >
            <FilterCompanyComponent
              :company="company.value"
              @change-company="changeFilterCompany"
            />
          </div>
          <button
            v-if="visibleCompanyFilter < companies.length"
            class="mt-1 mb-3 w-full !py-2"
            @click="showMoreFilters(filterType.company)"
          >
            Voir +
          </button>
        </div>
        <div
          v-if="categories.length > 0"
          class="bg-white pt-2"
        >

          <h3 class="text-primary text-base md:text-lg uppercase font-bold">Catégories ({{ categories.length }})</h3>
          <div
            v-for="(category, index) in categories"
            v-show="index < visibleCategoryFilters"
            :key="category.value.id"
            class="h-max  pt-3 pb-2 text-lg"
          >
            <FilterCategoryComponent
              :category="category.value"
              @change-category="changeFilterCategory"
            />
          </div>
          <button
            v-if="visibleCategoryFilters < categories.length"
            class="mt-1 mb-3 w-full !py-2"
            @click="showMoreFilters(filterType.category)"
          >
            Voir +
          </button>
        </div>
        <div
          v-if="filters.properties"
          class="mt-3"
        >
          <h3 class="text-primary  text-base md:text-lg uppercase font-bold">Propriétés</h3>
          <div
            v-for="(filter, index) in filters.properties"
            v-show="index < visiblePropertyFilter"
            :key="index"
            class="h-max bg-white pt-3 pb-2"
          >
            <label class="text-primary">{{ filter.name }} ({{ filter.count }})</label>

            <select
              v-if="filter.type === 'choice'"
              v-model="filter.id"
              class="w-full flex"
              :data-name="filter.name"
              @change="changeFilterProperties"
            >
              <option value="">{{ `-- ${filter.name} --` }}</option>
              <option
                v-for="child in filter.child"
                :key="child.id"
                :value="child.value"
                :data-key="child.id"
              >
                {{ child.name }}
              </option>
            </select>
          </div>
          <button
            v-if="visiblePropertyFilter < filters.properties.length"
            class="mt-1 mb-3 w-full !py-2"
            @click="showMoreFilters(filterType.property)"
          >
            Voir +
          </button>
        </div>
      </div>
    </template>
  </DropdownListComponent>
</template>
<script lang="ts" setup>
import DropdownListComponent from '@/vuejs/modules/shared/DropdownListComponent.vue'
import FilterCategoryComponent from '@/vuejs/modules/products/components/filters/FilterCategoryComponent.vue'
import { computed, ref } from 'vue'
import { useProductStore } from '@/vuejs/stores/product'
import FilterCompanyComponent from '@/vuejs/modules/products/components/filters/FilterCompanyComponent.vue';
import { filterType } from '@/vuejs/modules/products';
const emit = defineEmits(['filter-product'])
const props = defineProps({
  filters: {
    required: false,
    type: Object,
  },
})

const productStore = useProductStore()

const visibleCategoryFilters = ref<number>(5)
const visibleCompanyFilter = ref<number>(5)
const visiblePropertyFilter = ref<number>(10)

const showMoreFilters = ((type) => {
  switch (type) {
    case filterType.category:
      visibleCategoryFilters.value +=5
      break
    case filterType.company:
      visibleCompanyFilter.value +=5
      break
    case filterType.property:
      visiblePropertyFilter.value +=10
      break
  }
})

const changeFilterCompany = (event) => {
  productStore.setSelectedCompany(event.company_id)
  emit('filter-product')
}

const changeFilterCategory = (event) => {
  productStore.setSelectedCategory(event.category_id)
  emit('filter-product')
}

const changeFilterProperties = (event) => {
  productStore.setSelectedProperty({
    property_id: event.target.selectedOptions[0].dataset.key,
    value: (event.target as HTMLInputElement).value,
  })
  emit('filter-product')
}

const categories = computed(() => {
  try {
    return Object.entries(props.filters.categories).map(([key, content]) => ({
      id: key,
      value: content
    }))
  } catch (error) {
    return []
  }

})

const companies = computed(() => {
  try {
    return Object.entries(props.filters.companies).map(([key, content]) => ({
      id: key,
      value: content
    }))
  } catch (error) {
    return []
  }
})

</script>

<style scoped>
.c-scrollbar::-webkit-scrollbar {
  width: 12px;
}

.c-scrollbar::-webkit-scrollbar-track {
  @apply bg-white;
}

.c-scrollbar::-webkit-scrollbar-thumb {
  @apply bg-primary;
}

.c-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #9f9f9f;
}
</style>
