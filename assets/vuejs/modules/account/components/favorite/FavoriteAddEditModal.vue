<template>
  <FavoriteModal @cancel="onCancelClick">
    <template #title>
      {{ isEditing ? 'Renommer la liste' : 'Ajouter une liste' }}
    </template>
    <template #content>
      <form v-if="favorite" class="w-full" @submit.prevent="onSubmitFavorite">
        <div class="px-3 md:px-8">
          <FavoriteForm
            :favorite="favorite"
            :error-submit="errorMessage"
            @change-value="changeValue($event)"
          />
          <div class="flex justify-between md:justify-end">
            <ButtonComponent
              class="button-primary-outline mr-2"
              type="button"
              @click="onCancelClick"
            >
              Annuler
            </ButtonComponent>
            <ButtonComponent class="button-primary" :is-loading="isLoading">
              Enregistrer
            </ButtonComponent>
          </div>
        </div>
      </form>
    </template>
  </FavoriteModal>
</template>

<script lang="ts" setup>
import { onMounted, onUpdated, ref } from 'vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import FavoriteForm from '@/vuejs/modules/account/components/favorite/FavoriteForm.vue'
import FavoriteModal from '@/vuejs/modules/account/pages/DefaultModalPage.vue'
import { Favorite } from '@/vuejs/types/Favorite'
import { useFavoriteStore } from '@/vuejs/stores/favorite'

const props = defineProps({
  favoriteId: {
    type: String,
    required: false,
    default: null,
  },
  isEditing: {
    type: Boolean,
    required: false,
    default: false,
  },
  isLoading: {
    type: Boolean,
    required: false,
    default: false,
  },
  errorSubmit: {
    type: String,
    required: false,
    default: null,
  },
})
const favorite = ref<Favorite>()
const favoriteName = ref<string|null>(null)
const errorMessage = ref<string|null>(null)
const favoriteStore = useFavoriteStore()
const emit = defineEmits(['cancel', 'submitFavorite', 'changeValue'])

onMounted(async () => {
  if (props.favoriteId) {
    favorite.value = favoriteStore.favorites.find(
      (fav) => fav.id === props.favoriteId,
    )
  } else {
    favorite.value = {
      name: null,
      public: false,
    }
  }
})

const onCancelClick = () => {
  errorMessage.value = null
  emit('cancel')
}

const onSubmitFavorite = async () => {
  emit('submitFavorite', {
    newFavoriteName: favoriteName.value,
    favorite: favorite.value,
    isEditing: props.isEditing,
  })
}

const changeValue = async (event) => {
  favoriteName.value = event
  emit('changeValue', event)
}

onUpdated(() => {
  errorMessage.value = props.errorSubmit
})
</script>

<style scoped></style>
