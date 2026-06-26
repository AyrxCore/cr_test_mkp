<template>
  <div>
    <div
      v-if="
        bannerItems.length > 0 &&
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
            v-for="(item, index) in bannerItems"
            :key="index"
            class="!flex h-[303px] items-center justify-center overflow-hidden rounded-lg bg-white xl:h-full"
          >
            <RouterLink
              v-if="item.slug"
              :to="{
                name: NewsPageList.NEWS_ITEM,
                params: { slug: item.slug },
              }"
              class="truncate-custom truncate-custom-2 text-primary"
              @click="
                sendGtmEvent('homepage_banner_click', {
                  link_url: `/news/${item.slug}`,
                  origin_url: router.currentRoute.value.fullPath,
                })
              "
            >
              <img
                :src="item.imageMobile"
                :alt="item.alt || 'Banner'"
                class="flex w-full items-center md:hidden"
              />
              <img
                :src="item.imageDesktop"
                :alt="item.alt || 'Banner'"
                class="mx-auto hidden items-center md:flex"
              />
            </RouterLink>
            <div v-else>
              <img
                :src="item.imageMobile"
                :alt="item.alt || 'Banner'"
                class="flex w-full items-center md:hidden"
              />
              <img
                :src="item.imageDesktop"
                :alt="item.alt || 'Banner'"
                class="mx-auto hidden items-center md:flex"
              />
            </div>
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
import { computed } from 'vue'
import { storeToRefs } from 'pinia'
import { RouterLink } from 'vue-router'
import { SwiperSlide } from 'swiper/vue'

import router from '@/vuejs/router'
import { NewsPageList } from '@/vuejs/router/pages-list'
import { useChannelStore } from '@/vuejs/stores/channel'
import { useNewsStore } from '@/vuejs/stores/news'
import { OPTIONAL_FRONT_BLOCKS } from '@/vuejs/services/const'
import { sendGtmEvent } from '@/vuejs/services/gtm'

import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'

const channelStore = useChannelStore()
const newsStore = useNewsStore()
const { channel } = storeToRefs(channelStore)

interface BannerItem {
  imageMobile: string
  imageDesktop: string
  slug?: string
  alt?: string
}

const bannerItems = computed<BannerItem[]>(() => {
  return newsStore.bannerNews.map((news) => ({
    imageMobile: news.bannerImgMobile?.filename ?? '',
    imageDesktop: news.bannerImgDesktop?.filename ?? '',
    slug: news.slug,
    alt: news.articleTitle ?? '',
  }))
})
</script>

<style lang="postcss" scoped>
.news-carousel :deep(.swiper-pagination) {
  transform: translateY(calc(100% + 14px));
}
</style>
