<template>
  <AccountPage>
    <template #right-side>
      <h3 class="text-title-primary mb-2 mt-2 xl:mt-0">
        Mes listes de produits favoris
      </h3>
      <FavoriteFormModal
        v-if="showFormFavorite"
        class="modal"
        :is-loading="isLoading"
        @cancel="showFormFavorite = false"
        @submit-favorite="onSubmitFavorite"
      />
      <div v-if="showAlert" class="lg:w-5/6">
        <AlertSharedComponent />
      </div>
      <div
        class="mt-8 mb-2.5 hidden items-center px-2.5 text-sm md:flex lg:text-base"
      >
        <div class="w-5/12">Nom de la liste</div>
        <div class="w-2/12">Créée le</div>
        <div class="w-2/12">Modifiée le</div>
        <div class="w-2/12">Nombre d'articles</div>
        <div class="w-1/12" />
      </div>
      <LoadingComponent v-if="isLoading" />
      <div v-else>
        <div
          v-if="favorites.length === 0"
          class="mt-5 flex flex-row flex-wrap justify-center rounded-lg bg-white py-2 text-sm md:text-base lg:text-lg"
        >
          Aucune liste de favori n'a été créée
        </div>
        <div v-else class="flex flex-row flex-wrap justify-between">
          <FavoritesProductsComponent
            v-for="(favorite, key) in favorites"
            :key="key"
            :favorite="favorite"
            :can-delete="favorite.accountId === user.account.id"
            @submit-favorite="onSubmitFavorite"
            @delete-favorite="onDeleteFavorite"
            @cancel="showFormFavorite = false"
          />
        </div>
        <div class="mt-4 flex w-full justify-end">
          <ButtonComponent
            class="button-primary mb-2 md:mb-0"
            @click="openFavoriteForm"
          >
            Ajouter une liste de favoris
          </ButtonComponent>
        </div>
      </div>
    </template>
  </AccountPage>
</template>
<script lang="ts" setup>
import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'
import { computed, onMounted, ref } from 'vue'
import FavoritesProductsComponent from '@/vuejs/modules/account/components/FavoriteProductComponent.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import FavoriteFormModal from '@/vuejs/modules/account/components/favorite/FavoriteAddEditModal.vue'
import { useFavoriteStore } from '@/vuejs/stores/favorite'
import { storeToRefs } from 'pinia'
import { useAlertStore } from '@/vuejs/stores/alert'
import AlertSharedComponent from '@/vuejs/modules/shared/AlertSharedComponent.vue'
import { useUserStore } from '@/vuejs/stores/user'
import LoadingComponent from '@/vuejs/modules/shared/LoadingComponent.vue'
import { sendGaEvent } from '@/vuejs/services/googleAnalytics'

const userStore = useUserStore()
const { user } = storeToRefs(userStore)
const alertStore = useAlertStore()
const showFormFavorite = ref<boolean>(false)
const deleteFavorite = ref<boolean>(false)
const favoriteStore = useFavoriteStore()
const isLoading = ref<boolean>(false)
const { show: showAlert } = storeToRefs(alertStore)

onMounted(async () => {
  isLoading.value = true
  await favoriteStore.fetchFavorites()
  isLoading.value = false
})
const openFavoriteForm = () => {
  showFormFavorite.value = true
  sendGaEvent('click_favorites_add')
}

const onSubmitFavorite = async (event) => {
  isLoading.value = true
  try {
    if (event.isEditing) {
      await favoriteStore.update(event.favorite)
    } else {
      await favoriteStore.create(event.favorite)
    }
    await favoriteStore.fetchFavorites()
    showFormFavorite.value = false
  } catch (error) {}

  isLoading.value = false
}

const onDeleteFavorite = async (event) => {
  isLoading.value = true
  try {
    if (event.selectedFavoriteId) {
      await favoriteStore.deleteFavoriteAndMoveProductToOtherFavorite(
        event.favoriteId,
        event.selectedFavoriteId,
      )
    } else {
      await favoriteStore.delete(event.favoriteId)
    }

    await favoriteStore.fetchFavorites()
    deleteFavorite.value = false
  } catch (error) {}

  isLoading.value = false
}

const favorites = computed(() => {
  return favoriteStore.favorites
})
</script>

<style scoped></style>
