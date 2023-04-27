<template>
  <FavoriteModal>
    <template #title> Supprimer une liste de produits favoris</template>
    <template #content>
      <div class="px-5">
        <div v-if="favorite">
          <h4 class="text-sm text-white md:text-base lg:text-lg">
            Voulez-vous vraiment supprimer cette liste:
            <strong>{{ favorite.name }}</strong>
          </h4>
          <div v-if="favorite.nbUpplerProducts > 0">
            <label class="mt-2 flex items-center text-white">
              <input
                v-model="moveItem"
                type="checkbox"
                class="mr-2 mt-1 cursor-pointer border border-white lg:mt-0"
              />
              <span class="mr-1 text-xs md:text-sm lg:text-base"
                >Déplacer les produits de la liste vers une autre liste avant de
                les supprimer</span
              >
            </label>
            <div class="mt-5 flex flex-col">
              <span
                v-if="errorSelectavorite"
                class="mb-2 bg-red-100 p-2 text-red-700"
              >
                Vous devez sélectionner une liste
              </span>
              <select v-if="moveItem" v-model="selectedFavorite">
                <option value="" disabled selected>
                  Sélectionner une liste
                </option>
                <option
                  v-for="favoriteItem in listeFavorites"
                  :key="favoriteItem.id"
                  :value="favoriteItem.id"
                >
                  {{ favoriteItem.name }}
                </option>
              </select>
            </div>
          </div>
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
            @click="onDeleteFavorite"
          >
            Supprimer
          </ButtonComponent>
        </div>
      </div>
    </template>
  </FavoriteModal>
</template>

<script lang="ts" setup>
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import { computed, onMounted, ref } from 'vue'
import { Favorite } from '@/vuejs/types/Favorite'
import { useFavoriteStore } from '@/vuejs/stores/favorite'
import { storeToRefs } from 'pinia'
import FavoriteModal from '@/vuejs/modules/account/pages/FavoriteModalPage.vue'

const props = defineProps({
  favoriteId: {
    type: String,
    required: false,
    default: null,
  },
  isLoading: {
    type: Boolean,
    required: false,
    default: false,
  },
})
const favorite = ref<Favorite>()
const moveItem = ref<boolean>(false)
const errorSelectavorite = ref<boolean>(false)
const selectedFavorite = ref(null)
const favoriteStore = useFavoriteStore()
const { favorites } = storeToRefs(favoriteStore)
const emit = defineEmits(['cancel', 'deleteFavorite'])

onMounted(() => {
  favorite.value = favoriteStore.favorites.find(
    (fav) => fav.id === props.favoriteId,
  )
})

const onCancelClick = () => {
  emit('cancel')
}

const listeFavorites = computed(() => {
  return favorites.value.filter((fa) => fa.id !== props.favoriteId)
})

const onDeleteFavorite = async () => {
  if (moveItem.value && selectedFavorite.value === null) {
    errorSelectavorite.value = true
    return false
  }
  await emit('deleteFavorite', {
    favoriteId: props.favoriteId,
    selectedFavoriteId: selectedFavorite.value,
  })
}
</script>

<style scoped></style>
