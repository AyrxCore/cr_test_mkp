<template>
  <BaseTemplate title="Carte des partenaires">
    <div
      v-if="isLoading"
      class="my-20 flex h-20 w-full flex-col items-center justify-center text-primary"
    >
      <LoadingComponent />
    </div>
    <div
      v-else
      class="mx-auto flex h-full w-full flex-grow flex-col px-4 py-4 pb-16 md:px-8 lg:px-10 xl:px-12 xl:pb-4"
    >
      <BreadcrumbSharedComponent
        :list-url="[
          {
            name: 'Liste des partenaires',
            url: { name: ProductPageList.SELLERS },
          },
        ]"
        current-page="Carte des partenaires"
      />
      <div class="mt-4 flex w-full flex-col">
        <!-- Titre mobile au-dessus des filtres -->
        <h3 class="text-title-primary mb-4 mt-2 block lg:hidden">
          Les partenaires à proximité
        </h3>

        <MobileFiltersMapComponent
          v-if="categories.length > 0"
          :categories="categories"
          :selected-category="selectedCategoryString"
          @category-changed="handleCategoryChange"
        />
        <div class="my-2 flex flex-col justify-between lg:flex-row">
          <div
            class="categories-container hidden h-[600px] w-full max-w-[300px] overflow-y-auto pl-2 lg:mr-6 lg:block xl:mr-8"
          >
            <MapFiltersComponent
              v-if="categories.length > 0"
              :categories="categories"
              :selected-category="selectedCategoryString"
              @category-changed="handleCategoryChange"
            />
          </div>

          <div class="flex w-full flex-grow flex-col">
            <!-- Titre desktop/tablette caché en mobile -->
            <h3 class="text-title-primary mb-4 mt-2 hidden lg:block xl:mt-0">
              Les partenaires à proximité
            </h3>

            <div class="relative w-full">
              <MapComponent
                :poi-color="'#FF0000'"
                :enable-geolocation="true"
                :enable-controls="true"
                :enable-zoom="true"
                height-class="h-[45vh] xl:h-[500px]"
              >
                <template #markers>
                  <LMarkerClusterGroup :show-coverage-on-hover="false">
                    <LMarker
                      v-for="store in stores"
                      :key="store.id"
                      :icon="markerIcon"
                      :lat-lng="getLatLng(store.latitude, store.longitude)"
                    >
                      <LPopup :options="getTooltipOptions(store.id)">
                        <StorePopupContent :store="store" />
                      </LPopup>
                    </LMarker>
                  </LMarkerClusterGroup>
                </template>
              </MapComponent>

              <div
                v-if="isFilterLoading"
                class="absolute left-0 right-0 top-0 z-10 flex h-[45vh] items-center justify-center rounded-lg bg-gray-500/50 xl:h-[500px]"
              >
                <LoadingComponent />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </BaseTemplate>
</template>

<script lang="ts" setup>
import { onBeforeMount, ref, computed } from 'vue'
import { LMarkerClusterGroup } from 'vue-leaflet-markercluster'
import { LMarker, LPopup } from '@vue-leaflet/vue-leaflet'

import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import LoadingComponent from '@/vuejs/modules/shared/LoadingComponent.vue'
import MapFiltersComponent from '@/vuejs/modules/map/components/MapFiltersComponent.vue'
import MobileFiltersMapComponent from '@/vuejs/modules/map/components/MobileFiltersMapComponent.vue'
import MapComponent from '@/vuejs/modules/shared/map/MapComponent.vue'
import StorePopupContent from '@/vuejs/modules/products/components/map/StorePopupContent.vue'

import { useMap } from '@/vuejs/modules/products/composables/useMap'
import { getLatLng } from '@/vuejs/modules/products/utils/map-utils'
import { ProductPageList } from '@/vuejs/router/pages-list'
import { StoreData, MapCategoryData } from '@/vuejs/types/Seller'
import SellerHttpClient from '@/vuejs/services/httpclient/SellerHttpClient'

const { createMarkerIcon, getTooltipOptions } = useMap()

const isLoading = ref<boolean>(true)
const isFilterLoading = ref<boolean>(false)
const stores = ref<StoreData[]>([])
const categories = ref<MapCategoryData[]>([])
const selectedCategoryId = ref<number | null>(null)

const markerIcon = computed(() => createMarkerIcon('text-primary'))
const selectedCategoryString = computed(
  () => selectedCategoryId.value?.toString() || null,
)

const handleCategoryChange = async (categoryId: string | null) => {
  isFilterLoading.value = true

  try {
    if (categoryId === 'all' || categoryId === null) {
      selectedCategoryId.value = null
    } else {
      selectedCategoryId.value = parseInt(categoryId)
    }

    await loadMapData(selectedCategoryId.value)
  } finally {
    isFilterLoading.value = false
  }
}

const loadMapData = async (categoryId?: number | null) => {
  try {
    const mapData = await SellerHttpClient.get().fetchMapData(
      categoryId !== null ? categoryId : undefined,
    )

    stores.value = mapData.stores
    categories.value = mapData.categories
  } catch (error) {
    console.error('❌ Erreur lors du chargement des données de la map:', error)
    stores.value = []
    categories.value = []
  }
}

onBeforeMount(async () => {
  isLoading.value = true
  try {
    await loadMapData()
  } catch (error) {
    console.error('Erreur lors du chargement initial des données:', error)
  } finally {
    isLoading.value = false
  }
})
</script>

<style scoped>
.categories-container {
  direction: rtl;
}

.categories-container > * {
  direction: ltr;
}

.categories-container::-webkit-scrollbar {
  width: 8px;
}

.categories-container::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 8px;
}

.categories-container::-webkit-scrollbar-thumb {
  background: var(--primary-color);
  border-radius: 8px;
}

.categories-container::-webkit-scrollbar-thumb:hover {
  background: var(--primary-color);
}
</style>
