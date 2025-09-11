<template>
  <div class="mt-16 bg-white pt-6">
    <div class="m-auto max-w-screen-98 px-12">
      <h3 class="text-title-primary">
        Nos catégories de produits et d'accords-cadres
      </h3>
    </div>
    <div class="flex w-full flex-col items-center pr-4 text-lg sm:flex">
      <div class="list-categories">
        <div
          v-for="category in categories"
          :key="category.id"
          class="list-categories-items cursor-pointer hover:bg-primary hover:text-white"
        >
          <RouterLink
            :to="{
              name: ProductPageList.PRODUCTS,
              query: { category: category.id },
            }"
            class="px-0.5 text-sm"
            @click="
              sendGtmEvent('cat_category_click', {
                link_text: $event.target.innerText,
                link_url: router.resolve({
                  name: ProductPageList.CATEGORIES,
                }).fullPath,
                origin_url: router.currentRoute.value.fullPath,
              })
            "
          >
            {{ category.name }}
          </RouterLink>
        </div>
      </div>
      <div class="my-8">
        <RouterLink
          :to="{ name: ProductPageList.CATEGORIES }"
          class="text-md text-primary underline"
          @click="
            sendGtmEvent('cat_category_click', {
              link_text: $event.target.innerText,
              link_url: router.resolve({
                name: ProductPageList.CATEGORIES,
              }).fullPath,
              origin_url: router.currentRoute.value.fullPath,
            })
          "
        >
          Voir plus
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed, onBeforeMount, onUnmounted, ref } from 'vue'

import router from '@/vuejs/router'
import { ProductPageList } from '@/vuejs/router/pages-list'
import { useCategoryStore } from '@/vuejs/stores/category'
import { sendGtmEvent } from '@/vuejs/services/gtm'
import { Category } from '@/vuejs/types/Product/Category'

const categoryStore = useCategoryStore()

const windowWidth = ref<number>(null)

const categories = computed((): Category[] => {
  return categoryStore.categoriesSortedAlphabetically.slice(0, 7)
})

const onResize = () => {
  windowWidth.value = window.innerWidth
}

onBeforeMount(() => {
  windowWidth.value = window.innerWidth
  window.addEventListener('resize', onResize)
})

onUnmounted(() => {
  window.removeEventListener('resize', onResize)
})
</script>

<style scoped>
::-webkit-scrollbar {
  display: none;
}
</style>
