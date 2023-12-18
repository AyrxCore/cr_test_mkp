<template>
  <div
    v-if="properties.img_en_savoir_plus && properties.txt_en_savoir_plus"
    class="my-8 bg-white p-6"
  >
    <div class="flex flex-col items-center justify-center lg:flex-row">
      <div
        class="flex max-w-screen-md justify-center rounded-lg px-2 lg:ml-10 lg:items-center lg:border lg:px-0"
      >
        <img
          :src="properties.img_en_savoir_plus"
          alt="Picture"
          class="object-cover"
        />
      </div>
      <div
        class="mt-5 flex flex-col rounded-lg bg-white p-5 text-lg lg:ml-6 lg:mt-0 lg:w-1/2 lg:pr-12"
      >
        <h3 class="mb-6 text-3xl font-bold text-primary">
          En savoir plus sur ce partenaire
        </h3>

        <p class="mb-5" v-html="properties.txt_en_savoir_plus" />

        <a
          v-for="(cta, key) in allCta"
          :key="key"
          target="_blank"
          :href="cta.link"
          class="underline"
          @click="
            sendGtmEvent(cta.eventName, {
              product_name: props.accordName,
            })
          "
          >{{ cta.text }}</a
        >
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { computed } from 'vue'
import { sendGtmEvent } from '@/vuejs/services/gtm'

const props = defineProps({
  properties: {
    type: Object,
    default: null,
  },
  accordName: {
    type: String,
    default: null,
  },
})

const allCta = computed(() => {
  const data = []

  for (let i = 1; i <= 3; i++) {
    const textKey = `mises_en_avant_${i}_cta_txt`
    const linkKey = `mises_en_avant_${i}_cta_link`
    const gtmEventName = `click_savoir_plus_cta${i}`

    if (props.properties[textKey] && props.properties[linkKey]) {
      data.push({
        text: props.properties[textKey],
        link: props.properties[linkKey],
        eventName: gtmEventName,
      })
    }
  }

  return data
})
</script>

<style scoped></style>
