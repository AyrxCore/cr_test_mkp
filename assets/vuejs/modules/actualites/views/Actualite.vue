<template>
  <BaseTemplate :title="pageTitle">
    <LoadingComponent v-if="isLoading" />
    <div
      v-else-if="currentExpertContent"
      class="xs:w-[100%] m-auto my-4 max-w-screen-2xl flex-1 px-5 sm:px-8"
    >
      <BreadcrumbSharedComponent
        :current-page="currentExpertContent.articleTitle"
        :list-url="listUrl"
        gtm-event-name="click_actualite_breadcrumbs"
      />
      <div
        class="m-auto my-2 flex w-[100%] max-w-screen-2xl flex-col lg:grid lg:grid-cols-2 lg:gap-4"
      >
        <!-- Bloc text actualité -->
        <div>
          <h3 class="text-title-primary mb-2">
            {{ currentExpertContent.articleTitle }}
          </h3>
          <span
            :style="{ background: currentExpertContent.categoryColor }"
            class="mr-2 w-max rounded-md px-2 py-1 text-white"
          >
            {{ currentExpertContent.categoryName }}
          </span>
          <span class="text-gray-500">
            {{ formattedDate }}
          </span>
          <div class="mt-5 h-[auto] rounded-lg">
            <p
              class="whitespace-pre-line [&_ol]:mb-3 [&_ol]:list-decimal [&_ol]:pl-5 [&_ul]:mb-3 [&_ul]:list-disc [&_ul]:pl-5"
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
              class="button button-primary mt-4 font-bold text-white"
              target="_blank"
              @click="
                sendGtmEvent('cta_news_click', {
                  link_text: currentExpertContent.ctaTxt,
                  link_url: currentExpertContent.ctaLink,
                  origin_url: router.currentRoute.value.fullPath,
                })
              "
            >
              <ArrowRigntIconComponent
                class="mr-2 w-4 items-center"
                stroke="#FFFFFF"
              />
              {{ currentExpertContent.ctaTxt }}
            </a>
          </div>
        </div>
        <!-- Fin Bloc text actualité -->

        <!-- Bloc image -->
        <div
          class="mt-[7rem] hidden h-[421px] overflow-hidden rounded-lg bg-white md:flex"
        >
          <img
            :src="currentExpertContent.article_img_desktop"
            alt="Picture"
            class="h-full w-full object-contain"
          />
        </div>
        <div class="mt-5 h-[421px] overflow-hidden rounded-lg bg-white md:hidden">
          <img
            :src="currentExpertContent.article_img_mobile"
            alt="Picture"
            class="h-full w-full object-contain"
          />
        </div>
        <!-- Fin Bloc image -->
      </div>

      <!-- Bloc articles recommandés -->
      <div v-if="contents.length > 0" class="mt-10 justify-center">
        <h3 class="text-title-primary mb-5">Articles recommandés</h3>
        <ExpertContentsComponent :contents="contents" />
      </div>
      <!-- Fin Bloc articles recommandés -->
    </div>
    <div
      v-else
      class="xs:w-[100%] m-auto my-4 flex max-w-screen-2xl flex-col items-center bg-orange-200 px-5 py-5 text-orange-500 sm:px-8"
    >
      <span class="flex">
        <WarningIconComponent class="w-5 fill-orange-500 stroke-orange-500" />
        Aucune news n'a été trouvée
      </span>

      <RouterLink
        :to="{ name: NewsPageList.NEWS }"
        class="mt-5 flex items-center"
      >
        Retour à la liste
      </RouterLink>
    </div>
  </BaseTemplate>
</template>

<script lang="ts" setup>
import { computed, ref, watch } from 'vue'
import { format } from 'date-fns'
import { useRoute } from 'vue-router'

import router from '@/vuejs/router'
import { NewsPageList } from '@/vuejs/router/pages-list'
import { useExpertContentStore } from '@/vuejs/stores/expertContent'
import { sendGtmEvent } from '@/vuejs/services/gtm'
import { ExpertContent } from '@/vuejs/types/ExpertContent'

import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import ExpertContentsComponent from '@/vuejs/modules/home/component/ExpertContentsComponent.vue'
import LoadingComponent from '@/vuejs/modules/shared/LoadingComponent.vue'
import WarningIconComponent from '@/vuejs/modules/shared/icon/WarningIconComponent.vue'
import ArrowRigntIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'

const route = useRoute()
const expertContent = useExpertContentStore()
const currentExpertContent = ref<ExpertContent>()
const isLoading = ref<boolean>(false)

const listUrl = ref([
  {
    name: 'Actualités',
    url: { name: NewsPageList.NEWS },
  },
])

const formattedDate = computed((): string => {
  return format(new Date(currentExpertContent.value.date), 'dd/MM/yyyy')
})

const pageTitle = computed(() => {
  return currentExpertContent.value
    ? currentExpertContent.value.articleTitle
    : ''
})

const contents = computed(() => {
  return expertContent.expertContents.filter(
    (c) =>
      c.categoryName === currentExpertContent.value?.categoryName &&
      c.slug !== currentExpertContent.value?.slug,
  )
})

watch(
  () => route.params.slug as string,
  async (slug: string) => {
    isLoading.value = true
    try {
      if (slug) {
        currentExpertContent.value = await expertContent.initExpertContent(slug)
      }
    } catch (_error) {
      // Ignored: error intentionally ignored
    } finally {
      isLoading.value = false
    }
  },
  { immediate: true },
)
</script>
