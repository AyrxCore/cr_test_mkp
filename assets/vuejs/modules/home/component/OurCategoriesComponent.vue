<template>
  <div class="mt-16 text-center">
    <h3 class="home-subtitle font-bold text-primary">
      Nos catégories de produits et d'accords-cadres
    </h3>
  </div>
  <div class="mt-5 flex w-full text-lg sm:flex">
    <DropdownListComponent>
      <template #button-label> Toutes les catégories</template>
      <template #content>
        <div class="list-categories">
          <div
            v-for="category in categories"
            :key="category.id"
            class="list-categories-items"
          >
            <RouterLink
              :to="{name: ProductPageList.PRODUCTS, query: { category: category.id}}"
              class="text-sm px-0.5"
            >
              {{ category.name }}
            </RouterLink>
          </div>
        </div>
      </template>
    </DropdownListComponent>
  </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import DropdownListComponent from '@/vuejs/modules/shared/DropdownListComponent.vue'
import { useCategoryStore } from '@/vuejs/stores/category'
import { ProductPageList } from '@/vuejs/router/pages-list'


const categoryStore = useCategoryStore()

const categories = computed(() => {
  return categoryStore.categories
})
</script>

<style scoped></style>
