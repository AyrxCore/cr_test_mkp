<template>
  <div v-if="listMiseEnAvant.length > 0" class="partner-carousel relative">
    <CarouselListSharedComponent
      class="mx-auto mt-10 items-center rounded-xl bg-white px-4 py-4 lg:h-[443px]"
      :slides-per-view="1"
      :space-between="10"
      :pagination="true"
      :breakpoints="{
        640: {
          slidesPerView: 1,
          spaceBetween: 10,
        },
      }"
    >
      <SwiperSlide v-for="(miseEnAvant, key) in listMiseEnAvant" :key="key">
        <div class="mt-5 flex flex-col lg:flex-row">
          <div
            class="flex h-[217px!important] justify-center rounded-lg px-2 lg:ml-10 lg:h-[374px!important] lg:w-1/2 lg:items-center lg:border lg:px-0 lg:py-8.5"
          >
            <img
              v-if="miseEnAvant.image"
              :src="miseEnAvant.image"
              alt="Picture"
              class="object-cover"
            />
            <iframe
              v-if="miseEnAvant.video"
              width="100%"
              height="100%"
              :src="miseEnAvant.video"
            >
            </iframe>
          </div>
          <div
            class="mt-5 flex flex-col rounded-lg bg-white p-5 text-lg text-gray-500 lg:mt-0 lg:w-1/2 lg:pr-12"
          >
            <h3
              class="mb-[1.563rem] text-title-35 font-bold leading-9 text-primary lg:w-3/4"
            >
              {{ miseEnAvant.title }}
            </h3>
            <p
              class="mb-5 text-sm md:text-base xl:text-lg"
              v-html="miseEnAvant.text"
            />

            <ButtonComponent
              v-if="miseEnAvant.buttonUrl"
              class="button-secondary mt-6 lg:w-1/2"
              @click="openInNewTab(miseEnAvant.buttonUrl)"
            >
              {{ miseEnAvant.buttonName }}
            </ButtonComponent>
            <a
              v-else-if="miseEnAvant.buttonMailto"
              class="button button-secondary mt-6 lg:w-1/2"
              :href="miseEnAvant.buttonMailto"
            >
              <span>
                {{ miseEnAvant.buttonName }}
              </span>
            </a>
          </div>
        </div>
      </SwiperSlide>
    </CarouselListSharedComponent>
  </div>
</template>
<script lang="ts" setup>
import { openInNewTab } from '@/vuejs/services/utils'
import { SwiperSlide } from 'swiper/vue'
import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import { computed } from 'vue'

const props = defineProps({
  properties: {
    type: Object,
    default: null,
  },
})

const listMiseEnAvant = computed(() => {
  const list = []
  for (let i = 1; i < 4; i++) {
    list.push({
      title: props.properties['mises_en_avant_' + i + '_titre'],
      image: props.properties['mises_en_avant_' + i + '_img'],
      video: props.properties['mises_en_avant_' + i + '_video'],
      text: props.properties['mises_en_avant_' + i + '_txt'],
      buttonName: props.properties['mises_en_avant_' + i + '_cta_txt'],
      buttonUrl: props.properties['mises_en_avant_' + i + '_cta_link'],
      buttonMailto: props.properties['mises_en_avant_' + i + '_cta_mailto'],
    })
  }
  return list.filter(function (el) {
    return (el.image != null || el.video) && el.text != null
  })
})
</script>

<style scoped></style>
