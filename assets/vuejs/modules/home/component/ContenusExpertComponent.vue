<template>
  <div class="relative mt-5 p-8">
    <CarouselListSharedComponent
        class="nav-mobile-only"
        :slides-per-view="1"
        :space-between="20"
        :show-nav="true"
        :breakpoints="{
        1280: {
          slidesPerView: 3,
          spaceBetween: 20,
        },
        640: {
          slidesPerView: 2,
          spaceBetween: 20,
        },
      }"
    >
      <SwiperSlide
          v-for="contenu in getExpertsContents"
          class="flex h-full items-center justify-center overflow-hidden rounded-lg"
      >
        <div class="grid lg:grid-cols-2">
          <div class="flex rounded-lg border-[#F3EDFE]">
            <img
                :src="contenu.mise_en_avant_homepage_img_desktop"
                alt="Picture"
                class="items-center sm:mx-auto"
            />
          </div>
          <div class="mt-5 text-left md:mt-0 md:px-6">
            <h3 class="text-[23px] font-bold text-primary">
              {{ contenu.articleTitle }}
            </h3>
            <p class="my-4">
              <a
                  href="#"
                  class="mr-2 mb-2 w-auto items-center rounded-md bg-purple-600 px-5 py-2.5 text-sm text-white"
                  :style="{'background': contenu.categoryColor}"
              >
                {{ contenu.categoryName }}
              </a>
            </p>
            <p class="text-lg">
              {{ contenu.articleTeaser }}
            </p>
            <div class="bottom-0">
              <a
                  href="" @click="goToArticle(contenu.slug)"
                  class="bottom-0 flex items-center text-sm font-medium text-primary underline"
              >
                Lire l'article
              </a>
            </div>
          </div>
        </div>
      </SwiperSlide>
    </CarouselListSharedComponent>
  </div>
</template>

<script lang="ts" setup>

import {SwiperSlide} from 'swiper/vue'
import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'

import {useExpertContentStore} from '@/vuejs/stores/expertContent'
import {storeToRefs} from 'pinia'
import router, {PageList} from '@/vuejs/router'

const expertContent = useExpertContentStore()
const {getExpertsContents} = storeToRefs(expertContent)

console.log('getExpertsContents')
console.log(getExpertsContents)
console.log('getExpertsContents !!')

const goToArticle = (slug) => {
  router.push({name: PageList.ACTUALITE, params: {slug: slug}})
}


</script>

<style scoped></style>
