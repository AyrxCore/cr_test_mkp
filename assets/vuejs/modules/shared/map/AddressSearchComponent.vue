<template>
  <div
    class="relative w-full md:w-auto"
    v-click-outside="() => (displayResults = false)"
  >
    <div
      class="flex w-full rounded border border-gray-300 bg-white shadow-sm md:w-96"
    >
      <input
        v-model="searchQuery"
        type="text"
        class="input w-full rounded-md !pl-6 !pr-0 text-sm !ring-transparent"
        placeholder="Rechercher une adresse"
        @focus="results.length > 0 && (displayResults = true)"
        @input="handleSearch"
      />
      <div
        :class="{ 'cursor-pointer': selectedAddress }"
        class="flex items-center px-5"
        @click="selectedAddress && clearSearch()"
      >
        <LoaderSharedComponent v-if="isLoading" />
        <SearchIconComponent v-else-if="!selectedAddress" />
        <CloseIconComponent v-else title="Effacer la recherche" />
      </div>
    </div>

    <div
      v-if="displayResults"
      class="mt-2 max-h-60 w-full overflow-y-auto rounded-md border border-gray-300 bg-white shadow-sm md:absolute md:top-full md:z-50 md:mt-1 md:w-96"
    >
      <div v-if="!results.length" class="p-2 text-sm text-slate-500">
        Aucun résultat
      </div>
      <AddressSearchResultComponent
        v-for="result in results"
        :key="result.place_id"
        :result="result"
        @click="handleResultClick"
      />
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, watch } from 'vue'

import {
  getDisplayedName,
  selectedAddress,
  leafletMap,
} from '@/vuejs/modules/products/composables/useMap'
import {
  AddressSearchResult,
  searchAddress,
} from '@/vuejs/services/searchAddressapi'

import CloseIconComponent from '@/vuejs/modules/shared/icon/CloseIconComponent.vue'
import SearchIconComponent from '@/vuejs/modules/shared/icon/SearchIconComponent.vue'
import AddressSearchResultComponent from '@/vuejs/modules/shared/map/AddressSearchResultComponent.vue'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'

const searchQuery = ref<string>('')
const results = ref<AddressSearchResult[]>([])
const isLoading = ref<boolean>(false)
const displayResults = ref<boolean>(false)

let searchTimeout: number | null = null

// S'assurer que leafletMap est disponible avant de permettre la recherche
const isMapReady = ref<boolean>(false)

// Surveiller quand la carte devient disponible
watch(
  () => leafletMap.value,
  (newMap) => {
    isMapReady.value = !!newMap
  },
  { immediate: true },
)
const handleSearch = async () => {
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }
  searchTimeout = window.setTimeout(async () => {
    if (!searchQuery.value || !isMapReady.value) {
      return
    }
    try {
      isLoading.value = true
      const response = await searchAddress(searchQuery.value, [
        'countrycodes=fr',
        'limit=10',
        'addressdetails=1',
        'layer=address',
      ])
      results.value = response.filter(
        (result: AddressSearchResult) =>
          ![
            'county',
            'suburb',
            'municipality',
            'city_district',
            'state_district',
            'state',
          ].includes(result.addresstype),
      )
      displayResults.value = true
    } catch (error) {
      console.error("Erreur lors de la recherche d'adresse:", error)
      results.value = []
    } finally {
      isLoading.value = false
    }
  }, 500)
}

const handleResultClick = (result: AddressSearchResult): void => {
  if (!isMapReady.value || !leafletMap.value) {
    console.warn('Carte non disponible pour la navigation')
    return
  }

  leafletMap.value.setView([parseFloat(result.lat), parseFloat(result.lon)], 15)
  searchQuery.value = getDisplayedName(result)
  displayResults.value = false
  selectedAddress.value = result
}

const clearSearch = (): void => {
  searchQuery.value = ''
  results.value = []
  displayResults.value = false
  selectedAddress.value = null
}
</script>
