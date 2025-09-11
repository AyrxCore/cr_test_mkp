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
            formatAddress(selectedBillingAddress) ||
            'Aucune adresse sélectionnée'
          }}
          <p v-if="billingAddresses.length > 0" class="mt-5">
            <select
              v-if="!isLoading"
              v-model="selectedBillingAddressId"
              class="h-[35px] w-full rounded-md py-0 text-center placeholder-gray-400"
              @change="selectAddress(ADDRESS_BILLING)"
            >
              <option :value="0">Choisir une autre adresse</option>
              <option
                v-for="address in billingAddresses"
                :key="address.id"
                :value="address.id"
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
              <option :value="0">Choisir une autre adresse</option>
              <option
                v-for="address in shippingAddresses"
                :key="address.id"
                :value="address.id"
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
    <CartRightSideComponent :show-shipment-price="false">
      <template #title>Récapitulatif panier</template>
      <template #button-next>
        <ButtonComponent
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
  defaultAddress,
  shippingAddresses: storeShippingAddresses,
  billingAddresses: storeBillingAddresses,
  defaultBillingAddress,
  defaultShippingAddress,
} = storeToRefs(addressStore)

const { cart } = storeToRefs(cartStore)

const allShippingAddresses = [
  ...storeShippingAddresses.value,
  defaultAddress.value,
]

const selectedShippingAddress = computed((): Address => {
  const selected = allShippingAddresses.find(
    (a) => a.id === cart.value.shipping_address?.id,
  )
  return selected || defaultShippingAddress.value
})

const shippingAddresses = computed((): Address[] => {
  if (!selectedShippingAddress.value) return allShippingAddresses
  return allShippingAddresses.filter(
    (a) => a.id !== selectedShippingAddress.value.id,
  )
})

const allBillingAddresses = [
  ...storeBillingAddresses.value,
  defaultAddress.value,
]

const selectedBillingAddress = computed((): Address => {
  const selected = allBillingAddresses.find(
    (a) => a.id === cart.value.billing_address?.id,
  )
  return selected || defaultBillingAddress.value
})
const billingAddresses = computed((): Address[] => {
  if (!selectedBillingAddress.value) return allBillingAddresses
  return allBillingAddresses.filter(
    (a) => a.id !== selectedBillingAddress.value.id,
  )
})

const selectedShippingAddressId = ref<number>(0)
const selectedBillingAddressId = ref<number>(0)
const isLoading = ref<boolean>(false)

const goToShipments = async (): Promise<void> => {
  if (!selectedBillingAddress.value || !selectedShippingAddress.value) {
    notifyError(
      'Veuillez sélectionner une adresse de facturation et de livraison pour continuer.',
    )
    return
  }
  if (
    !cartStore.cart.shipping_address?.id ||
    !cartStore.cart.billing_address?.id
  ) {
    isLoading.value = true
    await selectAddress('all')
    isLoading.value = false
  }
  router.push({ name: PageList.CART_SHIPMENTS })
}

const selectAddress = async (type: string): Promise<void> => {
  const data = {
    shippingAddressId:
      selectedShippingAddress.value?.id || cartStore.cart.shipping_address?.id,
    billingAddressId:
      selectedBillingAddress.value?.id || cartStore.cart.billing_address?.id,
  }
  if (type === ADDRESS_SHIPPING) {
    data.shippingAddressId = selectedShippingAddressId.value
  } else if (type === ADDRESS_BILLING) {
    data.billingAddressId = selectedBillingAddressId.value
  }
  isLoading.value = true
  await cartStore.updateCartAddress({ cartId: cartStore.cart.id, ...data })
  selectedShippingAddressId.value = 0
  selectedBillingAddressId.value = 0
  isLoading.value = false
}

useHead({
  title: 'Adresses | QANTIS Marketplace',
  meta: [{ property: 'og:title', content: 'Adresses | QANTIS Marketplace' }],
})

onMounted(() => {
  sendGtmEvent('begin_checkout', {
    ecommerce: {
      currency: 'EUR',
      items: formatCartItemsGtmEvent(cart.value),
    },
  })
})
</script>
