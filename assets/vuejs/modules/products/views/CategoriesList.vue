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
        class="m-auto grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
      >
        <div v-for="(category, key) in listItemsCategories" :key="key">
          <CategoryComponent :category="category" />
        </div>
      </div>
    </div>
  </BaseTemplate>
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import { getImage, listCategories } from '@/vuejs/services/utils'
import defaultImage from '@/vuejs/assets/img/default-image.png'
import { computed } from 'vue'
import CategoryComponent from '@/vuejs/modules/products/components/CategoryComponent.vue'

const listItemsCategories = computed(() => {
  const categories = []

  listCategories.value.forEach((value, index) => {
    const children = []
    for (let i = 0; i < 3; i++) {
      const subChildren = []
      for (let i = 0; i < 3; i++) {
        subChildren.push({
          name: 'Sous-catégorie ' + i,
        })
      }
      children.push({
        name: 'Catégorie ' + i,
        child: subChildren,
      })
    }
    categories.push({
      id: index,
      name: value,
      image: getImage(defaultImage),
      child: children,
    })
  })
  console.log(categories)
  return categories
})
</script>

<style scoped></style>
