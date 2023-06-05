<template>
  <div v-click-outside="onOutsideBlock" class="flex items-center justify-end">
    <button class="flex text-gray-500" @click="onOpenFavorite">
      <HeartIconComponent
        class="... h-[40px] w-[40px] stroke-gray-500 lg:h-auto lg:w-auto"
        :class="{
          'fill-secondary !stroke-secondary': favoritesSelected.length > 0,
        }"
        :stroke-color="'#000000'"
      />
    </button>

    <div v-if="showTooltip" class="modal-overlay !absolute">
      <div class="z-[9] mx-3 rounded p-3 md:p-0 lg:mx-0">
        <div v-if="showTooltip" class="flex w-full">
          <div class="tooltip">
            <form
              v-if="showList"
              class="flex w-full flex-col space-y-2"
              @submit.prevent="addProductToFavorite"
            >
              <h3 class="font-bold text-primary">
                Ajouter à une liste de produits préférés
              </h3>
              <hr />
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
                <div v-for="favoriteItem in favorites" :key="favoriteItem.id">
                  <label class="my-1 flex items-center text-base text-gray-600">
                    <input
                      v-model="selectedFavorite[favoriteItem.id]"
                      type="checkbox"
                      class="cursor-pointer appearance-none rounded border border-gray-400 text-secondary checked:bg-secondary focus:ring-secondary"
                      :checked="isInArray(favoriteItem.id)"
                      :disabled="isInArray(favoriteItem.id)"
                      :class="{
                        'cursor-not-allowed opacity-25': isInArray(
                          favoriteItem.id,
                        ),
                      }"
                    />
                    <span class="ml-2">{{ favoriteItem.name }}</span>
                    <span
                      v-if="isInArray(favoriteItem.id)"
                      class="ml-2 flex cursor-pointer items-center justify-end text-secondary underline"
                      title="Supprimer de la liste"
                      @click="onRemoveItem(favoriteItem.id)"
                    >
                      <LoaderSharedComponent v-if="isDelFavoriteLoading" />
                      <TrashIconComponent
                        v-else
                        class="w-[20px] stroke-secondary"
                      />
                    </span>
                  </label>
                </div>
              </div>
              <div class="flex px-1">
                <label class="text-base text-gray-600">
                  <input
                    v-model="selectedNewFavorite"
                    type="checkbox"
                    :checked="newFavorite !== null || newFavorite !== ''"
                  />
                </label>
                <input
                  v-model.trim="newFavorite"
                  class="ml-1 w-full rounded py-0"
                  type="text"
                  placeholder="Ajouter une nouvelle liste"
                />
              </div>

              <div class="!mt-5 flex justify-end">
                <ButtonComponent
                  class="button-gradient flex !h-10 justify-end !py-2"
                  :disabled="
                    (selectedNewFavorite &&
                      (newFavorite === null || newFavorite === '')) ||
                    isDelFavoriteLoading
                  "
                  :is-loading="addProductToFavoriteLoading"
                  >Ajouter
                </ButtonComponent>
                <ButtonComponent
                  type="button"
                  class="button-white-secondary ml-2 flex !h-10 justify-end !py-2 !text-secondary hover:!bg-white focus:!bg-white"
                  @click="onOutsideBlock"
                  >Annuler
                </ButtonComponent>
              </div>
            </form>
          </div>
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
import TrashIconComponent from '@/vuejs/modules/shared/icon/TrashIconComponent.vue'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import { notifySuccess } from '@/vuejs/services/utils'

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
  favoritesSelected: {
    type: Array,
    default: null,
  },
})

const favoriteStore = useFavoriteStore()
const { favorites } = storeToRefs(favoriteStore)
const showTooltip = ref<boolean>(false)
const showList = ref<boolean>(true)
const selectedFavorite = ref([])
const selectedNewFavorite = ref(null)
const newFavorite = ref(null)
const addProductToFavoriteLoading = ref<boolean>(false)
const isLoading = ref<boolean>(false)
const isDelFavoriteLoading = ref<boolean>(false)
const showErrorNotSelected = ref<boolean>(false)

const emit = defineEmits(['openFavorite'])

const onOpenFavorite = () => {
  showTooltip.value = true
  emit('openFavorite', { showTooltip: showTooltip.value })
}
const onOutsideBlock = () => {
  showTooltip.value = false
  selectedFavorite.value = []
  showErrorNotSelected.value = false
  selectedNewFavorite.value = false
  newFavorite.value = null
  emit('openFavorite', { showTooltip: showTooltip.value })
}

const isInArray = (favoriteId) => {
  const selected = props.favoritesSelected.filter(function (el) {
    return el.id === favoriteId
  })

  return selected.length > 0
}

const addProductToFavorite = async () => {
  if (
    selectedNewFavorite.value &&
    (newFavorite.value === null || newFavorite.value === '')
  ) {
    return false
  }
  isLoading.value = true
  const selectedFavorites = Object.keys(selectedFavorite.value)

  if (selectedNewFavorite.value || selectedFavorites.length > 0) {
    showErrorNotSelected.value = false
    addProductToFavoriteLoading.value = true
    try {
      if (selectedNewFavorite.value) {
        const favorite = await favoriteStore.create({
          name: newFavorite.value,
          public: false,
        })
        const favoriteId = favorite.id
        selectedFavorites.push(favoriteId)
      }
      await favoriteStore.addItem({
        selectedFavorites,
        productId: props.productId,
        productName: props.productName,
        variantId: props.variantId,
      })
      selectedFavorites.forEach((favoriteId) => {
        props.favoritesSelected.push({ id: favoriteId })
      })
      onOutsideBlock()
      await favoriteStore.fetchFavorites()
      selectedFavorite.value = []
    } catch (error) {}

    addProductToFavoriteLoading.value = false
  } else {
    showErrorNotSelected.value = true
  }
  isLoading.value = false
}

const onRemoveItem = async (favoriteId) => {
  isDelFavoriteLoading.value = true
  try {
    await favoriteStore.removeItem(favoriteId, props.productId, props.variantId)
    const index = props.favoritesSelected.findIndex(
      (element) => element.id === favoriteId,
    )
    if (index !== -1) {
      props.favoritesSelected.splice(index, 1)
      notifySuccess('Le produit a été retiré de la liste')
    }
  } catch (error) {}

  isDelFavoriteLoading.value = false
}
</script>

<style scoped>
.tooltip {
  @apply flex w-[350px] flex-col items-center justify-center rounded-lg border border-2 border-primary bg-white p-2.5 shadow-[0_20px_250px_25px] shadow-gray-600;
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
