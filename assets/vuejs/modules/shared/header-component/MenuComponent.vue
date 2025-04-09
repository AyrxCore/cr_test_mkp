<template>
  <div class="top-3 flex items-center lg:top-0 lg:mt-3.5 lg:w-auto">
    <div class="lg:mx-4 xl:mx-8">
      <button
        id="menu-button-categorie"
        class="flex items-center rounded border-b-2 border-b-transparent py-1 hover:opacity-75"
        @click.stop="toggleMenu"
      >
        <MenuIconComponent class="lg:w-auto" />
        <span class="ml-2 hidden text-sm lg:block">Menu</span>
      </button>

      <MenuCategoryComponent
        v-if="isMenuOpen"
        v-model="isMenuOpen"
        class="xs:flex modal text-black md:block"
      />
    </div>
    <RouterLink
      :to="{ name: ProductPageList.SELLERS }"
      replace
      class="sr-only w-fit min-w-fit rounded border-b-2 border-b-transparent bg-secondary text-center text-sm text-white hover:border-primary lg:not-sr-only lg:px-4 lg:py-2"
    >
      Tous les partenaires
    </RouterLink>
    <div
      class="sr-only flex items-center justify-around lg:not-sr-only lg:w-fit"
    >
      <RouterLink
        v-for="category in listMenu"
        :key="category.id"
        :to="{
          name: ProductPageList.PRODUCTS,
          query: { category: category.id },
        }"
        class="ml-3 border-b-2 border-b-transparent px-0.5 text-center text-sm last:mr-3 hover:border-primary xl:ml-6 xl:last:mr-6"
        replace
        @click="
          sendGaEvent('click_header_category', {
            category_name: category.name,
          })
        "
      >
        {{ category.name }}
      </RouterLink>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed, ref } from 'vue'

import { storeToRefs } from 'pinia'

import MenuCategoryComponent from '@/vuejs/modules/shared/header-component/MenuCategoryComponent.vue'
import MenuIconComponent from '@/vuejs/modules/shared/icon/MenuIconComponent.vue'

import { ProductPageList } from '@/vuejs/router/pages-list'
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'
import { useCategoryStore } from '@/vuejs/stores/category'
import { useChannelStore } from '@/vuejs/stores/channel'

import { Category } from '@/vuejs/types/Product/Category'

const isMenuOpen = ref<boolean>(false)

const { channel } = storeToRefs(useChannelStore())
const { categories } = storeToRefs(useCategoryStore())

const toggleMenu = (): void => {
  isMenuOpen.value = !isMenuOpen.value
  sendGaEvent('click_header_all_categories')
}

const listMenu = computed((): Category[] => {
  const customCategories = channel?.value?.options?.CUSTOM_HEADER_CATEGORIES
  const categoryIds = customCategories
    ? customCategories.split(',').map(Number)
    : []

  if (categoryIds.length) {
    const filteredCategories = categories.value.filter((category: Category) =>
      categoryIds.includes(category.id),
    )
    return filteredCategories.sort(
      (a: Category, b: Category) =>
        categoryIds.indexOf(a.id) - categoryIds.indexOf(b.id),
    )
  }

  return categories.value.slice(0, 6)
})
</script>
