<template>
  <FavoriteModal @cancel="onCancelClick">
    <template #title> Déplacer vers une autre liste</template>
    <template #content>
      <div class="px-5">
        <div v-if="favoriteProduct">
          <h4 class="text-sm text-white md:text-base lg:text-lg">
            Voulez-vous vraiment déplacer ce produit:
            <strong>{{ favoriteProduct.upplerProductName }}</strong>
          </h4>
        </div>

        <div class="mt-5 flex flex-col">
          <span
            v-if="errorSelectavorite"
            class="mb-2 bg-red-100 p-2 text-red-700"
          >
            Vous devez sélectionner une liste
          </span>
          <select v-model="selectedFavorite">
            <option
              v-for="favoriteItem in listeFavorites"
              :key="favoriteItem.id"
              :value="favoriteItem.id"
            >
              {{ favoriteItem.name }}
            </option>
          </select>
        </div>

        <div class="mt-5 flex justify-end">
          <ButtonComponent
            class="button-primary-outline mr-2"
            type="button"
            @click="onCancelClick"
          >
            Annuler
          </ButtonComponent>
          <ButtonComponent
            class="button-primary"
            :is-loading="isLoading"
            @click="onMoveItem"
          >
            Déplacer
          </ButtonComponent>
        </div>
      </div>
    </template>
  </FavoriteModal>
</template>

<script lang="ts" setup>
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import FavoriteModal from '@/vuejs/modules/account/pages/DefaultModalPage.vue'
import { computed, onMounted, PropType, ref } from 'vue'
import { Favorite, FavoriteProduct } from '@/vuejs/types/Favorite'
import { useFavoriteStore } from '@/vuejs/stores/favorite'
import { storeToRefs } from 'pinia'

const props = defineProps({
  favoriteId: {
    type: String,
    required: false,
    default: null,
  },
  favoriteProduct: {
    type: Object as PropType<FavoriteProduct>,
    required: true,
  },
  isLoading: {
    type: Boolean,
    required: false,
    default: false,
  },
})
const emit = defineEmits(['cancel', 'moveProduct'])
const favorite = ref<Favorite>()
const errorSelectavorite = ref<boolean>(false)
const selectedFavorite = ref(null)
const favoriteStore = useFavoriteStore()
const { favorites } = storeToRefs(favoriteStore)

onMounted(() => {
  favorite.value = favoriteStore.favorites.find(
    (fav) => fav.id === props.favoriteId,
  )
})

const onCancelClick = () => {
  emit('cancel')
}

const onMoveItem = async () => {
  if (selectedFavorite.value === null) {
    errorSelectavorite.value = true
    return false
  }

  await emit('moveProduct', {
    favoriteId: selectedFavorite.value,
    favoriteProductId: props.favoriteProduct.id,
  })
}

const listeFavorites = computed(() => {
  return favorites.value.filter((fa) => fa.id !== props.favoriteId)
})
</script>

<style scoped></style>
