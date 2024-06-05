<template>
  <div class="top-3 flex items-center lg:top-0 lg:mt-3.5 lg:w-auto">
    <div class="ml-4 lg:ml-14 lg:mr-16">
      <div>
        <button
          id="menu-button-categorie"
          class="flex items-center rounded border-b-2 border-b-transparent py-1 hover:opacity-75"
          @click.stop="toggleMenu"
        >
          <MenuIconComponent class="mr-0.5 text-xl lg:w-auto" />
        </button>

        <MenuCategoryComponent
          v-if="isMenuOpen"
          v-model="isMenuOpen"
          class="xs:flex modal text-black md:block"
        />
      </div>
    </div>
    <div
      v-for="category in listMenu"
      :key="category.id"
      class="sr-only !mr-16 flex px-2 lg:not-sr-only"
    >
      <RouterLink
        :to="{
          name: ProductPageList.PRODUCTS,
          query: { category: category.id },
        }"
        replace
        class="border-b-2 border-b-transparent px-0.5 text-sm hover:border-primary"
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
