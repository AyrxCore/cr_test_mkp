<template>
  <AccountPage>
    <template #right-side>
      <div
        v-if="isLoading"
        class="mt-5 flex h-20 w-full items-center justify-center"
      >
        <LoaderSharedComponent
          class="text-secondary"
          classes="loader-xl loader"
        />
      </div>
      <div v-else>
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
          v-for="(product, key) in favorite.favoriteProducts"
          :key="key"
          :product="product"
          :favorite-id="favorite.id"
          @remove-item="onRemoveItem"
          @move-item="onMoveItem"
          @selected-item="addItemSelectedToList"
          @remove-selected-item="removeItemSelectedToList"
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
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import FavoriteFormModal from '@/vuejs/modules/account/components/favorite/FavoriteAddEditModal.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import { useAlertStore } from '@/vuejs/stores/alert'
import AlertSharedComponent from '@/vuejs/modules/shared/AlertSharedComponent.vue'
import { storeToRefs } from 'pinia'
import { addProductToCartGoogleAnalytics } from '@/vuejs/modules/products'
import { useCartStore } from '@/vuejs/stores/cart'

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
  try {
    isEditLoading.value = true
    await favoriteStore.update(event.favorite)
    favorite.value.name = event.favorite.name
    isEditLoading.value = false
    showFormFavorite.value = false
  } catch (error) {}
}

const onRemoveItem = async (event) => {
  try {
    await favoriteStore.removeItem(event.favoriteId, event.productId)
    await refreshFavoriteItems(favorite.value.id)
  } catch (error) {}
}

const onMoveItem = async (event) => {
  try {
    await favoriteStore.moveItem({
      favoriteId: event.favoriteId,
      favoriteIdToReceive: event.favoriteIdToReceive,
      upplerProductId: event.upplerProductId,
    })
    await refreshFavoriteItems(favorite.value.id)
  } catch (error) {}
}

const addItemSelectedToList = async (event) => {
  try {
    listItemToAddCart.value.push({
      id: event.selectedItem.id,
      variantId: event.selectedItem.upplerVariantId,
      product: event.product,
    })

    cartProducts.value.push({
      variantId: event.selectedItem.upplerVariantId,
      quantity: 1,
    })
  } catch (error) {}
}

const removeItemSelectedToList = async (event) => {
  try {
    listItemToAddCart.value = listItemToAddCart.value.filter(
      (item) => item.id !== event.selectedItem.id,
    )
    cartProducts.value = cartProducts.value.filter(
      (cartProduct) =>
        cartProduct.variantId !== event.selectedItem.upplerVariantId,
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
