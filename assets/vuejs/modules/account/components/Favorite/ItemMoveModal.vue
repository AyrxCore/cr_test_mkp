<template>
  <FavoriteModal>
    <template #title> Déplacer vers une autre liste</template>
    <template #content>
      <div class="px-5">
        <div v-if="product">
          <h4 class="text-sm text-white md:text-base lg:text-lg">
            Voulez-vous vraiment déplacer ce produit:
            <strong>{{ product.name }}</strong>
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
            class="button-secondary !hover:border mr-2 !border"
            type="button"
            @click="onCancelClick"
          >
            Annuler
          </ButtonComponent>
          <ButtonComponent
            class="button-primary !border-0 hover:!bg-primary focus:!bg-primary"
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
import FavoriteModal from '@/vuejs/modules/account/pages/FavoriteModalPage.vue'
import { computed, onMounted, ref } from 'vue'
import { Favorite } from '@/vuejs/types/Favorite'
import { useFavoriteStore } from '@/vuejs/stores/favorite'
import { storeToRefs } from 'pinia'

const props = defineProps({
  favoriteId: {
    type: String,
    required: false,
    default: null,
  },
  product: {
    type: Object,
    required: true,
  },
  isLoading: {
    type: Boolean,
    required: false,
    default: false,
  },
})
const emit = defineEmits(['cancel', 'moveItem'])
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

  await emit('moveItem', {
    favoriteId: props.favoriteId,
    favoriteIdToReceive: selectedFavorite.value,
    upplerProductId: props.product.id,
  })
}

const listeFavorites = computed(() => {
  return favorites.value.filter((fa) => fa.id !== props.favoriteId)
})
</script>

<style scoped></style>
