<template>
  <LMarkerClusterGroup :show-coverage-on-hover="false">
    <LMarker
      v-for="store in props.stores"
      :key="store.id"
      :icon="createMarkerIcon('text-primary')"
      :lat-lng="getLatLng(store)"
    >
      <LPopup v-if="store && store.id" :options="getTooltipOptions(store.id)">
        <div
          class="m-2 w-full cursor-auto overflow-hidden break-words md:m-4 md:w-64"
        >
          <div class="my-2 flex flex-col gap-1">
            <div class="flex justify-start">
              <img
                v-if="store.partnerLogo"
                :src="store.partnerLogo"
                :alt="store.name"
                class="h-12 w-auto object-contain"
              />
            </div>

            <div class="w-full">
              <strong
                class="block text-sm font-bold uppercase leading-tight text-slate-900"
              >
                {{ store.name }}
              </strong>
            </div>
          </div>
          <p
            class="mt-1 hyphens-auto break-words text-xs leading-snug text-slate-600"
          >
            {{ store.address }}
          </p>

          <div v-if="store.phone || store.upplerId" class="mt-2">
            <div
              class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between md:gap-1"
            >
              <a
                v-if="store.phone"
                :href="`tel:${store.phone}`"
                class="block text-left text-sm font-medium text-blue-500 md:text-xs"
                @click.stop
              >
                {{ store.phone }}
              </a>

              <RouterLink
                v-if="store.upplerId"
                :to="{
                  name: ProductPageList.PRODUCTS,
                  query: { company: store.upplerId },
                }"
                target="_blank"
                class="mx-auto max-h-7 w-full max-w-[90%] rounded-full bg-primary !px-1 !py-0.5 text-center !text-xs !text-white"
                :class="[
                  'hover:!scale-100 hover:!transform-none',
                  'md:mx-0 md:w-auto md:max-w-32 md:!px-3 md:!py-1 md:!text-sm',
                ]"
                @click.stop
              >
                Voir l'offre
              </RouterLink>
            </div>
          </div>
        </div>
      </LPopup>
    </LMarker>
  </LMarkerClusterGroup>
</template>

<script lang="ts" setup>
import { PropType } from 'vue'
import { LMarker, LPopup } from '@vue-leaflet/vue-leaflet'
import { LMarkerClusterGroup } from 'vue-leaflet-markercluster'

import { PartnerStore } from '@/vuejs/types/Seller'
import { getLatLng } from '@/vuejs/modules/products/utils/map-utils'
import { ProductPageList } from '@/vuejs/router/pages-list'
import { useMap } from '@/vuejs/modules/products/composables/useMap'

const { createMarkerIcon, getTooltipOptions } = useMap()

const props = defineProps({
  stores: {
    type: Array as PropType<PartnerStore[]>,
    required: true,
  },
})
</script>
