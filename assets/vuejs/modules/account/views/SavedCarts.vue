<template>
  <AccountPage>
    <template #right-side>
      <h3 class="mb-2 mt-2 text-title-35 text-primary md:mt-0">
        Paniers sauvegardés
      </h3>
      <div v-if="showAlert" class="lg:w-5/6">
        <AlertSharedComponent />
      </div>
      <div
        class="mb-2.5 hidden items-center text-sm text-gray-500 md:flex lg:text-base"
      >
        <div class="w-5/12">Nom du panier</div>
        <div class="w-2/12">Créé le</div>
        <div class="w-3/12">Nombre d'articles</div>
        <!--        <div class="w-2/12"></div>-->
        <div class="w-2/12"></div>
      </div>
      <div
        v-if="isLoading"
        class="mt-5 flex h-20 w-full items-center justify-center"
      >
        <LoaderSharedComponent
          class="text-secondary"
          classes="loader-xl loader"
        />
      </div>
      <div v-else class="flex flex-row flex-wrap justify-between">
        <SavedCartComponent
          v-for="(savedCart, key) in savedCarts"
          :key="key"
          :saved-cart="savedCart"
          @cancel="showFormSavedCart = false"
          @submit="onSubmit"
          @delete="onDelete"
          @add-to-cart="onAddToCart"
        />
      </div>
    </template>
  </AccountPage>
</template>
<script lang="ts" setup>
import AccountPage from '@/vuejs/modules/account/pages/AccountPage.vue'
import { computed, onMounted, ref } from 'vue'
import SavedCartComponent from '@/vuejs/modules/account/components/SavedCartComponent.vue'
import { useAlertStore } from '@/vuejs/stores/alert'
import { storeToRefs } from 'pinia'
import { useSavedCartStore } from '@/vuejs/stores/savedCart'
import AlertSharedComponent from '@/vuejs/modules/shared/AlertSharedComponent.vue'
import { useCartStore } from '@/vuejs/stores/cart'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'

const cartStore = useCartStore()
const alertStore = useAlertStore()
const showFormSavedCart = ref<boolean>(false)
const deleteSavedCart = ref<boolean>(false)
const savedCartStore = useSavedCartStore()
const isLoading = ref<boolean>(false)
const { show: showAlert } = storeToRefs(alertStore)

onMounted(async () => {
  isLoading.value = true
  await savedCartStore.fetchSavedCarts()
  isLoading.value = false
})

const onSubmit = async (event) => {
  isLoading.value = true
  try {
    if (event.isEditing) {
      await savedCartStore.update(event.savedCart)
    }
    await savedCartStore.fetchSavedCarts()
    showFormSavedCart.value = false
  } catch (error) {}

  isLoading.value = false
}

const onDelete = async (event) => {
  isLoading.value = true
  try {
    await savedCartStore.delete(event.savedCartId)

    await savedCartStore.fetchSavedCarts()
    deleteSavedCart.value = false
  } catch (error) {}

  isLoading.value = false
}

const onAddToCart = async (event) => {
  isLoading.value = true
  try {
    const cartProducts = []
    const cart = await savedCartStore.findSavedCartById(event.savedCartId)
    for (const [, value] of Object.entries(cart.savedCartProducts)) {
      cartProducts.push({
        variantId: value.upplerVariantId,
        quantity: value.quantity,
      })
    }
    await cartStore.addProductsToCart(cartProducts)

    await savedCartStore.fetchSavedCarts()
    deleteSavedCart.value = false
  } catch (error) {}

  isLoading.value = false
}

const savedCarts = computed(() => {
  return savedCartStore.savedCarts
})
</script>

<style scoped></style>
