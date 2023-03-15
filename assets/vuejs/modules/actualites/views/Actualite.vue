<template>
  <BaseTemplate title="Qantis - MarketPlace">
    <div class="xs:w-[100%] m-auto my-4 max-w-screen-2xl flex-1 px-5 sm:px-8" v-if="currentExpertContent">
      <breadcrumb-shared-component
          :list-url="listUrl"
          :current-page="'Actualité'"
      />
      <div class="w-[100%] max-w-screen-2xl">
        <ContactUsButtonComponent/>
      </div>
      <div
          class="m-auto my-2 flex w-[100%] max-w-screen-2xl flex-col lg:grid lg:grid-cols-2 lg:gap-4"
      >
        <!-- Bloc text actualité -->
        <div>
          <h3 class="primary text-title-35 mb-2">
            {{ currentExpertContent.articleTitle }}
          </h3>
          <span
              class="mr-2 w-max rounded-md px-2 py-1 text-white text-white"
              :style="{'background': currentExpertContent.categoryColor}"
          >{{ currentExpertContent.categoryName }}</span
          >
          <span class="text-gray-500">{{ moment(String(currentExpertContent.date)).format('MM/DD/YYYY') }}</span>
          <div class="mt-5 h-[auto] rounded-lg">
            <p class="whitespace-pre-line text-gray-500">
              {{ currentExpertContent.articleContent }}
            </p>
          </div>
          <div v-if="currentExpertContent.ctaTxt !== '' && currentExpertContent.ctaLink !== ''">
            <a
                :href="'//' + currentExpertContent.ctaLink"
                target="_blank"
                class="default-button-gradient mt-4 inline-flex justify-center px-3.5 py-3 text-center font-bold text-white"
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
        <div class="mt-[7rem] h-[421px] rounded-lg bg-white hidden md:flex">
          <img
              :src="currentExpertContent.article_img_desktop"
              alt="Picture"
              class="m-auto h-[inherit] items-center"
          />
        </div>
        <div class="mt-[7rem] h-[421px] rounded-lg bg-white md:hidden">
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
        <h3 class="primary home-subtitle mb-5">Articles recommandés</h3>
        <ContenusExpertComponent :contenus="contenusExpert"/>
      </div>
      <!-- Fin Bloc articles recommandés -->
    </div>
  </BaseTemplate>
</template>
<script lang="ts" setup>
import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import {computed, ref, watch} from 'vue'
import ContactUsButtonComponent from '@/vuejs/modules/shared/ContactUsButtonComponent.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import ArrowRigntIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import {contenusExpert} from '@/vuejs/modules/actualites'
import ContenusExpertComponent from '@/vuejs/modules/home/component/ContenusExpertComponent.vue'
import {useExpertContentStore} from '@/vuejs/stores/expertContent'
import {useRoute} from 'vue-router'
import {ExpertContent} from '@/vuejs/types/ExpertContent'
import moment from 'moment'

const route = useRoute()
const expertContentStore = useExpertContentStore()
const currentExpertContent = ref<ExpertContent>()

const listUrl = ref([
  {
    name: 'Actualités',
    url: '/app/actualites',
  },
])

watch(
    () => route.params.slug as string,
    async (slug: string) => {
      if (slug) {
        console.log(moment(String('2023-02-15')).format('MM/DD/YYYY'))
        currentExpertContent.value = await expertContentStore.initActualitePage(slug)
      }

    },
    {immediate: true},
)
</script>

<style scoped></style>
