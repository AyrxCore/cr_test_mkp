<template>
  <div v-if="hasStores" class="my-8 w-full">
    <div class="mx-auto w-[calc(100%-2rem)] md:w-[90%] lg:w-[85%] xl:w-[80%]">
      <h3
        class="mb-6 px-2 text-left text-xl font-bold text-primary md:px-0 md:text-center md:text-2xl"
      >
        Où trouver ce partenaire ?
      </h3>

      <div class="px-2 md:px-0">
        <MapComponent
          :poi-color="'#FF0000'"
          :enable-geolocation="true"
          :enable-controls="true"
          :enable-zoom="true"
          :height-class="'h-80 md:h-[450px] lg:h-[600px]'"
        >
          <template #markers>
            <LMarkerClusterGroup :show-coverage-on-hover="false">
              <LMarker
                v-for="store in stores"
                :key="store.id"
                :icon="createMarkerIcon('text-primary')"
                :lat-lng="getLatLng(store.latitude, store.longitude)"
              >
                <LPopup :options="getTooltipOptions(store.id)">
                  <StorePopupContent :store="store">
                    <template #logo>
                      <img
                        v-if="accordLogo"
                        :src="accordLogo"
                        :alt="store.name"
                        class="h-12 w-auto object-contain"
                      />
                    </template>
                    <template #link>
                      <RouterLink
                        v-if="props.accord?.seller?.id"
                        :to="{
                          name: ProductPageList.PRODUCTS,
                          query: { seller: props.accord.seller.externalId },
                        }"
                        target="_blank"
                        :class="[
                          'mx-auto max-h-7 w-full max-w-[90%] rounded-full bg-primary !px-1 !py-0.5 text-center !text-xs !text-white',
                          'hover:!scale-100 hover:!transform-none md:mx-0 md:w-auto md:max-w-32 md:!px-3 md:!py-1 md:!text-sm',
                        ]"
                        @click.stop
                      >
                        Voir l'offre
                      </RouterLink>
                    </template>
                  </StorePopupContent>
                </LPopup>
              </LMarker>
            </LMarkerClusterGroup>
          </template>
        </MapComponent>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, computed, onMounted, PropType } from 'vue'
import { LMarkerClusterGroup } from 'vue-leaflet-markercluster'
import { LMarker, LPopup } from '@vue-leaflet/vue-leaflet'

import { useMap } from '@/vuejs/modules/products/composables/useMap'
import { getLatLng } from '@/vuejs/modules/products/utils/map-utils'
import AccordHttpClient from '@/vuejs/services/httpclient/AccordHttpClient'
import { type StoreData } from '@/vuejs/types/Seller'
import { Product } from '@/vuejs/types/Product'
import { ProductPageList } from '@/vuejs/router/pages-list'

import MapComponent from '@/vuejs/modules/shared/map/MapComponent.vue'
import StorePopupContent from '@/vuejs/modules/products/components/map/StorePopupContent.vue'

const emit = defineEmits<{ (e: 'loaded', hasStores: boolean): void }>()

const props = defineProps({
  accord: {
    type: Object as PropType<Product>,
    required: false,
    default: null,
  },
})

const { createMarkerIcon, getTooltipOptions } = useMap()

const stores = ref<StoreData[]>([])
const accordLogo = ref<string>('')

const hasStores = computed<boolean>(() => {
  return stores.value && stores.value.length > 0
})

onMounted(async () => {
  if (!props.accord) {
    return
  }

  const accordId = props.accord.accordId
  if (!accordId) {
    console.warn('Aucun accord ID trouvé')
    return
  }

  try {
    const accordData =
      await AccordHttpClient.get().fetchAccordWithStores(accordId)

    if (accordData?.stores) {
      stores.value = accordData.stores
      accordLogo.value = accordData.logo || ''
    } else {
      stores.value = []
      accordLogo.value = ''
    }
  } catch (err) {
    console.error("Erreur lors du chargement des magasins de l'accord:", err)
    stores.value = []
  } finally {
    emit('loaded', stores.value.length > 0)
  }
})
</script>
