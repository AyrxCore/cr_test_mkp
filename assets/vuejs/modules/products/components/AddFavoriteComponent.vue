<template>
  <div v-click-outside="onOutsideBlock" class="flex items-center justify-end">
    <button class="flex text-gray-500" @click="showTooltip = true">
      <HeartIconComponent class="... stroke-gray-500" />
    </button>
    <div v-if="showTooltip" class="tooltip">
      <form
        v-if="showList"
        class="flex flex-col space-y-2"
        @submit.prevent="addProductToFavorite"
      >
        <span
          v-if="showErrorNotSelected"
          class="bg-orange-200 p-3 text-base text-gray-600"
        >
          Veuillez sélectionner une liste de favori</span
        >
        <div
          v-if="favorites"
          class="c-scrollbar flex max-h-[250px] flex-col overflow-y-auto px-1"
        >
          <label
            v-for="favoriteItem in favorites"
            :key="favoriteItem.id"
            class="text-base text-gray-600"
            :class="{ 'bg-blue-200': favorite.name === favoriteItem.name }"
          >
            <input
              v-model="selectedFavorite[favoriteItem.id]"
              type="checkbox"
            />
            {{ favoriteItem.name }}
          </label>
        </div>

        <ButtonComponent
          class="button-secondary flex !h-6 justify-end !px-0 !py-2"
          :is-loading="addProductToFavoriteLoading"
          >Ajouter à la liste
        </ButtonComponent>
      </form>
      <ButtonComponent
        v-if="showAddButton"
        type="button"
        class="button-white-secondary mt-2 flex !h-6 justify-end !px-2 !py-2 !text-secondary hover:!bg-white focus:!bg-white"
        @click.stop="openFavoriteForm"
        >+ Créer une nouvelle liste
      </ButtonComponent>
      <div v-show="showFormFavorite" class="mt-2">
        <h3 class="mb-2 text-primary">Ajouter une nouvelle liste</h3>
        <FavoriteForm :favorite="favorite" :form-col="true" />
        <div class="flex justify-between">
          <button
            class="button button-white-secondary !h-6 !px-2 !py-2 !text-secondary hover:!bg-white focus:!bg-white"
            type="button"
            @click="closeFavoriteForm"
          >
            Annuler
          </button>
          <button
            class="button button-secondary !h-6 !px-2 !py-2"
            type="button"
            @click="onSubmitFavorite"
          >
            Créer
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<script lang="ts" setup>
import HeartIconComponent from '@/vuejs/modules/shared/icon/HeartIconComponent.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import { useFavoriteStore } from '@/vuejs/stores/favorite'
import { storeToRefs } from 'pinia'
import { ref } from 'vue'
import { Favorite } from '@/vuejs/types/Favorite'
import FavoriteForm from '@/vuejs/modules/account/components/favorite/FavoriteForm.vue'

const props = defineProps({
  productId: {
    type: Number,
    required: true,
  },
  variantId: {
    required: true,
    type: Number,
  },
  productName: {
    required: true,
    type: String,
  },
})

const favorite = ref<Favorite>({
  name: null,
  public: false,
})

const favoriteStore = useFavoriteStore()
const { favorites } = storeToRefs(favoriteStore)
const showTooltip = ref<boolean>(false)
const showList = ref<boolean>(true)
const selectedFavorite = ref([])
const addProductToFavoriteLoading = ref<boolean>(false)
const showFormFavorite = ref<boolean>(false)
const isLoading = ref<boolean>(false)
const showAddButton = ref<boolean>(true)
const showErrorNotSelected = ref<boolean>(false)
const onOutsideBlock = () => {
  showTooltip.value = false
  selectedFavorite.value = []
  showErrorNotSelected.value = false
}

const openFavoriteForm = () => {
  showFormFavorite.value = true
  showAddButton.value = false
  showTooltip.value = true
  showList.value = false
}

const closeFavoriteForm = () => {
  showFormFavorite.value = false
  showAddButton.value = true
  showList.value = true
  showTooltip.value = true
}

const onSubmitFavorite = async () => {
  isLoading.value = true
  try {
    await favoriteStore.create(favorite.value)
    await favoriteStore.fetchFavorites()
    closeFavoriteForm()
  } catch (error) {}

  isLoading.value = false
}

const addProductToFavorite = async () => {
  const selectedFavorites = Object.keys(selectedFavorite.value)

  if (selectedFavorites.length > 0) {
    showErrorNotSelected.value = false
    addProductToFavoriteLoading.value = true
    try {
      await favoriteStore.addItem({
        selectedFavorites: Object.keys(selectedFavorite.value),
        productId: props.productId,
        productName: props.productName,
        variantId: props.variantId,
      })
      onOutsideBlock()
      selectedFavorite.value = []
    } catch (error) {}

    addProductToFavoriteLoading.value = false
  } else {
    showErrorNotSelected.value = true
  }
}
</script>

<style scoped>
.tooltip {
  @apply absolute left-0 right-0 top-7 z-10 m-auto  flex min-h-[150px] w-[300px] flex-col items-center justify-center rounded border bg-white p-2.5;
}

.c-scrollbar::-webkit-scrollbar {
  width: 12px;
}

.c-scrollbar::-webkit-scrollbar-track {
  @apply bg-white;
}

.c-scrollbar::-webkit-scrollbar-thumb {
  @apply bg-primary;
}

.c-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #9f9f9f;
}
</style>
