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
      class="sr-only w-fit min-w-fit rounded border-b-2 border-b-transparent bg-secondary text-center text-sm text-white hover:border-primary lg:not-sr-only lg:px-4 lg:py-2"
      replace
    >
      Tous les partenaires
    </RouterLink>
    <div
      v-if="isLoaded"
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
          sendGtmEvent('menu_click', {
            link_text: category.name,
            link_url: router.resolve({
              name: ProductPageList.PRODUCTS,
              query: { category: category.id },
            }).fullPath,
            origin_url: router.currentRoute.value.fullPath,
          })
        "
      >
        {{ category.name }}
      </RouterLink>
      <RouterLink
        v-if="sustainableCategoryUi"
        :class="sustainableCategoryUi?.textClass"
        :to="{
          name: ProductPageList.PRODUCTS,
          query: { category: SUSTAINABLE_PURCHASES_CATEGORY_ID },
        }"
        class="ml-3 inline-flex items-center border-b-2 border-b-transparent px-0.5 text-center text-sm last:mr-3 hover:border-green-qantis xl:ml-6 xl:last:mr-6"
        replace
        @click="
          sendGtmEvent('menu_click', {
            link_text: 'Achats durables',
            link_url: router.resolve({
              name: ProductPageList.PRODUCTS,
              query: { category: SUSTAINABLE_PURCHASES_CATEGORY_ID },
            }).fullPath,
            origin_url: router.currentRoute.value.fullPath,
          })
        "
      >
        <component
          :is="sustainableCategoryUi?.icon"
          class="mr-1 h-[1em] w-[1em] shrink-0 align-middle"
        />
        Achats durables
      </RouterLink>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed, ref } from 'vue'
import { storeToRefs } from 'pinia'

import router from '@/vuejs/router'
import { ProductPageList } from '@/vuejs/router/pages-list'
import { useCategoryStore } from '@/vuejs/stores/category'
import { useChannelStore } from '@/vuejs/stores/channel'
import { sendGtmEvent } from '@/vuejs/services/gtm'
import { Category } from '@/vuejs/types/Product/Category'
import {
  CATEGORY_CONFIGS,
  CategoryConfig,
  SUSTAINABLE_PURCHASES_CATEGORY_ID,
} from '@/vuejs/constants/categoryConfigs'

import MenuCategoryComponent from '@/vuejs/modules/shared/header-component/MenuCategoryComponent.vue'
import MenuIconComponent from '@/vuejs/modules/shared/icon/MenuIconComponent.vue'

const { channel } = storeToRefs(useChannelStore())
const { categories, isLoaded } = storeToRefs(useCategoryStore())

const isMenuOpen = ref<boolean>(false)

const listMenu = computed((): Category[] => {
  const customCategories = channel?.value?.options?.CUSTOM_HEADER_CATEGORIES
  const categoryIds = customCategories ? customCategories.split(',') : []
  if (categoryIds.length) {
    const filteredCategories = categories.value.filter((category: Category) =>
      categoryIds.includes(category.id) &&
      category.id !== SUSTAINABLE_PURCHASES_CATEGORY_ID,
    )
    return filteredCategories.sort(
      (a: Category, b: Category) =>
        categoryIds.indexOf(a.id) - categoryIds.indexOf(b.id),
    )
  }

  return categories.value
    .filter((c: Category) => c.id !== SUSTAINABLE_PURCHASES_CATEGORY_ID)
    .slice(0, 6)
})

const toggleMenu = (): void => {
  isMenuOpen.value = !isMenuOpen.value
  sendGtmEvent('menu_selector_click')
}

const sustainableCategoryUi = computed((): CategoryConfig | null => {
  const hasAccess = categories.value.some(
    (c: Category) => c.id === SUSTAINABLE_PURCHASES_CATEGORY_ID,
  )
  return hasAccess ? CATEGORY_CONFIGS[SUSTAINABLE_PURCHASES_CATEGORY_ID] : null
})
</script>
