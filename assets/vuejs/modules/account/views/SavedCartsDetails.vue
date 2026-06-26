<template>
  <AccountPage>
    <template #right-side>
      <LoadingComponent v-if="isLoading" />
      <div v-else>
        <div v-if="savedCart">
          <div v-if="showAlert" class="lg:w-5/6">
            <AlertSharedComponent />
          </div>
          <div class="my-4 flex justify-between xl:mt-0">
            <h3 class="text-title-primary mb-2">{{ name }}</h3>
            <ButtonComponent
              :disabled="isNeoAutoLogin"
              class="button-primary-outline"
              @click="openSavedCartForm"
            >
              <EditIconComponent />
              Renommer le panier
            </ButtonComponent>
          </div>
          <SavedCartModal
            v-if="showForm"
            :is-loading="isLoadingModal"
            :saved-cart-id="savedCart.id"
            class="modal"
            @cancel="showForm = false"
            @submit-saved-cart="onSubmitSavedCart"
          />

          <div
            class="mb-2.5 hidden items-center text-sm text-gray-500 md:flex lg:text-base"
          >
            <div class="md:w-6/12">Description des articles</div>
            <div class="flex md:w-2/12">Prix unitaire</div>
            <div class="flex md:w-2/12">Quantité</div>
            <div class="flex md:w-2/12">Sous-total</div>
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
              :disabled="isNeoAutoLogin"
              :is-loading="isAddToCartLoading"
              class="button-primary mt-5 md:mt-0"
              @click="addToCart"
            >
              <ShoppingCartIconComponent :stroke="'#FFFFFF'" class="mr-2 w-4" />
              Ajouter au panier
            </ButtonComponent>
          </div>
        </div>
      </div>
    </template>
  </AccountPage>
</template>

<script lang="ts" setup>
import { computed, onMounted, ref, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { useRoute } from 'vue-router'

import { useUserStore } from '@/vuejs/stores/user'
import { useAlertStore } from '@/vuejs/stores/alert'
import { useCartStore } from '@/vuejs/stores/cart'
import { useSavedCartStore } from '@/vuejs/stores/savedCart'
import { sendGtmEvent } from '@/vuejs/services/gtm'
import { SavedCart } from '@/vuejs/types/SavedCart'

import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import AlertSharedComponent from '@/vuejs/modules/shared/AlertSharedComponent.vue'
import SavedCartDetailsComponent from '@/vuejs/modules/account/components/SavedCartDetailsComponent.vue'
import LoadingComponent from '@/vuejs/modules/shared/LoadingComponent.vue'
import SavedCartModal from '@/vuejs/modules/account/components/savedCart/SavedCartModal.vue'
import ShoppingCartIconComponent from '@/vuejs/modules/shared/icon/ShoppingCartIconComponent.vue'
import EditIconComponent from '@/vuejs/modules/shared/icon/EditIconComponent.vue'

const { isNeoAutoLogin } = storeToRefs(useUserStore())
const cartStore = useCartStore()
const route = useRoute()
const savedCart = ref<SavedCart>()
const savedCartStore = useSavedCartStore()
const isAddToCartLoading = ref<boolean>(false)
const isEditLoading = ref<boolean>(false)
const showForm = ref<boolean>(false)
const isLoadingModal = ref<boolean>(false)
const isLoading = ref<boolean>(false)
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
  // TODO: adapter à la nouvelle méthode addProductsToCart
  // await cartStore.addProductsToCart(cartProducts.value)

  for (const [, value] of Object.entries(cartProducts.value)) {
    sendGtmEvent('add_to_cart', {
      ecommerce: {
        currency: 'EUR',
        items: [
          {
            variant_id: value.variantId,
            quantity: value.quantity,
          },
        ],
      },
    })
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
