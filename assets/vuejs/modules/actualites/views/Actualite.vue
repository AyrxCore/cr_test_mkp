<template>
  <BaseTemplate :title="`${pageTitle} | Qantis - Marketplace`">
    <div
      v-if="currentExpertContent"
      class="xs:w-[100%] m-auto my-4 max-w-screen-2xl flex-1 px-5 sm:px-8"
    >
      <BreadcrumbSharedComponent
        :list-url="listUrl"
        :current-page="'Actualité'"
      />
      <div class="w-[100%] max-w-screen-2xl">
        <ContactUsButtonComponent />
      </div>
      <div
        class="m-auto my-2 flex w-[100%] max-w-screen-2xl flex-col lg:grid lg:grid-cols-2 lg:gap-4"
      >
        <!-- Bloc text actualité -->
        <div>
          <h3 class="primary mb-2 text-title-35">
            {{ currentExpertContent.articleTitle }}
          </h3>
          <span
            class="mr-2 w-max rounded-md px-2 py-1 text-white"
            :style="{ background: currentExpertContent.categoryColor }"
          >
            {{ currentExpertContent.categoryName }}
          </span>
          <span class="text-gray-500">
            {{ formattedDate }}
          </span>
          <div class="mt-5 h-[auto] rounded-lg">
            <p
              class="whitespace-pre-line text-gray-500"
              v-html="currentExpertContent.articleContent"
            />
          </div>
          <div
            v-if="
              currentExpertContent.ctaTxt !== '' &&
              currentExpertContent.ctaLink !== ''
            "
          >
            <a
              :href="currentExpertContent.ctaLink"
              target="_blank"
              class="button button-gradient mt-4 font-bold text-white"
            >
              <ArrowRigntIconComponent
                class="mt-1 mr-2 w-4 items-center"
                :stroke-color="'#FFFFFF'"
              />
              {{ currentExpertContent.ctaTxt }}
            </a>
          </div>
        </div>
        <!-- Fin Bloc text actualité -->

        <!-- Bloc image -->
        <div class="mt-[7rem] hidden h-[421px] rounded-lg bg-white md:flex">
          <img
            :src="currentExpertContent.article_img_desktop"
            alt="Picture"
            class="m-auto h-[inherit] items-center"
          />
        </div>
        <div class="mt-5 h-[421px] rounded-lg bg-white md:hidden">
          <img
            :src="currentExpertContent.article_img_mobile"
            alt="Picture"
            class="m-auto h-[inherit] items-center"
          />
        </div>
        <!-- Fin Bloc image -->
      </div>

      <!-- Bloc articles recommandés -->
      <div class="mt-10 justify-center">
        <h3 class="home-subtitle mb-5 text-primary">Articles recommandés</h3>
        <ContenusExpertComponent />
      </div>
      <!-- Fin Bloc articles recommandés -->
    </div>
    <div v-else class="mt-5 flex h-20 w-full items-center justify-center">
      <LoaderSharedComponent
        class="text-secondary"
        classes="loader-xl loader"
      />
    </div>
  </BaseTemplate>
</template>

<script lang="ts" setup>
import { format } from 'date-fns'
import { computed, onBeforeMount, ref, watch } from 'vue'
import { useRoute } from 'vue-router'

import ArrowRigntIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import ContenusExpertComponent from '@/vuejs/modules/home/component/ContenusExpertComponent.vue'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import { useExpertContentStore } from '@/vuejs/stores/expertContent'
import { ExpertContent } from '@/vuejs/types/ExpertContent'
import { NewsPageList } from '@/vuejs/router/pages-list'

const route = useRoute()
const expertContentStore = useExpertContentStore()
const currentExpertContent = ref<ExpertContent>()

const listUrl = ref([
  {
    name: 'Actualités',
    url: { name: NewsPageList.NEWS },
  },
])

const formattedDate = computed((): string => {
  return format(new Date(currentExpertContent.value.date), 'dd/MM/yyyy')
})

onBeforeMount(async () => {
  await expertContentStore.init()
})

const pageTitle = computed(() => {
  return currentExpertContent.value ? currentExpertContent.value.articleTitle : ''
})

watch(
  () => route.params.slug as string,
  async (slug: string) => {
    if (slug) {
      currentExpertContent.value = await expertContentStore.initActualitePage(
        slug,
      )
    }
  },
  { immediate: true },
)
</script>

<style scoped></style>
