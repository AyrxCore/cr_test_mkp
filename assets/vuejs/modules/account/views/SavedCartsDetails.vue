<template>
  <AccountPage>
    <template #right-side>
      <LoadingComponent v-if="isLoading" />
      <div v-else>
        <div v-if="savedCart">
          <div v-if="showAlert" class="lg:w-5/6">
            <AlertSharedComponent />
          </div>
          <div class="mt-2 mb-2 flex justify-between md:mt-0 md:mb-0">
            <h3 class="mb-2 text-title-35 text-primary">{{ name }}</h3>
            <ButtonComponent
              class="button button-white button-white-secondary"
              @click="openSavedCartForm"
            >
              <EditIconComponent />
              Renommer le panier
            </ButtonComponent>
          </div>
          <SavedCartModal
            v-if="showForm"
            class="modal"
            :is-loading="isLoadingModal"
            :saved-cart-id="savedCart.id"
            @cancel="showForm = false"
            @submit-saved-cart="onSubmitSavedCart"
          />

          <div
            class="mb-2.5 hidden items-center text-sm text-gray-500 md:flex lg:text-base"
          >
            <div class="md:w-8/12 lg:w-9/12">Description des articles</div>
            <div class="flex w-full items-center justify-between md:w-4/12">
              <div class="flex">Quantité</div>
              <div class="flex">Sous-total</div>
            </div>
          </div>
          <SavedCartDetailsComponent
            v-for="(savedCartProduct, key) in savedCart.savedCartProducts"
            :key="key"
            :saved-cart-product="savedCartProduct"
            @change-quantity="onChangeQuantity"
          />
          <p class="text-xs text-gray-500 md:text-sm lg:text-base">
            Les prix affichés sont donnés à titre indicatif et peuvent être mis
            à jour par les vendeurs
          </p>
          <div class="mt-6 flex flex-col justify-end md:flex-row">
            <ButtonComponent
              class="button-gradient mt-5 md:mt-0"
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
      </div>
    </template>
  </AccountPage>
</template>
<script lang="ts" setup>
import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'
import { computed, onMounted, ref, watch } from 'vue'
import ShoppingCartIconComponent from '@/vuejs/modules/shared/icon/ShoppingCartIconComponent.vue'
import { useRoute } from 'vue-router'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import { useAlertStore } from '@/vuejs/stores/alert'
import AlertSharedComponent from '@/vuejs/modules/shared/AlertSharedComponent.vue'
import { storeToRefs } from 'pinia'
import { addProductToCartGoogleAnalytics } from '@/vuejs/modules/products'
import { useCartStore } from '@/vuejs/stores/cart'
import SavedCartModal from '@/vuejs/modules/account/components/savedCart/SavedCartModal.vue'
import { SavedCart } from '@/vuejs/types/SavedCart'
import { useSavedCartStore } from '@/vuejs/stores/savedCart'
import SavedCartDetailsComponent from '@/vuejs/modules/account/components/SavedCartDetailsComponent.vue'
import EditIconComponent from '@/vuejs/modules/shared/icon/EditIconComponent.vue'
import LoadingComponent from '@/vuejs/modules/shared/LoadingComponent.vue'

const cartStore = useCartStore()
const route = useRoute()
const savedCart = ref<SavedCart>()
const savedCartStore = useSavedCartStore()
const isAddToCartLoading = ref<boolean>(false)
const isEditLoading = ref<boolean>(false)
const showForm = ref<boolean>(false)
const isLoadingModal = ref<boolean>(false)
const isLoading = ref<boolean>(false)
const listItemToAddCart = ref([])
const alertStore = useAlertStore()
const cartProducts = ref([])

const { show: showAlert } = storeToRefs(alertStore)

const name = computed(() => {
  return savedCart.value.name
})
const openSavedCartForm = () => {
  showForm.value = true
}

onMounted(async () => {
  await savedCartStore.fetchSavedCarts()
})

const onSubmitSavedCart = async (event) => {
  try {
    isEditLoading.value = true

    await savedCartStore.update(event.savedCart)

    savedCart.value.name = event.savedCart.name
    isEditLoading.value = false
    showForm.value = false
  } catch (error) {}
}

const onChangeQuantity = async (event) => {
  try {
    const index = cartProducts.value.findIndex(
      (element) => element.variantId === event.variantId,
    )

    cartProducts.value[index].quantity = event.quantity
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
    isLoading.value = true

    if (id) {
      savedCart.value = await savedCartStore.findSavedCartById(id)

      for (const [, value] of Object.entries(
        savedCart.value.savedCartProducts,
      )) {
        cartProducts.value.push({
          variantId: value.upplerVariantId,
          quantity: value.quantity,
        })
      }
    }

    isLoading.value = false
  },

  { immediate: true },
)
</script>

<style scoped></style>
