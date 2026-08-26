<template>
  <BaseTemplate title="Liste des news">
    <div class="m-auto my-4 w-full max-w-screen-2xl px-5 sm:px-8">
      <breadcrumb-shared-component current-page="Actualités" />
      <div class="mt-O mt-10">
        <h3 class="text-title-primary mb-5">Nos contenus experts</h3>
        <!-- Bloc liste des actus -->
        <div class="m-auto my-2 flex w-full flex-col-reverse lg:flex-row">
          <div class="w-full lg:w-4/5 lg:pr-5">
            <LoadingComponent v-if="isLoading" />
            <div
              v-else-if="expertContents && expertContents.length > 0"
              class="m-auto flex flex-col md:grid md:grid-cols-2 md:gap-4 lg:grid-cols-3"
            >
              <div v-for="content in expertContents" :key="content.id">
                <ActualiteComponent :content="content" />
              </div>
            </div>
            <div
              v-else
              class="m-auto my-4 flex max-w-screen-2xl flex-col items-center bg-orange-200 px-5 py-5 text-orange-500"
            >
              <span class="flex"> Aucun contenu expert n'a été trouvé </span>
            </div>
          </div>
          <div
            v-if="getExpertsContentsCategories.length"
            class="my-5 w-full lg:my-0 lg:w-1/5"
          >
            <h3 class="hidden text-[25px] text-primary lg:block">Catégories</h3>
            <DropdownListComponent>
              <template #button-label> Catégories</template>
              <template #title></template>
              <template #content>
                <div class="list-categories h-[225px]! flex-col">
                  <p
                    v-for="category in getExpertsContentsCategories"
                    :key="category.id"
                    class="mb-3 w-max rounded-md px-2 py-1 text-white"
                    :class="category.color"
                    :style="{ background: category.color }"
                  >
                    {{ category.name }}
                  </p>
                </div>
              </template>
            </DropdownListComponent>
          </div>
        </div>
        <!-- Fin bloc liste actu -->
      </div>
    </div>
  </BaseTemplate>
</template>
<script lang="ts" setup>
import { onMounted, ref } from 'vue'
import { storeToRefs } from 'pinia'

import { useExpertContentStore } from '@/vuejs/stores/expertContent'

import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import ActualiteComponent from '@/vuejs/modules/actualites/components/ActualiteComponent.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import DropdownListComponent from '../../shared/DropdownListComponent.vue'
import LoadingComponent from '@/vuejs/modules/shared/LoadingComponent.vue'

const expertContentStore = useExpertContentStore()
const { getExpertsContentsCategories, expertContents } =
  storeToRefs(expertContentStore)
const isLoading = ref<boolean>(true)

onMounted(async () => {
  try {
    await expertContentStore.init()
  } catch (_error) {
    // Ignored: error intentionally ignored
  } finally {
    isLoading.value = false
  }
})
</script>

<style scoped></style>
