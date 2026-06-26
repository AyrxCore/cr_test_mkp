<template>
  <BaseTemplate title="Carte des partenaires">
    <div
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
        <h3 class="text-title-primary mb-4 mt-2 block lg:hidden">
          Les partenaires à proximité
        </h3>
        <MobileFiltersMapComponent
          v-if="categories.length > 0"
          :categories="categories"
          :selected-category="selectedCategoryId"
          :is-loading="isFilterLoading"
          @category-changed="handleCategoryChange"
        />
        <div class="my-2 flex flex-col justify-between lg:flex-row">
          <div
            class="categories-container relative hidden max-h-[600px] min-h-[200px] w-full max-w-[300px] overflow-y-auto lg:mr-6 lg:block xl:mr-8"
          >
            <div class="h-full w-full pl-2">
              <!-- Liste des catégories -->
              <MapFiltersComponent
                v-if="categories.length > 0"
                :categories="categories"
                :selected-category="selectedCategoryId"
                @category-changed="handleCategoryChange"
              />
              <div
                v-else-if="categories.length === 0 && isLoading"
                class="flex h-full items-center justify-center rounded-lg bg-white p-4 shadow-sm"
              >
                <LoaderSharedComponent />
              </div>
            </div>
          </div>

          <div class="flex w-full flex-grow flex-col">
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
                      v-for="store in lightStores"
                      :key="store.id"
                      :icon="markerIcon"
                      :lat-lng="getLatLng(store.latitude, store.longitude)"
                      @click="handleMarkerClick(store.id)"
                    >
                      <LPopup
                        :options="getTooltipOptions(store.id)"
                        :ref="(el) => setPopupRef(store.id, el)"
                        @open="handlePopupOpen(store.id)"
                        @close="handlePopupClose"
                      >
                        <div
                          v-if="
                            isLoadingStoreDetail && currentOpenedId === store.id
                          "
                          class="relative w-full"
                          style="width: 100%; height: 200px"
                        >
                          <div
                            class="absolute inset-0 flex items-center justify-center"
                          >
                            <p class="text-sm text-gray-700">
                              Chargement en cours…
                            </p>
                          </div>
                        </div>
                        <div
                          v-else-if="storeDetails.has(store.id)"
                          style="width: 100%"
                        >
                          <StorePopupContent
                            :store="storeDetails.get(store.id)!"
                            :active="currentOpenedId === store.id"
                          >
                            <template #logo>
                              <LogoCarousel
                                v-if="
                                  storeDetails.get(store.id)!.accordLogos
                                    ?.length
                                "
                                :logos="storeDetails.get(store.id)!.accordLogos"
                              />
                              <img
                                v-else-if="storeDetails.get(store.id)!.logo"
                                :src="storeDetails.get(store.id)!.logo"
                                :alt="storeDetails.get(store.id)!.name"
                                class="h-12 w-auto object-contain"
                              />
                            </template>
                          </StorePopupContent>
                        </div>
                      </LPopup>
                    </LMarker>
                  </LMarkerClusterGroup>
                </template>
              </MapComponent>
              <div
                v-if="isLoading || isFilterLoading"
                class="absolute inset-0 z-10 flex items-center justify-center bg-gray-900/70"
              >
                <div
                  class="flex items-center justify-center rounded-full bg-white p-4 shadow-lg"
                >
                  <LoaderSharedComponent class="h-8 w-8 text-primary" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </BaseTemplate>
</template>

<script lang="ts" setup>
import { onBeforeMount, ref, computed, nextTick } from 'vue'
import { LMarkerClusterGroup } from 'vue-leaflet-markercluster'
import { LMarker, LPopup } from '@vue-leaflet/vue-leaflet'

import { ProductPageList } from '@/vuejs/router/pages-list'
import { useMap } from '@/vuejs/modules/products/composables/useMap'
import SellerHttpClient from '@/vuejs/services/httpclient/SellerHttpClient'
import { StoreLightData, StoreData } from '@/vuejs/types/Seller'
import { Category } from '@/vuejs/types/Product/Category'
import { getLatLng } from '@/vuejs/modules/products/utils/map-utils'

import BaseTemplate from '@/vuejs/BaseTemplate.vue'
import BreadcrumbSharedComponent from '@/vuejs/modules/shared/BreadcrumbSharedComponent.vue'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import MapFiltersComponent from '@/vuejs/modules/map/components/MapFiltersComponent.vue'
import MobileFiltersMapComponent from '@/vuejs/modules/map/components/MobileFiltersMapComponent.vue'
import MapComponent from '@/vuejs/modules/shared/map/MapComponent.vue'
import LogoCarousel from '@/vuejs/modules/products/components/map/LogoCarousel.vue'
import StorePopupContent from '@/vuejs/modules/products/components/map/StorePopupContent.vue'

const { createMarkerIcon, getTooltipOptions } = useMap()

const isLoading = ref<boolean>(true)
const isFilterLoading = ref<boolean>(false)
const isLoadingStoreDetail = ref<boolean>(false)
const lightStores = ref<StoreLightData[]>([])
const storeDetails = ref<Map<string, StoreData>>(new Map())
const categories = ref<Category[]>([])
const selectedCategoryId = ref<string | null>(null)
const currentOpenedId = ref<string | null>(null)

let currentAbortController: AbortController | null = null

const popupRefs = new Map<string, any>()
const setPopupRef = (id: string, el: any) => {
  if (el) popupRefs.set(id, el)
}

const markerIcon = computed(() => createMarkerIcon('text-primary'))

const handleMarkerClick = async (storeId: string) => {
  currentOpenedId.value = storeId
  if (!storeDetails.value.has(storeId)) {
    await loadStoreDetail(storeId)
  }
  await nextTick()
  popupRefs.get(storeId)?.leafletObject?.update?.()
}

const handlePopupOpen = async (storeId: string) => {
  currentOpenedId.value = storeId
  if (!storeDetails.value.has(storeId)) {
    await loadStoreDetail(storeId)
  }
  await nextTick()
  popupRefs.get(storeId)?.leafletObject?.update?.()
}

const handlePopupClose = () => {
  currentOpenedId.value = null
}

const loadStoreDetail = async (storeId: string) => {
  if (storeDetails.value.has(storeId)) return

  isLoadingStoreDetail.value = true
  try {
    await forcePopupUpdate(storeId)

    const storeDetail = await SellerHttpClient.get().fetchStoreDetail(storeId)
    storeDetails.value.set(storeId, storeDetail)
    await forcePopupUpdate(storeId)
  } catch (error) {
    console.error(
      `❌ Erreur lors du chargement des détails du store ${storeId}:`,
      error,
    )
  } finally {
    isLoadingStoreDetail.value = false
  }
}

const forcePopupUpdate = async (storeId: string) => {
  await nextTick()
  popupRefs.get(storeId)?.leafletObject?.update?.()
}

const handleCategoryChange = async (categoryId: string | null) => {
  // Annuler la requête précédente si elle existe
  if (currentAbortController) {
    currentAbortController.abort()
  }

  // Créer un nouveau controller pour cette requête
  currentAbortController = new AbortController()
  const signal = currentAbortController.signal

  isFilterLoading.value = true

  try {
    if (categoryId === 'all' || categoryId === null) {
      selectedCategoryId.value = null
    } else {
      selectedCategoryId.value = categoryId
    }

    // Effacer seulement les détails des stores et la popup ouverte
    storeDetails.value.clear()
    currentOpenedId.value = null

    // Charger les nouvelles données sans effacer les catégories avant
    const mapData = await SellerHttpClient.get().fetchMapData(
      selectedCategoryId.value,
      signal,
    )

    // Mettre à jour seulement si la requête n'a pas été annulée.
    // Les catégories restent celles du chargement initial (sans filtre) pour pouvoir changer de filtre sans repasser par « Toutes ».
    if (!signal.aborted) {
      lightStores.value = mapData.stores
    }
  } catch (error) {
    // Ne pas traiter les erreurs d'annulation
    if (error.name !== 'AbortError') {
      console.error('Erreur lors du changement de catégorie:', error)
    }
  } finally {
    // Ne réinitialiser isFilterLoading que si cette requête n'a pas été annulée
    if (!signal.aborted) {
      isFilterLoading.value = false
    }
  }
}

const loadMapData = async (
  categoryId?: string | null,
  signal?: AbortSignal,
) => {
  try {
    const mapData = await SellerHttpClient.get().fetchMapData(
      categoryId,
      signal,
    )

    lightStores.value = mapData.stores
    categories.value = mapData.categories
  } catch (error) {
    // Ne pas traiter les erreurs d'annulation
    if (error.name !== 'AbortError') {
      console.error(
        '❌ Erreur lors du chargement des données de la map:',
        error,
      )
      lightStores.value = []
      categories.value = []
    }
  }
}

onBeforeMount(async () => {
  isLoading.value = true
  try {
    // Créer un controller pour le chargement initial
    const initialController = new AbortController()
    currentAbortController = initialController

    await loadMapData(null, initialController.signal)
  } catch (error) {
    if (error.name !== 'AbortError') {
      console.error('Erreur lors du chargement initial des données:', error)
    }
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
