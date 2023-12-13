<template>
  <AccountPage>
    <template #right-side>
      <LoadingComponent v-if="isLoading" />
      <div v-else>
        <div v-if="favorite">
          <div v-if="showAlert" class="lg:w-5/6">
            <AlertSharedComponent />
          </div>
          <div class="mt-2 mb-2 flex justify-between md:mt-0 md:mb-0">
            <h3 class="mb-2 text-title-35 text-primary">{{ favoriteName }}</h3>
            <ButtonComponent
              class="button button-white button-white-secondary"
              @click="openFavoriteForm"
            >
              Renommer
            </ButtonComponent>
          </div>
          <FavoriteFormModal
            v-if="showFormFavorite"
            class="modal"
            :favorite-id="favorite.id"
            :is-editing="true"
            :is-loading="isEditLoading"
            @cancel="showFormFavorite = false"
            @submit-favorite="onSubmitFavorite"
          />

          <div
            class="mb-2.5 hidden items-center text-sm text-gray-500 md:flex lg:text-base"
          >
            <div class="md:w-8/12 lg:w-9/12">Description des articles</div>
            <div class="flex justify-start md:w-4/12 lg:w-3/12">Sous-total</div>
          </div>
          <FavoritesProductsDetailsComponent
            v-for="(favoriteProduct, key) in favorite.favoriteProducts"
            :key="key"
            :favorite-product="favoriteProduct"
            :favorite-id="favorite.id"
            @remove-product="onRemoveProduct"
            @move-product="onMoveProduct"
            @selected-product="addProductSelectedToList"
            @remove-selected-product="removeProductSelectedToList"
          />
          <div class="mt-6 flex flex-col justify-between md:flex-row">
            <ButtonComponent
              class="button-white-secondary !text-secondary hover:!bg-white focus:!bg-white"
              @click="refreshFavoriteItems(favorite.id)"
            >
              Mettre à jour la liste
            </ButtonComponent>
            <ButtonComponent
              class="button-gradient mt-5 md:mt-0"
              :disabled="listItemToAddCart.length === 0"
              :is-loading="isAddToCartLoading"
              @click="addToCart"
            >
              <ShoppingCartIconComponent
                :stroke-color="'#FFFFFF'"
                class="mr-2 w-4"
              />
              Ajouter au panier
            </ButtonComponent>
          </div>
        </div>
        <div
          v-else
          class="xs:w-full m-auto my-4 flex max-w-screen-2xl flex-col items-center justify-center p-10"
        >
          <div>Aucune liste avec cet identifiant n'a été trouvée</div>
          <RouterLink
            class="button button-gradient mt-10 w-auto md:w-auto"
            :to="{ name: PageList.FAVORITES_LIST }"
          >
            <ArrowLeftIconComponent
              :stroke-color="'#FFFFFF'"
              class="mr-2 w-4 !stroke-white"
            />
            Retour à la liste des favoris
          </RouterLink>
        </div>
      </div>
    </template>
  </AccountPage>
</template>
<script lang="ts" setup>
import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'
import { computed, onMounted, ref, watch } from 'vue'
import FavoritesProductsDetailsComponent from '@/vuejs/modules/account/components/FavoriteProductDetailsComponent.vue'
import ShoppingCartIconComponent from '@/vuejs/modules/shared/icon/ShoppingCartIconComponent.vue'
import { useRoute } from 'vue-router'
import { useFavoriteStore } from '@/vuejs/stores/favorite'
import { Favorite } from '@/vuejs/types/Favorite'
import FavoriteFormModal from '@/vuejs/modules/account/components/favorite/FavoriteAddEditModal.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import { useAlertStore } from '@/vuejs/stores/alert'
import AlertSharedComponent from '@/vuejs/modules/shared/AlertSharedComponent.vue'
import { storeToRefs } from 'pinia'
import { addProductToCartGoogleAnalytics } from '@/vuejs/modules/products'
import { useCartStore } from '@/vuejs/stores/cart'
import LoadingComponent from '@/vuejs/modules/shared/LoadingComponent.vue'
import { PageList } from '@/vuejs/router'
import ArrowLeftIconComponent from '@/vuejs/modules/shared/icon/ArrowLeftIconComponent.vue'

const cartStore = useCartStore()
const route = useRoute()
const favoriteStore = useFavoriteStore()
const isLoading = ref<boolean>(false)
const isAddToCartLoading = ref<boolean>(false)
const isEditLoading = ref<boolean>(false)
const favorite = ref<Favorite>()
const showFormFavorite = ref<boolean>(false)
const listItemToAddCart = ref([])
const alertStore = useAlertStore()
const cartProducts = ref([])

const { show: showAlert } = storeToRefs(alertStore)

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

  try {
    await favoriteStore.update(event.favorite)
    favorite.value.name = event.favorite.name
    isEditLoading.value = false
    showFormFavorite.value = false
  } catch (error) {
  } finally {
    isEditLoading.value = false
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
    listItemToAddCart.value.push({
      id: event.selectedProduct.id,
      variantId: event.selectedProduct.upplerVariantId,
      product: event.product,
    })

    cartProducts.value.push({
      variantId: event.selectedProduct.upplerVariantId,
      quantity: 1,
    })
  } catch (error) {}
}

const removeProductSelectedToList = async (event) => {
  try {
    listItemToAddCart.value = listItemToAddCart.value.filter(
      (product) => product.id !== event.selectedProduct.id,
    )
    cartProducts.value = cartProducts.value.filter(
      (cartProduct) =>
        cartProduct.variantId !== event.selectedProduct.upplerVariantId,
    )
  } catch (error) {}
}
const addToCart = async () => {
  isAddToCartLoading.value = true
  await cartStore.addProductsToCart(cartProducts.value)

  for (const [, value] of Object.entries(listItemToAddCart.value)) {
    await addProductToCartGoogleAnalytics(value.product, value.variantId, 1)
  }
  isAddToCartLoading.value = false

  await refreshFavoriteItems(favorite.value.id)
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

<style scoped></style>
