<template>
  <AccountPage>
    <template #right-side>
      <LoadingComponent v-if="isLoading" />
      <div v-else>
        <div v-if="favorite">
          <div v-if="showAlert" class="lg:w-5/6">
            <AlertSharedComponent />
          </div>
          <div class="my-6 mt-2 flex justify-between md:mt-0">
            <h3 class="text-title-primary">
              {{ favoriteName }}
            </h3>
            <ButtonComponent
              :disabled="isNeoAutoLogin"
              class="button-primary-outline"
              @click="openFavoriteForm"
            >
              Renommer la liste
            </ButtonComponent>
          </div>
          <FavoriteFormModal
            v-if="showFormFavorite"
            :favorite-id="favorite.id"
            :is-editing="true"
            :is-loading="isEditLoading"
            class="modal"
            @cancel="showFormFavorite = false"
            @submit-favorite="onSubmitFavorite"
          />

          <div v-if="favorite.favoriteProducts.length > 0">
            <div
              class="mb-2.5 hidden items-center text-sm md:flex lg:text-base"
            >
              <div class="md:w-8/12 lg:w-9/12">Description</div>
              <div class="flex justify-start md:w-4/12 lg:w-3/12">
                Prix unitaire
              </div>
            </div>
            <FavoritesProductsDetailsComponent
              v-for="(favoriteProduct, key) in favorite.favoriteProducts"
              :key="key"
              :favorite-id="favorite.id"
              :favorite-product="favoriteProduct"
              @remove-product="onRemoveProduct"
              @move-product="onMoveProduct"
              @selected-product="addProductSelectedToList"
              @remove-selected-product="removeProductSelectedToList"
            />
            <div class="mt-6 flex flex-col md:flex-row md:justify-end">
              <ButtonComponent
                :disabled="isNeoAutoLogin"
                class="button-primary-outline"
                @click="refreshFavoriteItems(favorite.id)"
              >
                Mettre à jour la liste
              </ButtonComponent>
              <ButtonComponent
                :disabled="listItemToAddCart.length === 0 || isNeoAutoLogin"
                :is-loading="isAddToCartLoading"
                class="button-primary ml-8 mt-5 md:mt-0"
                @click="addToCart"
              >
                Ajouter au panier
              </ButtonComponent>
            </div>
            <div class="mt-6">
              Les prix affichés sont donnés à titre indicatif et peuvent être
              mis à jour par les vendeurs
            </div>
          </div>
          <div
            v-else
            class="flex h-[100px] w-full items-center justify-center rounded-sm bg-white"
          >
            Aucune entrée dans votre liste de favoris
          </div>
        </div>
        <div
          v-else
          class="xs:w-full m-auto my-4 flex max-w-screen-2xl flex-col items-center justify-center p-10"
        >
          <div>Aucune liste avec cet identifiant n'a été trouvée</div>
          <RouterLink
            :to="{ name: PageList.FAVORITES_LIST }"
            class="button button-primary mt-10 w-auto md:w-auto"
          >
            <ArrowLeftIconComponent
              class="mr-2 w-4 !stroke-white"
              stroke="#FFFFFF"
            />
            Retour à la liste des favoris
          </RouterLink>
        </div>
      </div>
    </template>
  </AccountPage>
</template>

<script lang="ts" setup>
import { computed, onMounted, ref, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { useRoute } from 'vue-router'

import { PageList } from '@/vuejs/router'
import { useUserStore } from '@/vuejs/stores/user'
import { useCartStore } from '@/vuejs/stores/cart'
import { useAlertStore } from '@/vuejs/stores/alert'
import { useFavoriteStore } from '@/vuejs/stores/favorite'
import { formatProductGtmEvent, sendGtmEvent } from '@/vuejs/services/gtm'
import { Favorite } from '@/vuejs/types/Favorite'

import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'
import FavoritesProductsDetailsComponent from '@/vuejs/modules/account/components/FavoriteProductDetailsComponent.vue'
import FavoriteFormModal from '@/vuejs/modules/account/components/favorite/FavoriteAddEditModal.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import LoadingComponent from '@/vuejs/modules/shared/LoadingComponent.vue'
import AlertSharedComponent from '@/vuejs/modules/shared/AlertSharedComponent.vue'
import ArrowLeftIconComponent from '@/vuejs/modules/shared/icon/ArrowLeftIconComponent.vue'

const route = useRoute()

const cartStore = useCartStore()
const favoriteStore = useFavoriteStore()
const alertStore = useAlertStore()
const { isNeoAutoLogin } = storeToRefs(useUserStore())
const { show: showAlert } = storeToRefs(alertStore)

const isLoading = ref<boolean>(false)
const isAddToCartLoading = ref<boolean>(false)
const isEditLoading = ref<boolean>(false)
const favorite = ref<Favorite>()
const showFormFavorite = ref<boolean>(false)
const listItemToAddCart = ref([])
const cartProducts = ref([])

const favoriteName = computed(() => {
  return favorite.value.name
})
const openFavoriteForm = () => {
  showFormFavorite.value = true
}

const refreshFavoriteItems = async (id) => {
  isLoading.value = true
  favorite.value = await favoriteStore.findFavoriteById(id)
  isLoading.value = false
}

onMounted(async () => {
  await favoriteStore.fetchFavorites()
})

const onSubmitFavorite = async (event) => {
  isEditLoading.value = true
  const oldFavoriteName = event.favorite.name
  event.favorite.name = event.newFavoriteName ?? event.favorite.name
  try {
    await favoriteStore.update(event.favorite)
    favorite.value.name = event.favorite.name
  } catch (error) {
    favorite.value.name = oldFavoriteName
  } finally {
    isEditLoading.value = false
    showFormFavorite.value = false
  }
}

const onRemoveProduct = async (event) => {
  isLoading.value = true

  try {
    await favoriteStore.removeProduct(event.favoriteProductId)
    await refreshFavoriteItems(favorite.value.id)
  } catch (error) {
  } finally {
    isLoading.value = false
  }
}

const onMoveProduct = async (event) => {
  isLoading.value = true
  try {
    await favoriteStore.moveProduct(event.favoriteProductId, event.favoriteId)
    await refreshFavoriteItems(favorite.value.id)
  } catch (error) {
  } finally {
    isLoading.value = false
  }
}

const addProductSelectedToList = async (event) => {
  try {
    // listItemToAddCart.value.push({
    //   id: event.selectedProduct.id,
    //   variantId: event.selectedProduct.upplerVariantId,
    //   product: event.product,
    // })
    //
    // cartProducts.value.push({
    //   variantId: event.selectedProduct.upplerVariantId,
    //   quantity: 1,
    // })
  } catch (error) {}
}

const removeProductSelectedToList = async (event) => {
  try {
    listItemToAddCart.value = listItemToAddCart.value.filter(
      (product) => product.id !== event.selectedProduct.id,
    )
    // cartProducts.value = cartProducts.value.filter(
    //   (cartProduct) =>
    //     cartProduct.variantId !== event.selectedProduct.upplerVariantId,
    // )
  } catch (error) {}
}
const addToCart = async () => {
  isAddToCartLoading.value = true
  // TODO: adapter à la nouvelle méthode addProductsToCart
  // await cartStore.addProductsToCart(cartProducts.value)

  for (const [, value] of Object.entries(listItemToAddCart.value)) {
    sendGtmEvent('add_to_cart', {
      ecommerce: {
        currency: 'EUR',
        value: value.product.price * value.product.quantity,
        items: formatProductGtmEvent([value.product]),
      },
    })
  }
  isAddToCartLoading.value = false

  isLoading.value = true
  favorite.value = await favoriteStore.findFavoriteById(favorite.value.id)
  isLoading.value = false
}

watch(
  () => route.params.id as string,
  async (id: string) => {
    if (id) {
      await refreshFavoriteItems(id)
    }
  },

  { immediate: true },
)
</script>
