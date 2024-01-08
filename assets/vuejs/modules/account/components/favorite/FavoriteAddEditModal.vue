<template>
  <FavoriteModal @cancel="onCancelClick">
    <template #title>
      {{ isEditing ? 'Renommer la liste' : 'Ajouter une liste' }}
    </template>
    <template #content>
      <form v-if="favorite" class="w-full" @submit.prevent="onSubmitFavorite">
        <div class="px-3 md:px-8">
          <FavoriteForm :favorite="favorite" />
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
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import { onMounted, ref } from 'vue'
import { Favorite } from '@/vuejs/types/Favorite'
import { useFavoriteStore } from '@/vuejs/stores/favorite'
import FavoriteForm from '@/vuejs/modules/account/components/favorite/FavoriteForm.vue'
import FavoriteModal from '@/vuejs/modules/account/pages/DefaultModalPage.vue'

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
})
const favorite = ref<Favorite>()
const favoriteStore = useFavoriteStore()
const emit = defineEmits(['cancel', 'submitFavorite'])

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
  emit('cancel')
}

const onSubmitFavorite = async () => {
  await emit('submitFavorite', {
    favorite: favorite.value,
    isEditing: props.isEditing,
  })
}
</script>

<style scoped></style>
