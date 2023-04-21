<template>
  <h3 class="mt-10 mb-2 text-title-35 text-primary">Adresses</h3>
  <div class="flex flex-col-reverse lg:grid lg:grid-cols-4 lg:gap-4 lg:px-0">
    <div class="col-span-3 flex flex-col lg:grid lg:grid-cols-2 lg:gap-2">
      <div>
        <h3 class="mb-5 text-[19px] text-primary md:text-[25px]">
          Adresse de facturation
        </h3>
        <div class="mt-5 items-center rounded-lg bg-white p-5 text-gray-500">
          {{
            formatAddress(selectedBillingAddress) ||
            'Aucune adresse sélectionnée'
          }}
          <p v-if="billingAddresses.length > 0" class="mt-5">
            <select
              v-if="!isLoading"
              v-model="selectedBillingAddressId"
              class="h-[35px] w-full rounded-md py-0 text-center text-gray-600 placeholder-gray-400"
              @change="selectAddress('billing')"
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
        <div class="mt-5 items-center rounded-lg bg-white p-5 text-gray-500">
          {{
            formatAddress(selectedShippingAddress) ||
            'Aucune adresse sélectionnée'
          }}
          <p v-if="shippingAddresses.length > 0" class="mt-5">
            <select
              v-if="!isLoading"
              v-model="selectedShippingAddressId"
              class="h-[35px] w-full rounded-md py-0 text-center text-gray-600 placeholder-gray-400"
              @change="selectAddress('shipping')"
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
    </div>
    <div>
      <CartRightSideComponent>
        <template #title>Récapitulatif</template>
        <template #button-next>
          <ButtonComponent
            @click="goToPayment"
            class="button button-gradient mt-3 w-full"
          >
            <ArrowRightIconComponent :stroke-color="'#FFFFFF'" />
            Passer au paiement
          </ButtonComponent>
          <!-- <div v-if="error" class="mt-2 text-center text-xs text-red-600">
            {{ error }}
          </div> -->
        </template>
      </CartRightSideComponent>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { computed, ref } from 'vue'
import { storeToRefs } from 'pinia'
import { useRouter } from 'vue-router'

import ArrowRightIconComponent from '@/vuejs/modules/shared/icon/ArrowRightIconComponent.vue'
import ButtonComponent from '@/vuejs/modules/shared/ButtonComponent.vue'
import CartRightSideComponent from '@/vuejs/modules/cart/components/CartRightSideComponent.vue'
import LoaderSharedComponent from '@/vuejs/modules/shared/LoaderSharedComponent.vue'

import { CartPageList } from '@/vuejs/router/pages-list'
import { formatAddress, notifyError } from '@/vuejs/services/utils'
import { useBuyerCompanyStore } from '@/vuejs/stores/buyer_company'
import { useCartStore } from '@/vuejs/stores/cart'
import { Address } from '@/vuejs/types/Address'

const router = useRouter()
const cartStore = useCartStore()
const companyStore = useBuyerCompanyStore()

const {
  defaultAddress,
  shippingAddresses: storeShippingAddresses,
  billingAddresses: storeBillingAddresses,
} = storeToRefs(companyStore)

const { cart } = storeToRefs(cartStore)

const allShippingAddresses = [
  ...storeShippingAddresses.value,
  defaultAddress.value,
]

const selectedShippingAddress = computed((): Address => {
  return allShippingAddresses.find(
    (a) => a.id === cart.value.shipping_address.id,
  )
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
  return allBillingAddresses.find((a) => a.id === cart.value.billing_address.id)
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

const goToPayment = (): void => {
  if (!selectedBillingAddress.value || !selectedShippingAddress.value) {
    notifyError(
      'Veuillez sélectionner une adresse de facturation et de livraison pour continuer.',
    )
    return
  }
  router.push({ name: CartPageList.PAYMENT })
}

const selectAddress = async (type: string): Promise<void> => {
  const data = {
    shippingAddressId:
      selectedShippingAddress.value?.id || cartStore.cart.shipping_address?.id,
    billingAddressId:
      selectedBillingAddress.value?.id || cartStore.cart.billing_address?.id,
  }
  if (type === 'shipping') {
    data.shippingAddressId = selectedShippingAddressId.value
  } else if (type === 'billing') {
    data.billingAddressId = selectedBillingAddressId.value
  }
  isLoading.value = true
  await cartStore.updateCartAddress({
    cartId: cartStore.cart.id,
    ...data,
  })
  selectedShippingAddressId.value = 0
  selectedBillingAddressId.value = 0
  isLoading.value = false
}
</script>

<style scoped></style>
