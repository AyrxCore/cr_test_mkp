<template>
  <h3 class="text-title-primary mb-2 mt-8">Adresses</h3>
  <div class="flex flex-col-reverse lg:grid lg:grid-cols-4 lg:gap-4 lg:px-0">
    <div class="col-span-3 flex flex-col lg:grid lg:grid-cols-2 lg:gap-2">
      <div>
        <h3 class="mt-5 text-[19px] text-primary md:text-[25px] lg:mt-0">
          Adresse de facturation
        </h3>
        <div class="mt-5 items-center rounded-lg bg-white p-5">
          {{
            formatAddress(displayedBillingAddress) ||
            'Aucune adresse sélectionnée'
          }}
          <p v-if="billingAddresses.length > 0" class="mt-5">
            <select
              v-if="!isLoading"
              v-model="selectedBillingAddressId"
              class="h-[35px] w-full rounded-md py-0 text-center placeholder-gray-400"
              @change="selectAddress(ADDRESS_BILLING)"
            >
              <option :value="''">Choisir une autre adresse</option>
              <option
                v-for="address in billingAddresses"
                :key="address.externalId"
                :value="address.externalId"
              >
                {{ formatAddress(address) }}
              </option>
            </select>
            <LoaderSharedComponent v-else />
          </p>
        </div>
      </div>
      <div>
        <h3
          class="my-5 text-[19px] text-primary md:mb-5 md:text-[25px] lg:my-0"
        >
          Adresse de livraison
        </h3>
        <div class="mt-5 items-center rounded-lg bg-white p-5">
          {{
            formatAddress(selectedShippingAddress) ||
            'Aucune adresse sélectionnée'
          }}
          <p v-if="shippingAddresses.length > 0" class="mt-5">
            <select
              v-if="!isLoading"
              v-model="selectedShippingAddressId"
              class="h-[35px] w-full rounded-md py-0 text-center placeholder-gray-400"
              @change="selectAddress(ADDRESS_SHIPPING)"
            >
              <option :value="''">Choisir une autre adresse</option>
              <option
                v-for="address in shippingAddresses"
                :key="address.externalId"
                :value="address.externalId"
              >
                {{ formatAddress(address) }}
              </option>
            </select>
            <LoaderSharedComponent v-else />
          </p>
        </div>
      </div>
      <div class="col-span-2 m-auto mt-4 lg:mt-0">
        <RouterLink
          :to="{ name: PageList.ADDRESSES }"
          class="button button-primary-outline"
        >
          Gérer mes adresses
        </RouterLink>
      </div>
    </div>
    <CartRightSideComponent :show-shipment-price="false" :show-payment-methods="true">
      <template #title>Récapitulatif panier</template>
      <template #button-next>
        <ButtonComponent
          :disabled="
            !selectedBillingAddress?.id || !selectedShippingAddress?.id
          "
          :is-loading="isLoading"
          class="button-primary mt-3 w-full"
          @click="goToShipments"
        >
          <ArrowRightIconComponent class="h-4 w-4" />
          Continuer
        </ButtonComponent>
      </template>
    </CartRightSideComponent>
  </div>
</template>

<script lang="ts" setup>
import { computed, onMounted, ref } from 'vue'
import { storeToRefs } from 'pinia'
import { useRouter } from 'vue-router'
import { useHead } from '@unhead/vue'

import { PageList } from '@/vuejs/router'
import { useAddressStore } from '@/vuejs/stores/address'
import { useCartStore } from '@/vuejs/stores/cart'
import { ADDRESS_BILLING, ADDRESS_SHIPPING } from '@/vuejs/services/const'
import { formatCartItemsGtmEvent, sendGtmEvent } from '@/vuejs/services/gtm'
import { formatAddress, notifyError } from '@/vuejs/services/utils'
import { Address } from '@/vuejs/types/Address'

import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import CartRightSideComponent from '@/vuejs/modules/cart/components/CartRightSideComponent.vue'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'
import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'

const router = useRouter()
const cartStore = useCartStore()
const addressStore = useAddressStore()

const {
  shippingAddresses: storeShippingAddresses,
  billingAddresses: storeBillingAddresses,
} = storeToRefs(addressStore)

const { cart } = storeToRefs(cartStore)

const allShippingAddresses = computed((): Address[] => storeShippingAddresses.value)

const selectedShippingAddress = computed((): Address | null => {
  return allShippingAddresses.value.find(
    (a) => a.externalId === cart.value.shippingAddressExternalId,
  ) ?? null
})

const shippingAddresses = computed((): Address[] => {
  if (!selectedShippingAddress.value) return allShippingAddresses.value
  return allShippingAddresses.value.filter(
    (a) => a.externalId !== selectedShippingAddress.value!.externalId,
  )
})

const allBillingAddresses = computed((): Address[] => storeBillingAddresses.value)

const selectedBillingAddress = computed((): Address | null => {
  return allBillingAddresses.value.find(
    (a) => a.externalId === cart.value.billingAddressExternalId,
  ) ?? null
})
const billingAddresses = computed((): Address[] => {
  if (!selectedBillingAddress.value) return allBillingAddresses.value
  return allBillingAddresses.value.filter(
    (a) => a.externalId !== selectedBillingAddress.value!.externalId,
  )
})

const selectedShippingAddressId = ref<string>('')
const selectedBillingAddressId = ref<string>('')
const isLoading = ref<boolean>(false)
// Adresse affichée manuellement : mise à jour uniquement après la fin de toutes les requêtes
const displayedBillingAddress = ref<Address | null>(null)

const goToShipments = async (): Promise<void> => {
  if (!selectedBillingAddress.value || !selectedShippingAddress.value) {
    notifyError(
      'Veuillez sélectionner une adresse de facturation et de livraison pour continuer.',
    )
    return
  }
  if (
    !cartStore.cart.shippingAddressExternalId ||
    !cartStore.cart.billingAddressExternalId
  ) {
    isLoading.value = true
    await selectAddress('all')
    isLoading.value = false
  }
  router.push({ name: PageList.CART_SHIPMENTS })
}

const selectAddress = async (type: string): Promise<void> => {
  const data = {
    shippingAddressExternalId:
      selectedShippingAddress.value?.externalId ||
      cartStore.cart.shippingAddressExternalId,
    billingAddressExternalId:
      selectedBillingAddress.value?.externalId ||
      cartStore.cart.billingAddressExternalId,
  }
  if (type === ADDRESS_SHIPPING) {
    data.shippingAddressExternalId = selectedShippingAddressId.value
  } else if (type === ADDRESS_BILLING) {
    data.billingAddressExternalId = selectedBillingAddressId.value
  }

  const isBillingUpdate = type === ADDRESS_BILLING || type === 'all'

  isLoading.value = true
  await cartStore.updateCartAddress({ cartId: cartStore.cart.id, ...data })
  selectedShippingAddressId.value = ''
  selectedBillingAddressId.value = ''
  isLoading.value = false

  if (isBillingUpdate) {
    await cartStore.fetchAdyenPaymentMethods()
    // Mise à jour simultanée de l'adresse affichée et du picto (CBPaymentMethod vient d'être chargé)
    displayedBillingAddress.value = selectedBillingAddress.value
  }
}

useHead({
  title: 'Adresses | QANTIS Marketplace',
  meta: [{ property: 'og:title', content: 'Adresses | QANTIS Marketplace' }],
})

onMounted(async () => {
  sendGtmEvent('begin_checkout', {
    ecommerce: {
      currency: 'EUR',
      items: formatCartItemsGtmEvent(cart.value),
    },
  })

  // Initialiser l'adresse affichée avec la valeur courante du store
  displayedBillingAddress.value = selectedBillingAddress.value

  // Réinitialiser les moyens de paiement pour ne les afficher que sur la base de la requête de cette page
  cartStore.resetPaymentMethods()

  // Si une adresse de facturation est déjà associée au panier, charger les moyens de paiement
  if (cartStore.cart?.billingAddressExternalId) {
    await cartStore.fetchAdyenPaymentMethods()
  }
})
</script>
