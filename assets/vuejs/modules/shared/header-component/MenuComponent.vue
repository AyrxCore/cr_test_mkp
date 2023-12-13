<template>
  <div class="top-3 flex items-center lg:top-0 lg:mt-3.5 lg:w-auto">
    <div class="ml-4 lg:ml-14 lg:mr-16">
      <div>
        <button
          id="menu-button-categorie"
          class="flex items-center rounded border-b-2 border-b-transparent py-1 hover:opacity-75"
          @click.stop="toggleMenu"
        >
          <MenuIconComponent
            class="mr-0.5 text-xl lg:w-auto"
            stroke-color="#000000"
          />
          <span class="ml-4 hidden lg:block"> Toutes les catégories </span>
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
          gtmEvent('click_header_category', {
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

import MenuCategoryComponent from '@/vuejs/modules/shared/header-component/MenuCategoryComponent.vue'
import MenuIconComponent from '@/vuejs/modules/shared/icon/MenuIconComponent.vue'

import { ProductPageList } from '@/vuejs/router/pages-list'
import { buildStandardGtmData, gtmMixinPushEvent } from '@/vuejs/services/gtm'
import { useUserStore } from '@/vuejs/stores/user'
import { useCategoryStore } from '@/vuejs/stores/category'
import { useChannelStore } from '@/vuejs/stores/channel'

const userStore = useUserStore()

const isMenuOpen = ref<boolean>(false)

const toggleMenu = (): void => {
  isMenuOpen.value = !isMenuOpen.value
  gtmEvent('click_header_all_categories')
}

const categoryStore = useCategoryStore()
const channelStore = useChannelStore()

const currentChannel = channelStore.currentChannel

const listMenu = computed(() => {
  return categoryStore.listMenu
})

const gtmEvent = (eventName: string, additionalData = null) => {
  let data = buildStandardGtmData(userStore.user['@id'], currentChannel.name)
  data = additionalData ? { ...data, ...additionalData } : data
  gtmMixinPushEvent(eventName, data)
}
</script>
