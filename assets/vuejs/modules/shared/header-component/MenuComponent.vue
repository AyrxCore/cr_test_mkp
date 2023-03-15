<template>
  <div
    class="top-3 flex items-center text-white lg:top-0 lg:mt-5 lg:w-auto lg:justify-around"
  >
    <div>
      <div class="">
        <button
          id="menu-button-categorie"
          class="flex items-center rounded border-b-2 border-b-transparent py-1 hover:opacity-75 lg:px-3"
          @click.stop="toggleMenu"
        >
          <MenuIconComponent class="mr-0.5 text-xl lg:w-auto" />
          <span class="ml-4 hidden lg:block">Toutes les catégories</span>
        </button>

        <MenuCategoryComponent v-model="isMenuOpen" class="hidden sm:block" />
      </div>

    </div>
    <div
      v-for="category in listMenu"
      :key="category.id"
      class="sr-only flex px-2 lg:not-sr-only"
    >
      <RouterLink
        :to="{name: ProductPageList.PRODUCTS, query: { category: category.id}}"
        replace
        class="border-b-2 border-b-transparent hover:border-secondary text-sm px-0.5"
      >
        {{ category.name }}
      </RouterLink>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed, ref } from 'vue'
import MenuIconComponent from '@/vuejs/modules/shared/icon/MenuIconComponent.vue'
import { useCategoryStore } from '@/vuejs/stores/category'
import MenuCategoryComponent from '@/vuejs/modules/shared/header-component/MenuCategoryComponent.vue'
import { ProductPageList } from '@/vuejs/router/pages-list'

const isMenuOpen = ref<boolean>(false)

const toggleMenu = (): void => {
  isMenuOpen.value = !isMenuOpen.value
}

const categoryStore = useCategoryStore()

const listMenu = computed(() => {
  return categoryStore.listMenu
})


</script>
