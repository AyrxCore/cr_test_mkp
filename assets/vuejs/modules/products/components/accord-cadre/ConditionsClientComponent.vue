<template>
  <div class="text-center text-lg leading-5 text-white">
    <div class="condition-beneficiaire mt-4">
      <p v-html="properties.process_fat_client" />
    </div>

    <div class="mt-6 flex flex-col items-center">
      <ButtonComponent
        v-if="cta1.name && cta1.url"
        class="button-primary mx-auto mb-6 border-2 border-solid !border-white"
        @click="openingNewTab(cta1.url)"
      >
        <span>
          {{ cta1.name }}
        </span>
      </ButtonComponent>
      <a
        v-else-if="cta1.name && cta1.mailto"
        class="button button-primary mx-auto mb-6 border-2 border-solid !border-white"
        :href="cta1.mailto"
      >
        <span>
          {{ cta1.name }}
        </span>
      </a>
      <ButtonComponent
        v-if="cta2.name && cta2.url"
        class="button-primary mx-auto mb-6 border-2 border-solid !border-white"
        @click="openingNewTab(cta2.url)"
      >
        {{ cta2.name }}
      </ButtonComponent>
      <a
        v-else-if="cta2.name && cta2.mailto"
        class="button button-primary mx-auto mb-6 border-2 border-solid !border-white"
        :href="cta2.mailto"
      >
        <span>
          {{ cta2.name }}
        </span>
      </a>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { computed } from 'vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import { openInNewTab } from '@/vuejs/services/utils'
import { formatUrlWithChannelCode } from '@/vuejs/services/formatter'

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

const cta1 = computed(() => {
  return {
    name: props.properties.cta1_txt_fat_client,
    url: props.properties.cta1_link_fat_client,
    mailto: props.properties.cta1_mailto_fat_client,
  }
})

const cta2 = computed(() => {
  return {
    name: props.properties.cta2_txt_fat_client,
    url: props.properties.cta2_link_fat_client,
    mailto: props.properties.cta2_mailto_fat_client,
  }
})

const openingNewTab = (url: string) => {
  openInNewTab(formatUrlWithChannelCode(url))
}
</script>
