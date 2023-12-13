<template>
  <div v-if="listButtons.length">
    <div class="mt-10 flex flex-col">
      <h3 class="home-title text-primary">En savoir plus sur ce partenaire</h3>
    </div>
    <div
      class="mt-5 rounded-xl bg-white p-4 text-gray-500 md:p-7.5 lg:mt-2 lg:w-auto"
    >
      <div class="flex flex-col justify-around lg:flex-row">
        <ButtonComponent
          v-for="(button, key) in listButtons"
          :key="key"
          class="button-white button-white-secondary mb-5 lg:mb-0"
          @click="openInNewTab(button.url)"
        >
          <ArrowRightIconComponent />
          {{ button.text }}
        </ButtonComponent>
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { openInNewTab } from '@/vuejs/services/utils'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import { computed } from 'vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'

const props = defineProps({
  properties: {
    type: Object,
    default: null,
  },
})

const listButtons = computed(() => {
  const list = [
    {
      text: props.properties.cta_en_savoir_plus_text_1,
      url: props.properties.cta_en_savoir_plus_link_1,
    },
    {
      text: props.properties.cta_en_savoir_plus_text_2,
      url: props.properties.cta_en_savoir_plus_link_2,
    },
    {
      text: props.properties.cta_en_savoir_plus_text_3,
      url: props.properties.cta_en_savoir_plus_link_3,
    },
  ]

  return list.filter(function (el) {
    return el.text != null
  })
})
</script>

<style scoped></style>
