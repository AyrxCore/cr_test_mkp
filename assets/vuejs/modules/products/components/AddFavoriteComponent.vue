<template>
  <div v-click-outside="onOutsideBlock" class="flex items-center justify-end">
    <button class="flex text-gray-500" @click="onOpenFavorite">
      <HeartIconComponent
        class="... h-[40px] w-[40px] stroke-gray-500 lg:h-auto lg:w-auto"
        :class="{
          'fill-secondary !stroke-secondary':
            currentSelectedFavorites.length > 0,
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
                      v-model="newSelectedFavorites"
                      type="checkbox"
                      class="checkbox-secondary"
                      :value="favoriteItem.id"
                      :checked="isChecked(favoriteItem.id)"
                      @change="handleChange"
                    />
                    <span class="ml-2">{{ favoriteItem.name }}</span>
                  </label>
                </div>
              </div>
              <div class="flex px-1">
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
                    disableAddButton &&
                    (newFavorite === null || newFavorite === '')
                  "
                  :is-loading="addProductToFavoriteLoading"
                >
                  {{ addButtonName }}
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
import { computed, ref } from 'vue'
import { arrayEqual } from '@/vuejs/services/utils'

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
  favoritesProduct: {
    type: Array,
    default: null,
  },
})

const favoriteStore = useFavoriteStore()
const { favorites } = storeToRefs(favoriteStore)
const showTooltip = ref<boolean>(false)
const showList = ref<boolean>(true)
const currentSelectedFavorites = ref(props.favoritesProduct)
const newSelectedFavorites = ref(props.favoritesProduct)
const newFavorite = ref(null)
const addProductToFavoriteLoading = ref<boolean>(false)
const isLoading = ref<boolean>(false)
const showErrorNotSelected = ref<boolean>(false)
const disableAddButton = ref<boolean>(true)

const emit = defineEmits(['openFavorite', 'updateSelectedFavoritesList'])

const onOpenFavorite = () => {
  showTooltip.value = true
  emit('openFavorite', { showTooltip: showTooltip.value })
}
const onOutsideBlock = () => {
  showTooltip.value = false
  showErrorNotSelected.value = false
  newFavorite.value = null
  emit('openFavorite', { showTooltip: showTooltip.value })
}

const isChecked = (favoriteId) => {
  return Object.entries(props.favoritesProduct).includes(favoriteId)
}

const handleChange = () => {
  disableAddButton.value = arrayEqual(
    newSelectedFavorites.value,
    currentSelectedFavorites.value,
  )
}

const addButtonName = computed(() => {
  return (newSelectedFavorites.value.length === 0 &&
    currentSelectedFavorites.value.length > 0) ||
    newSelectedFavorites.value.length < currentSelectedFavorites.value.length
    ? 'Retirer'
    : 'Ajouter'
})

const addProductToFavorite = async () => {
  isLoading.value = true

  if (
    newFavorite.value ||
    newSelectedFavorites.value.length > 0 ||
    (newSelectedFavorites.value.length === 0 &&
      currentSelectedFavorites.value.length > 0)
  ) {
    showErrorNotSelected.value = false
    addProductToFavoriteLoading.value = true
    try {
      if (newFavorite.value) {
        const favorite = await favoriteStore.create({
          name: newFavorite.value,
          public: false,
        })
        const favoriteId = favorite.id
        newSelectedFavorites.value.push(favoriteId)
      }
      await favoriteStore.addProduct({
        selectedFavorites: newSelectedFavorites.value,
        productId: props.productId,
        productName: props.productName,
        variantId: props.variantId,
      })

      currentSelectedFavorites.value = newSelectedFavorites.value
      onOutsideBlock()
      await favoriteStore.fetchFavorites()
    } catch (error) {}

    addProductToFavoriteLoading.value = false
  } else {
    showErrorNotSelected.value = true
  }
  isLoading.value = false
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
