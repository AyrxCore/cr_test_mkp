<template>
  <div v-if="hasStores" class="my-8 w-full">
    <div class="mx-auto w-[calc(100%-2rem)] md:w-[90%] lg:w-[85%] xl:w-[80%]">
      <h3
        class="mb-6 px-2 text-left text-xl font-bold md:px-0 md:text-center md:text-2xl"
        :style="{ color: channelPrimaryColor }"
      >
        Où trouver ce partenaire ?
      </h3>

      <div class="px-2 md:px-0">
        <MapComponent
          :poi-color="'#FF0000'"
          :enable-geolocation="true"
          :enable-controls="true"
          :enable-zoom="true"
        >
          <template #markers>
            <StoreMarkersComponent
              :stores="partner?.partnerStores || []"
              :is-mobile="isMobile"
            />
          </template>
        </MapComponent>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, computed, onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import { useChannelStore } from '@/vuejs/stores/channel'
import SellerHttpClient from '@/vuejs/services/httpclient/SellerHttpClient'
import { Partner } from '@/vuejs/types/Seller'

import { useMap } from '@/vuejs/modules/products/composables/useMap'

import MapComponent from '@/vuejs/modules/shared/map/MapComponent.vue'
import StoreMarkersComponent from '@/vuejs/modules/products/components/map/StoreMarkersComponent.vue'

const { isMobile } = useMap()

const props = defineProps({
  sellerId: {
    type: Number,
    required: false,
    default: null,
  },
  primaryColor: {
    type: String,
    required: false,
    default: undefined,
  },
})

const { channelPrimaryColor } = storeToRefs(useChannelStore())

const isLoading = ref<boolean>(false)
const partner = ref<Partner | null>(null)

partner.value = { partnerStores: [] } as Partner

const hasStores = computed<boolean>(() => {
  return partner.value?.partnerStores && partner.value.partnerStores.length > 0
})

const loadPartnerStores = async (sellerId: number) => {
  if (!sellerId) {
    return
  }

  isLoading.value = true

  try {
    const response = await SellerHttpClient.get().fetchPartnerByUpplerId(
      sellerId.toString(),
    )

    if (response) {
      partner.value = response as Partner

      if (!partner.value.partnerStores) {
        partner.value.partnerStores = []
      }
    } else {
      partner.value = { partnerStores: [] } as Partner
    }
  } catch (err) {
    console.error('Erreur lors du chargement des magasins:', err)
    partner.value = { partnerStores: [] } as Partner
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  if (props.sellerId) {
    loadPartnerStores(props.sellerId)
  }
})
</script>
