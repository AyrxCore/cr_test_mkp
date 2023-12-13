<template>
  <div class="mt-16 bg-white px-12 pt-6">
    <div class="m-auto max-w-screen-98 text-center">
      <h3 class="home-title font-bold text-primary">
        Nos catégories de produits et d'accords-cadres
      </h3>
    </div>
    <div class="ml-1 flex w-full flex-col text-lg sm:flex md:items-center">
      <DropdownListComponent>
        <template #button-label> Toutes les catégories</template>
        <template #content>
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
                  gtmEvent('click_cat_category', {
                    category_name: category.name,
                  })
                "
              >
                {{ category.name }}
              </RouterLink>
            </div>
          </div>
        </template>
      </DropdownListComponent>
      <div class="my-8">
        <router-link
          :to="{ name: ProductPageList.CATEGORIES }"
          class="text-md text-primary underline"
          @click="gtmEvent('click_cat_voir_plus')"
        >
          Voir plus
        </router-link>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed, onBeforeMount, onUnmounted, ref } from 'vue'
import DropdownListComponent from '@/vuejs/modules/shared/DropdownListComponent.vue'
import { useCategoryStore } from '@/vuejs/stores/category'
import { ProductPageList } from '@/vuejs/router/pages-list'
import { buildStandardGtmData, gtmMixinPushEvent } from '@/vuejs/services/gtm'
import { useUserStore } from '@/vuejs/stores/user'
import { useChannelStore } from '@/vuejs/stores/channel'

const categoryStore = useCategoryStore()
const userStore = useUserStore()
const channelStore = useChannelStore()

const currentChannel = channelStore.currentChannel

const windowWidth = ref<number>(null)

const categories = computed(() => {
  return windowWidth.value <= 1280
    ? categoryStore.categories
    : categoryStore.categories.slice(0, 7)
})

const gtmEvent = (eventName: string, additionalData = null) => {
  let data = buildStandardGtmData(userStore.user['@id'], currentChannel.name)
  data = additionalData ? { ...data, ...additionalData } : data
  gtmMixinPushEvent(eventName, data)
}

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

<style scoped></style>
