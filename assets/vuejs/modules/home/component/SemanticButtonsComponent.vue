<template>
  <div
    v-if="semanticButtons.length > 0"
    class="max-w-screen-94 m-auto mt-4 md:px-0"
  >
    <div class="mt-10 sm:w-[45rem]">
      <h3 class="text-title-primary mb-3">
        {{ sectionTitle }}
      </h3>
    </div>
    <div class="list-semantic-buttons max-w-screen-94 m-auto">
      <div
        v-for="semanticButton in visibleButtons"
        :key="semanticButton.id"
        class="list-semantic-buttons-items hover:bg-primary cursor-pointer uppercase hover:text-white"
      >
        <RouterLink
          :to="{
            name: ProductPageList.PRODUCTS,
            query: { q: semanticButton.search },
          }"
          class="px-0.5 text-sm"
          @click="
            sendGtmEvent('semantics_cta_click', {
              link_text: $event.target.innerText,
              link_url: router.resolve({
                name: ProductPageList.PRODUCTS,
                query: { q: semanticButton.search },
              }).fullPath,
              origin_url: router.currentRoute.value.fullPath,
            })
          "
        >
          {{ semanticButton.label }}
        </RouterLink>
      </div>
    </div>
    <button
      v-if="semanticButtons.length > MAX_VISIBLE_BUTTONS"
      class="text-primary mt-4 text-sm underline"
      @click="isExpanded = !isExpanded"
    >
      {{ isExpanded == true ? 'Voir moins' : 'Voir plus' }}
    </button>
  </div>
</template>

<script lang="ts" setup>
import { computed, onBeforeMount, ref } from 'vue'
import { storeToRefs } from 'pinia'

import router from '@/vuejs/router'
import { ProductPageList } from '@/vuejs/router/pages-list'
import { useSemanticButtonsStore } from '@/vuejs/stores/semanticButtons'
import { sendGtmEvent } from '@/vuejs/services/gtm'

const MAX_VISIBLE_BUTTONS = 8

const semanticButtonsStore = useSemanticButtonsStore()
const { semanticButtonsSectionTitle, semanticButtons } = storeToRefs(
  useSemanticButtonsStore(),
)

const isExpanded = ref(false)

const visibleButtons = computed(() => {
  if (isExpanded.value) {
    return semanticButtons.value
  }
  return semanticButtons.value.slice(0, MAX_VISIBLE_BUTTONS - 1)
})

const sectionTitle = computed<string>(() => {
  return semanticButtonsSectionTitle.value
    ? semanticButtonsSectionTitle.value.sectionTitle
    : 'Vos catégories préférées'
})

onBeforeMount(async () => {
  const promises = []
  promises.push(semanticButtonsStore.init())
  await Promise.all(promises)
})
</script>

<style>
@reference '@/style/main.css';

.list-semantic-buttons {
  @apply flex flex-col overflow-auto rounded-lg text-left text-gray-700 sm:items-center xl:relative xl:mt-2 xl:h-auto xl:flex-row xl:justify-start xl:overflow-auto xl:bg-transparent xl:p-0 xl:text-center;
}

.list-semantic-buttons-items {
  @apply border-primary text-primary my-2 items-center border-2 border-solid bg-white px-2.5 py-1.5 text-center text-sm sm:w-1/2 sm:text-base xl:mx-4 xl:w-fit xl:justify-center xl:rounded xl:text-lg;
}
</style>
