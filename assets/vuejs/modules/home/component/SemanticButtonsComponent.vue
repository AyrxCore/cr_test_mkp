<template>
  <div
    v-if="semanticButtons.length > 0"
    class="m-auto mt-4 max-w-screen-94 md:px-0"
  >
    <div class="mt-10 sm:w-[45rem]">
      <h3 class="text-title-primary mb-3">
        {{ sectionTitle }}
      </h3>
    </div>
    <div class="list-semantic-buttons m-auto max-w-screen-94">
      <div
        v-for="semanticButton in semanticButtons"
        :key="semanticButton.id"
        class="list-semantic-buttons-items cursor-pointer uppercase hover:bg-primary hover:text-white"
      >
        <RouterLink
          :to="{
            name: ProductPageList.PRODUCTS,
            query: { q: semanticButton.search },
          }"
          class="px-0.5 text-sm"
        >
          {{ semanticButton.label }}
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed, onBeforeMount } from 'vue'
import { storeToRefs } from 'pinia'

import { ProductPageList } from '@/vuejs/router/pages-list'
import { useSemanticButtonsStore } from '@/vuejs/stores/semanticButtons'

const semanticButtonsStore = useSemanticButtonsStore()
const { semanticButtonsSectionTitle, semanticButtons } = storeToRefs(
  useSemanticButtonsStore(),
)

const sectionTitle = computed<string>(() => {
  return semanticButtonsSectionTitle.value
    ? semanticButtonsSectionTitle.value.sectionTitle
    : 'Titre générique si rien de renseigné'
})

onBeforeMount(async () => {
  const promises = []
  promises.push(semanticButtonsStore.init())
  await Promise.all(promises)
})
</script>

<style lang="scss">
.list-semantic-buttons {
  @apply flex flex-col
  overflow-auto
  rounded-lg
  text-left
  text-gray-700
  sm:items-center
  xl:relative
  xl:mt-2 xl:h-auto xl:flex-row xl:justify-start
  xl:overflow-auto xl:bg-transparent xl:p-0 xl:text-center;
}

.list-semantic-buttons-items {
  @apply my-2 items-center border-2
  border-solid border-primary bg-white
  px-2.5 py-1.5 text-center
  text-sm text-primary
  sm:w-1/2 sm:text-base
  xl:mx-4 xl:w-fit xl:justify-center
  xl:rounded xl:text-lg;
}
</style>
