<template>
  <BaseTemplate title="Liste des catégories">
    <div class="xs:w-[100%] m-auto my-4 max-w-screen-2xl px-5 sm:px-8">
      <BreadcrumbSharedComponent
        current-page="Catégories"
        gtm-event-name="click_categories_breadcrumbs"
      />
      <div class="mt-3.5 mb-5">
        <h3 class="text-title-primary">
          Tous nos produits et nos accords-cadres en quelques clics !
        </h3>
        <p class="text-sm md:text-base lg:text-lg">
          Retrouvez ici l'ensemble des produits et services de nos partenaires
          aux conditions négociées
        </p>
      </div>
      <div
        v-if="!categories.length"
        class="flex h-16 w-full items-center justify-center"
      >
        <LoadingComponent />
      </div>
      <div
        v-else
        class="m-auto grid auto-cols-max grid-cols-1 items-stretch gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
      >
        <div
          v-for="category in categories"
          :key="category.id"
          class="rounded-xl bg-white p-5"
        >
          <img
            v-if="category.image"
            :src="category.image"
            :alt="category.name"
            class="m-auto h-[210px]"
          />
          <CategoryComponent :category="category" />
        </div>
      </div>
    </div>
  </BaseTemplate>
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import { computed } from 'vue'
import { useCategoryStore } from '@/vuejs/stores/category'
import CategoryComponent from '@/vuejs/modules/products/components/CategoryComponent.vue'
import LoadingComponent from '@/vuejs/modules/shared/LoadingComponent.vue'
import { Category } from '@/vuejs/types/Product/Category'

const categoryStore = useCategoryStore()

const categories = computed((): Category[] => {
  return categoryStore.categoriesSortedAlphabetically
})
</script>

<style scoped></style>
