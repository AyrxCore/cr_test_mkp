<template>
  <div class="flex h-full flex-col rounded-md px-6 pb-8 pt-5">
    <div class="flex justify-end">
      <div
        class="rounded-sm bg-secondary p-1 text-sm"
        :style="{
          color: betterTextColor('secondary'),
        }"
      >
        Accord-cadre
      </div>
    </div>
    <div class="mx-auto">
      <img :src="properties.logo_partenaire" alt="Image produit" />
    </div>
    <div class="mb-8 text-center text-sm font-normal uppercase sm:text-lg">
      {{ accord.name }}
    </div>

    <RouterLink
      :to="{
        name: ProductPageList.ACCORD_CADRE,
        params: { slug: accord.slug },
      }"
      class="button button-primary flex justify-center"
      :style="{
        color: betterTextColor('primary'),
      }"
      @click="
        gtmEvent('click_slider_home_fat_cta', {
          partenaire_name: accord.seller.name,
          partenaire_id: accord.seller.id,
        })
      "
    >
      Profiter de l'accord-cadre
    </RouterLink>
  </div>
</template>

<script lang="ts" setup>
import { computed, PropType } from 'vue'

import { ProductPageList } from '@/vuejs/router/pages-list'
import { Product } from '@/vuejs/types/Product'

import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import { betterTextColor } from '@/vuejs/services/utils'
import { buildStandardGtmData, gtmMixinPushEvent } from '@/vuejs/services/gtm'
import { useUserStore } from '@/vuejs/stores/user'
import { useChannelStore } from '@/vuejs/stores/channel'

const userStore = useUserStore()
const channelStore = useChannelStore()

const currentChannel = channelStore.currentChannel

const props = defineProps({
  accord: {
    required: true,
    type: Object as PropType<Product>,
  },
})

const properties = computed(() => {
  return props.accord.properties
})

const gtmEvent = (eventName: string, additionalData = null) => {
  let data = buildStandardGtmData(userStore.user['@id'], currentChannel.name)
  data = additionalData ? { ...data, ...additionalData } : data
  gtmMixinPushEvent(eventName, data)
}
</script>

<style scoped></style>
