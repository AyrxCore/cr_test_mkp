<template>
  <div class="relative mt-5 p-8">
    <CarouselListSharedComponent
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
      :show-nav="true"
      :slides-per-view="1"
      :space-between="20"
      class="nav-mobile-only swiper-nav-outside"
    >
      <SwiperSlide
        v-for="contenu in props.contents"
        :key="contenu?.id"
        class="!flex h-full items-center justify-center overflow-hidden rounded-lg"
      >
        <div
          class="mx-auto flex h-[500px] w-[392px] flex-col justify-start rounded-md bg-white px-6 py-4"
        >
          <!-- Bloc image -->
          <div class="mx-auto flex h-[191px] justify-center rounded-lg px-1">
            <RouterLink
              :to="{
                name: PageList.NEWS_ITEM,
                params: { slug: contenu?.slug },
              }"
            >
              <img
                :src="contenu?.mise_en_avant_homepage_img_desktop"
                alt="Picture"
                class="w-full items-center sm:mx-auto"
              />
            </RouterLink>
          </div>
          <!-- Fin bloc image -->

          <!-- Bloc nom et description -->
          <div class="mt-6 flex w-full flex-col justify-start">
            <RouterLink
              :to="{
                name: PageList.NEWS_ITEM,
                params: { slug: contenu?.slug },
              }"
            >
              <h3
                class="truncate-custom truncate-custom-2 mb-5 text-[23px] font-bold text-primary"
              >
                {{ contenu?.articleTitle }}
              </h3>
            </RouterLink>
            <div class="h-[100px]">
              <p class="truncate-custom truncate-custom-3 text-lg">
                {{ contenu?.articleTeaser }}
              </p>
            </div>
          </div>
          <div class="bottom-0">
            <RouterLink
              :to="{
                name: PageList.NEWS_ITEM,
                params: { slug: contenu?.slug },
              }"
              class="button border-2 border-primary text-sm font-medium !text-primary shadow-none hover:scale-105 hover:!border-primary hover:!bg-white hover:!shadow-inner-darker focus:!bg-white"
            >
              En savoir plus
            </RouterLink>
          </div>
          <!-- Fin bloc nom et description -->
        </div>
      </SwiperSlide>
    </CarouselListSharedComponent>
  </div>
</template>

<script lang="ts" setup>
import { PropType } from 'vue'
import { SwiperSlide } from 'swiper/vue'
import { PageList } from '@/vuejs/router'
import { ExpertContent } from '@/vuejs/types/ExpertContent'
import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'

const props = defineProps({
  contents: {
    required: true,
    type: Array as PropType<ExpertContent[]>,
  },
})
</script>
