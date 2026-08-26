<template>
  <div>
    <div
      v-if="
        expertContents.length > 0 &&
        channelStore.isAllowedToShow(
          OPTIONAL_FRONT_BLOCKS.BANNER_SLIDER_HOMEPAGE,
        )
      "
    >
      <div class="news-carousel relative mt-2">
        <CarouselListSharedComponent
          :autoplay="{
            delay: 3000,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
          }"
          :breakpoints="{
            640: {
              slidesPerView: 1,
              spaceBetween: 20,
            },
          }"
          :slides-per-view="1"
          :space-between="20"
          :pagination="true"
          class="swiper-nav-outside"
          loop
        >
          <SwiperSlide
            v-for="content in expertContents"
            :key="content.id"
            class="flex! h-[303px] items-center justify-center overflow-hidden rounded-lg bg-white xl:h-full"
          >
            <RouterLink
              :to="{
                name: NewsPageList.NEWS_ITEM,
                params: { slug: content.slug },
              }"
              class="truncate-custom truncate-custom-2 text-primary"
              @click="
                sendGtmEvent('homepage_banner_click', {
                  link_url: router.resolve({
                    name: NewsPageList.NEWS_ITEM,
                    params: { slug: content.slug },
                  }).fullPath,
                  origin_url: router.currentRoute.value.fullPath,
                })
              "
            >
              <img
                :src="content.slider_img_mobile"
                alt="Picture"
                class="flex w-full items-center md:hidden"
              />
              <img
                :src="content.slider_img_desktop"
                alt="Picture"
                class="mx-auto hidden items-center md:flex"
              />
            </RouterLink>
          </SwiperSlide>
        </CarouselListSharedComponent>
      </div>
    </div>
    <div
      v-else-if="channel.options['BANNER_HOMEPAGE']"
      :style="{ backgroundImage: `url(${channel.options['BANNER_HOMEPAGE']})` }"
      class="h-[160px] bg-cover bg-center md:h-[200px]"
    >
      <h3
        class="flex h-full w-full items-center justify-center bg-white/50 text-center text-xl font-bold sm:text-4xl"
      >
        {{ channel.options['BANNER_TITLE_HOMEPAGE'] }}
      </h3>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { storeToRefs } from 'pinia'
import { SwiperSlide } from 'swiper/vue'

import router from '@/vuejs/router'
import { NewsPageList } from '@/vuejs/router/pages-list'
import { useExpertContentStore } from '@/vuejs/stores/expertContent'
import { useChannelStore } from '@/vuejs/stores/channel'
import { OPTIONAL_FRONT_BLOCKS } from '@/vuejs/services/const'
import { sendGtmEvent } from '@/vuejs/services/gtm'

import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'

const expertContent = useExpertContentStore()
const channelStore = useChannelStore()

const { expertContents } = storeToRefs(expertContent)
const { channel } = storeToRefs(channelStore)
</script>

<style scoped>
.news-carousel :deep(.swiper-pagination) {
  transform: translateY(calc(100% + 14px));
}
</style>
