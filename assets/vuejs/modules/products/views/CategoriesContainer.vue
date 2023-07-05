<template>
  <BaseTemplate title="Qantis - MarketPlace - Liste des catégrories">
    <div class="xs:w-[100%] m-auto my-4 max-w-screen-2xl px-5 sm:px-8">
      <BreadcrumbSharedComponent :current-page="'Catégories'" />
      <div class="w-[100%] max-w-screen-2xl justify-end">
        <ContactUsButtonComponent />
      </div>
      <div class="mt-3.5 mb-5">
        <h3 class="text-title-35 text-primary">
          Tous nos produits et nos accords-cadres en quelques clics !
        </h3>
        <p class="text-sm text-gray-400 md:text-base lg:text-lg">
          Retrouvez ici l'ensemble des produits et services de nos partenaires
          aux conditions négociées
        </p>
      </div>
      <div
        v-if="categories.length < 1"
        class="flex h-16 w-full items-center justify-center"
      >
        <LoadingComponent />
      </div>
      <div
        v-else
        class="m-auto grid auto-cols-max grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
      >
        <div v-for="category in categories" :key="category.id">
          <div class="rounded-xl bg-white p-5">
            <img
              v-if="category.image"
              :src="category.image"
              :alt="category.name"
              class="h-[210px]"
            />
            <CategoryComponent :category="category" />
          </div>
        </div>
      </div>
    </div>
  </BaseTemplate>
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import { computed } from 'vue'
import { useCategoryStore } from '@/vuejs/stores/category'
import CategoryComponent from '@/vuejs/modules/products/components/CategoryComponent.vue'
import LoadingComponent from '@/vuejs/modules/shared/LoadingComponent.vue'

const categoryStore = useCategoryStore()

const categories = computed(() => {
  return categoryStore.categories
})
</script>

<style scoped></style>
