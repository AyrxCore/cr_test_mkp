<template>
  <!-- TODO (MKP-1411): Bouton "Ajouter aux favoris" (❤️) temporairement masqué sur les fiches produit.
       À rétablir (restaurer le v-if ci-dessous) quand les Favoris seront disponibles via DJUST.
       v-if="channelStore.isAllowedToShow(OPTIONAL_FRONT_BLOCKS.FAVORITES)" -->
  <div
    v-if="false"
    class="flex items-center justify-end"
  >
    <div class="flex cursor-pointer" @click="onOpenFavorite">
      <div v-if="hasFavoriteLabel" class="mr-2 underline">
        <template v-if="currentSelectedFavorites.length > 0"
          >Retirer de
        </template>
        <template v-else>Ajouter à</template>
        mes favoris
      </div>
      <button class="flex text-gray-500">
        <HeartIconComponent
          :fill="
            currentSelectedFavorites.length > 0
              ? channelSecondaryColor
              : '#FFFFFF'
          "
          :stroke="
            currentSelectedFavorites.length > 0
              ? channelSecondaryColor
              : '#000000'
          "
          class="lg:h-auto lg:w-auto"
        />
      </button>
    </div>

    <Modal v-if="showTooltip" :close="onOutsideBlock">
      <div class="fixed">
        <div class="z-[9] mx-3 rounded p-3 md:p-0 lg:mx-0">
          <div class="flex w-full">
            <div class="tooltip mt-10">
              <form
                v-if="showList"
                class="flex w-full flex-col space-y-2"
                @submit.prevent="addProductToFavorite"
              >
                <h3 class="font-bold text-primary">Gestion des favoris</h3>
                <span class="my-2"
                  >Ajouter/retirer<span class="mx-2 font-bold italic">{{
                    productName
                  }}</span
                  >de vos listes de favoris ?</span
                >
                <hr />
                <span
                  v-if="showErrorNotSelected"
                  class="bg-orange-200 p-3 text-base text-gray-600"
                >
                  Veuillez sélectionner une liste de favoris</span
                >

                <div
                  v-if="favorites"
                  class="c-scrollbar flex max-h-[250px] flex-col overflow-y-auto px-1"
                >
                  <div v-for="favoriteItem in favorites" :key="favoriteItem.id">
                    <label
                      class="my-2 flex cursor-pointer items-center text-base text-gray-600"
                    >
                      <input
                        v-model="newSelectedFavorites"
                        :checked="isChecked(favoriteItem)"
                        :value="favoriteItem.id"
                        class="checkbox-secondary"
                        type="checkbox"
                        @change="handleChange"
                      />
                      <span class="ml-2">{{ favoriteItem.name }}</span>
                    </label>
                  </div>
                </div>
                <div class="flex px-1">
                  <input
                    v-model.trim="newFavorite"
                    class="my-2 ml-1 w-full rounded py-0"
                    placeholder="Ajouter une nouvelle liste"
                    type="text"
                  />
                </div>

                <div class="!mt-5 flex justify-end">
                  <ButtonComponent
                    class="button-primary-outline flex !h-10 justify-end !py-2"
                    type="button"
                    @click="onOutsideBlock"
                    >Annuler
                  </ButtonComponent>
                  <ButtonComponent
                    :disabled="
                      disableAddButton &&
                      (newFavorite === null || newFavorite === '')
                    "
                    :is-loading="addProductToFavoriteLoading"
                    class="button-primary ml-2 !h-10"
                  >
                    {{ addButtonName }}
                  </ButtonComponent>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script lang="ts" setup>
import { computed, ref } from 'vue'
import { storeToRefs } from 'pinia'

import { useFavoriteStore } from '@/vuejs/stores/favorite'
import { useChannelStore } from '@/vuejs/stores/channel'
import { useProductStore } from '@/vuejs/stores/product'
import { arrayEqual, notifySuccess } from '@/vuejs/services/utils'
// TODO (MKP-1411): Décommenter quand les Favoris seront disponibles via DJUST
// import { OPTIONAL_FRONT_BLOCKS } from '@/vuejs/services/const'
import { sendGtmEvent } from '@/vuejs/services/gtm'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import HeartIconComponent from '@/vuejs/modules/shared/icon/HeartIconComponent.vue'

const props = defineProps({
  productId: {
    type: String,
    required: true,
  },
  variantId: {
    type: Number,
    default: null,
  },
  productName: {
    required: true,
    type: String,
  },
  favoritesProduct: {
    type: Array,
    default: null,
  },
  hasFavoriteLabel: {
    type: Boolean,
    default: false,
  },
})

// TODO (MKP-1411): Décommenter quand les Favoris seront disponibles via DJUST
// const channelStore = useChannelStore()
const favoriteStore = useFavoriteStore()
const productStore = useProductStore()
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

const { channelSecondaryColor } = storeToRefs(useChannelStore())

const emit = defineEmits([
  'toggleFavorite',
  'updateSelectedFavoritesList',
  'openFavorite',
  'selectFavorite',
  'addFavoriteList',
])

const onOpenFavorite = () => {
  showTooltip.value = true
  sendGtmEvent('add_to_wishlist', {
    ecommerce: {
      currency: 'EUR',
      items: [
        {
          item_id: props.productId,
          item_name: props.productName,
        },
      ],
    },
  })
  emit('toggleFavorite', { showTooltip: showTooltip.value })
  emit('openFavorite')
}
const onOutsideBlock = () => {
  showTooltip.value = false
  showErrorNotSelected.value = false
  newFavorite.value = null
  newSelectedFavorites.value = props.favoritesProduct
  disableAddButton.value = true
  emit('toggleFavorite', { showTooltip: showTooltip.value })
}

const isChecked = (favorite) => {
  emit('selectFavorite', favorite.name)
  return Object.entries(props.favoritesProduct).includes(favorite.id)
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

      const favoritesNotChanged = currentSelectedFavorites.value.filter((val) =>
        newSelectedFavorites.value.includes(val),
      )

      if (favoritesNotChanged.length < currentSelectedFavorites.value.length) {
        notifySuccess(`${props.productName} a été retiré des favoris`)
      }
      if (
        newSelectedFavorites.value.length > 0 &&
        newSelectedFavorites.value.length > favoritesNotChanged.length
      ) {
        notifySuccess(`${props.productName} a été ajouté aux favoris`)
      }

      currentSelectedFavorites.value = newSelectedFavorites.value
      emit('addFavoriteList', newFavorite.value)
      onOutsideBlock()
      await favoriteStore.fetchFavorites()
      await productStore.initSliderAccordsCadres()
      await productStore.initSliderProductsSelection()
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
  @apply flex max-w-[330px] flex-col items-center justify-center rounded-lg border border-2 bg-white p-7 shadow-[0_20px_250px_25px] shadow-gray-600 md:max-w-[600px];
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
