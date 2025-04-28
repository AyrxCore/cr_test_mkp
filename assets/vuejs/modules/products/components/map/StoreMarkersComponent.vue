<template>
  <LMarker
    v-for="store in stores"
    :key="store.id"
    :lat-lng="getLatLng(store)"
    :icon="createMarkerIcon()"
  >
    <LPopup v-if="store && store.id" :options="tooltipOptions(store.id)">
      <div class="flex w-full justify-between">
        <strong
          class="text-sm font-bold leading-tight text-slate-900 md:text-base"
        >
          {{ store.name }}
        </strong>
      </div>
      <p
        class="mt-1 text-xs leading-snug text-slate-600 md:text-sm md:leading-normal"
      >
        {{ store.address }}
      </p>
      <p v-if="store.phone" class="mt-1 md:mt-2">
        <a
          :href="`tel:${store.phone}`"
          class="text-xs font-medium text-blue-500 md:text-sm"
          @click.stop
          >{{ formatPhoneNumber(store.phone) }}</a
        >
      </p>
    </LPopup>
  </LMarker>
</template>

<script lang="ts" setup>
import { PropType } from 'vue'
import type { DivIcon } from 'leaflet'
import { LMarker, LPopup } from '@vue-leaflet/vue-leaflet'
import { PartnerStore } from '@/vuejs/types/Seller'
import {
  formatPhoneNumber,
  getLatLng,
} from '@/vuejs/modules/products/utils/map-utils'

defineProps({
  stores: {
    type: Array as PropType<PartnerStore[]>,
    required: true,
  },
  isMobile: {
    type: Boolean,
    required: true,
  },
  createMarkerIcon: {
    type: Function as PropType<() => DivIcon>,
    required: true,
  },
  tooltipOptions: {
    type: Function as PropType<(storeId: string) => Record<string, unknown>>,
    required: true,
  },
})
</script>
