<template>
  <div
    class="mb-12 flex h-full flex-col items-center px-5 md:px-8 lg:flex-row-reverse lg:justify-end"
  >
    <div class="lg:ml-16">
      <h3 class="text-title-primary mb-6 text-center leading-9 lg:text-left">
        Conditions négociées
      </h3>
      <div>
        <div class="mb-6">
          <ul class="mx-7 flex list-disc flex-col text-lg">
            <li v-for="(condition, key) in textes" :key="key" class="mb-5">
              {{ condition }}
            </li>
          </ul>
        </div>
        <div class="flex flex-col items-center justify-center lg:flex-row">
          <ButtonComponent
            v-for="(button, key) in buttons"
            :key="key"
            class="mb-6 h-auto w-full md:mr-5 md:w-auto lg:h-12 lg:whitespace-nowrap"
            :class="
              button.style === 'primary'
                ? 'button-primary'
                : 'button-primary-outline'
            "
            @click="clickOnCta(button.url, button.style)"
          >
            <span class="w-full">{{ button.name }}</span>
          </ButtonComponent>
        </div>
      </div>
    </div>
    <div class="partner-carousel relative w-full lg:w-1/3">
      <CarouselListSharedComponent
        class="mx-auto mb-10 items-center rounded-xl bg-white px-4 py-4"
        :slides-per-view="1"
        :space-between="10"
        :pagination="true"
        :breakpoints="{
          640: {
            slidesPerView: 1,
            spaceBetween: 10,
          },
        }"
        @click-left="
          sendGaEvent('click_fat_tableau_conditions_left', {
            productName: props.accordName,
          })
        "
        @click-right="
          sendGaEvent('click_fat_tableau_conditions_right', {
            productName: props.accordName,
          })
        "
      >
        <SwiperSlide
          v-for="(image, key) in images"
          :key="key"
          class="flex items-center justify-center"
        >
          <img :src="image" alt="Picture" />
        </SwiperSlide>
      </CarouselListSharedComponent>
    </div>
  </div>
</template>
<script lang="ts" setup>
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import { computed } from 'vue'
import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import { SwiperSlide } from 'swiper/vue'
import { checkIsAbsoluteUrl, openInNewTab } from '@/vuejs/services/utils'
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'
import { useProductStore } from '@/vuejs/stores/product'

const productStore = useProductStore()

const props = defineProps({
  mentionsLegales: {
    type: String,
    default: null,
  },
  properties: {
    type: Object,
    default: null,
  },
  accordName: {
    type: String,
    default: null,
  },
})

const images = computed(() => {
  if (props.properties.tableau_conditions_negocies) {
    return props.properties.tableau_conditions_negocies.split(';')
  }

  return []
})

const textes = computed(() => {
  if (props.properties.text1_conditions_negocies) {
    return props.properties.text1_conditions_negocies.split(';')
  }

  return []
})

const buttons = computed(() => {
  const buttons = [
    {
      name: props.properties.cta1_text,
      url: props.properties.cta1_link,
      style: 'primary',
    },
    {
      name: props.properties.cta2_text,
      url: props.properties.cta2_link,
      style: 'secondary',
    },
  ]

  return buttons.filter(function (el) {
    return el.url != null
  })
})

const clickOnCta = (buttonUrl: string, buttonStyle: string) => {
  if (checkIsAbsoluteUrl(buttonUrl)) {
    openInNewTab(buttonUrl)
  } else {
    productStore.downloadPdfFile(buttonUrl)
  }

  const eventName =
    buttonStyle === 'primary'
      ? 'click_fat_conditions_cta1'
      : 'click_fat_conditions_cta2'
  sendGaEvent(eventName, {
    productName: props.properties.fat_marque,
  })
}
</script>

<style scoped></style>
