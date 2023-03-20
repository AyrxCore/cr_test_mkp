<template>
  <div class="bloc-content col-span-5 flex h-full flex-col">
    <h3
      class="text-title-35 mb-[1.563rem] font-bold leading-9 text-primary xl:w-3/4"
    >
      Vos conditions négociées
    </h3>
    <p
      class="mb-10 flex"
      v-html="properties.mentions_legales_conditions"
    />
    <div class="relative">
      <div
        v-if="images.length"
        class="partner-carousel relative"
      >
        <CarouselListSharedComponent
          class="mx-auto mb-10 items-center rounded-xl bg-white px-4 py-4 lg:h-[443px]"
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
          <SwiperSlide v-for="(image, key) in images" :key="key">
            <img :src="image" alt="Picture" class="items-center" />
          </SwiperSlide>
          </CarouselListSharedComponent>
      </div>
      <div v-else>
        <ul class="mx-7 flex list-disc flex-col">
          <li
            v-for="(condition, key) in textes"
            :key="key"
            class="mb-5"
          >
            {{ condition }}
          </li>
        </ul>
      </div>

      <div class="flex flex-col items-center justify-center">
        <ButtonComponent
          v-for="(button, key) in buttons"
          :key="key"
          class="button-gradient md:mr-5 mb-5 !whitespace-normal lg:whitespace-nowrap h-auto lg:h-12"
          @click="openInNewTab(button.url)"
        >
          <span class="w-full">{{ button.name }}</span>
        </ButtonComponent>
      </div>
    </div>

  </div>
</template>
<script lang="ts" setup>
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import { computed } from 'vue'
import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import { SwiperSlide } from 'swiper/vue'
import { openInNewTab } from '@/vuejs/services/utils'


const props = defineProps({
  mentionsLegales: {
    type: String,
    default: null
  },
  properties: {
    type: Object,
    default: null
  },
})

const images = computed(() => {
  if (props.properties.tableau_conditions_negocies) {
    return props.properties.tableau_conditions_negocies.split(';')
  }

  return []
})

const textes = computed(() => {
  const textes = [
    props.properties.text1_conditions_negocies,
    props.properties.text2_conditions_negocies,
    props.properties.text3_conditions_negocies,
  ]
  return textes.filter(function (el) {
    return el != null
  })
})

const buttons = computed(() => {
  const buttons = [
    {
      name: props.properties.cta1_text,
      url: props.properties.cta1_link,
    },
    {
      name: props.properties.cta2_text,
      url: props.properties.cta2_link,
    }
  ]

  return buttons.filter(function (el) {
    return el.url != null
  })
})
</script>

<style scoped></style>
