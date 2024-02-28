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
          <template v-for="(button, key) in buttons" :key="key">
            <ButtonDownloadFileWithLogo
              :event-name="
                button.style === 'primary'
                  ? 'click_fat_conditions_cta1'
                  : 'click_fat_conditions_cta2'
              "
              :event-params="{
                product_name: props.properties.fat_marque,
              }"
              :classes="
                'mb-6 h-auto w-full md:mr-5 md:w-auto lg:h-12 lg:whitespace-nowrap' +
                  button.style ===
                'primary'
                  ? 'button-primary'
                  : 'button-primary-outline'
              "
              :url="formatUrlWithChannelCode(button.url)"
              :name="button.name"
            />
          </template>
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
            product_name: props.accordName,
          })
        "
        @click-right="
          sendGaEvent('click_fat_tableau_conditions_right', {
            product_name: props.accordName,
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
import { computed } from 'vue'
import { SwiperSlide } from 'swiper/vue'
import CarouselListSharedComponent from '@/vuejs/modules/shared/CarouselListSharedComponent.vue'
import ButtonDownloadFileWithLogo from '@/vuejs/modules/products/components/accord-cadre/ButtonDownloadFileWithLogo.vue'
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'
import { formatUrlWithChannelCode } from '@/vuejs/services/formatter'

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
</script>

<style scoped></style>
