<template>
  <BaseTemplate title="Liste des news | Qantis - MarketPlace">
    <div class="xs:w-[100%] m-auto my-4 max-w-screen-2xl flex-1 sm:px-8">
      <breadcrumb-shared-component :current-page="'Actualités'" />
      <div class="w-[100%] max-w-screen-2xl">
        <ContactUsButtonComponent />
        <h3 class="text-[35px] text-primary">Nos contenus experts</h3>
        <!-- Bloc liste des actus -->
        <div class="m-auto my-2 flex">
          <div class="w-3/4 pr-5">
            <div class="m-auto md:grid md:grid-cols-2 md:gap-4 lg:grid-cols-3">
              <div v-for="contenu in expertsContents" :key="contenu.id">
                <ActualiteComponentComponent :contenu="contenu" />
              </div>
            </div>
          </div>
          <div class="w-1/4">
            <h3 class="text-[25px] text-primary">Catégories</h3>
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
        </div>
        <!-- Fin bloc liste actu -->
      </div>
    </div>
  </BaseTemplate>
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import { onMounted, ref } from 'vue'
import ActualiteComponentComponent from '@/vuejs/modules/actualites/components/ActualiteComponent.vue'
import { useExpertContentStore } from '@/vuejs/stores/expertContent'
import { storeToRefs } from 'pinia'
import { ExpertContent } from '@/vuejs/types/ExpertContent'

const expertContentStore = useExpertContentStore()
const { getExpertsContentsCategories } = storeToRefs(expertContentStore)
const expertsContents = ref<Array<ExpertContent>>([])

onMounted(async () => {
  expertsContents.value = await expertContentStore.init()
})
</script>

<style scoped></style>
