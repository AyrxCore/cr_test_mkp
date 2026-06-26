<template>
  <BaseTemplate :title="pageTitle">
    <LoadingComponent v-if="newsStore.isLoading" />
    <div
      v-else-if="newsData"
      class="xs:w-[100%] m-auto my-4 max-w-screen-2xl flex-1 px-5 sm:px-8"
    >
      <BreadcrumbSharedComponent
        :current-page="articleTitle || 'Actualité'"
        :list-url="listUrl"
        gtm-event-name="click_actualite_breadcrumbs"
      />
      <div
        class="m-auto my-2 flex w-[100%] max-w-screen-2xl flex-col lg:grid lg:grid-cols-2 lg:gap-4"
      >
        <!-- Bloc text actualité -->
        <div>
          <h3 class="text-title-primary mb-2">
            {{ articleTitle }}
          </h3>
          <span
            v-if="categoryName"
            :style="{ background: categoryColor || '#primary' }"
            class="mr-2 w-max rounded-md px-2 py-1 text-white"
          >
            {{ categoryName }}
          </span>
          <span v-if="formattedDate" class="text-gray-500">
            {{ formattedDate }}
          </span>
          <div class="mt-5 h-[auto] rounded-lg">
            <RichTextRenderer v-if="articleContent" :content="articleContent" />
          </div>
          <div v-if="ctaTxt && ctaLink">
            <a
              :href="ctaLink"
              class="button button-primary mt-4 font-bold text-white"
              target="_blank"
              @click="
                sendGtmEvent('cta_news_click', {
                  link_text: ctaTxt,
                  link_url: ctaLink,
                  origin_url: router.currentRoute.value.fullPath,
                })
              "
            >
              <ArrowRigntIconComponent
                class="mr-2 w-4 items-center"
                stroke="#FFFFFF"
              />
              {{ ctaTxt }}
            </a>
          </div>
        </div>
        <!-- Fin Bloc text actualité -->

        <!-- Bloc image -->
        <div
          v-if="articleImgDesktop"
          class="mt-[7rem] hidden h-[421px] rounded-lg bg-white md:flex"
        >
          <img
            :src="articleImgDesktop"
            alt="Picture"
            class="m-auto h-[inherit] items-center"
          />
        </div>
        <div
          v-if="articleImgMobile"
          class="mt-5 h-[421px] rounded-lg bg-white md:hidden"
        >
          <img
            :src="articleImgMobile"
            alt="Picture"
            class="m-auto h-[inherit] items-center"
          />
        </div>
        <!-- Fin Bloc image -->
      </div>
    </div>
    <div
      v-else
      class="xs:w-[100%] m-auto my-4 flex max-w-screen-2xl flex-col items-center bg-orange-200 px-5 py-5 text-orange-500 sm:px-8"
    >
      <span class="flex">
        <WarningIconComponent class="w-5 fill-orange-500 stroke-orange-500" />
        Aucune actualité n'a été trouvée
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
import { computed, ref } from 'vue'
import { useRoute } from 'vue-router'
import { format } from 'date-fns'

import router from '@/vuejs/router'
import { NewsPageList } from '@/vuejs/router/pages-list'
import { sendGtmEvent } from '@/vuejs/services/gtm'
import { useNewsStore } from '@/vuejs/stores/news'
import type { News } from '@/vuejs/types/News'

import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import LoadingComponent from '@/vuejs/modules/shared/LoadingComponent.vue'
import ArrowRigntIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import WarningIconComponent from '@/vuejs/modules/shared/icon/WarningIconComponent.vue'
import RichTextRenderer from '@/vuejs/modules/shared/RichTextRenderer.vue'

const route = useRoute()
const newsStore = useNewsStore()

const newsData = computed((): News | null => {
  const slug = route.params.slug as string
  if (!slug) return null
  return newsStore.getNewsBySlug(slug) ?? null
})

const listUrl = ref([
  {
    name: 'Actualités',
    url: { name: NewsPageList.NEWS },
  },
])

const formattedDate = computed((): string => {
  if (newsData.value?.firstPublishedAt) {
    return format(new Date(newsData.value.firstPublishedAt), 'dd/MM/yyyy')
  }
  return ''
})

const pageTitle = computed((): string => {
  return articleTitle.value || 'Actualité'
})

const articleTitle = computed((): string => newsData.value?.articleTitle ?? '')

const categoryName = computed((): string => newsData.value?.categoryName ?? '')

const categoryColor = computed(
  (): string => newsData.value?.categoryColor ?? '',
)

const articleContent = computed(
  (): string => newsData.value?.articleContent ?? '',
)

const articleImgDesktop = computed(
  (): string => newsData.value?.articleImgDesktop?.filename ?? '',
)

const articleImgMobile = computed(
  (): string => newsData.value?.articleImgMobile?.filename ?? '',
)

const ctaTxt = computed((): string => newsData.value?.ctaTxt ?? '')

const ctaLink = computed((): string => newsData.value?.ctaLink ?? '')
</script>

<style scoped></style>
